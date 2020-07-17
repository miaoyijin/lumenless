<?php

namespace infra\dependencies;

use AMQPEnvelope;
use AMQPQueue;
use \Exception;

/**
 * rabbitmq的依赖
 * @author yangd
 */
class AmqpDependencies
{
    private static $arrInstance = []; // 连接单例的数组

    private $objConn = null; // 连接
    private $objChannel = null; // 通道
    private $arrExchange = []; // 交换机数组，默认只有一个，名叫ydlm
    private $arrQueue = []; // 队列数组

    /**
     * Amqp constructor.
     *
     * @param array $arrConfig $arrConfig
     *
     * @throws Exception
     */
    private function __construct(array $arrConfig = [])
    {
        if (class_exists('AMQPConnection')) {
            $this->objConn = new \AMQPConnection($arrConfig);
            if ($this->objConn->connect()) {
                $this->objChannel = new \AMQPChannel($this->objConn);
                if (!$this->objChannel->isConnected()) {
                    throw new Exception('can not connect to amqp channel');
                }
            } else {
                throw new Exception('can not connect to amqp server');
            }
        } else {
            throw new Exception('class "AMQPConnection" not exists');
        }
    }

    /**
     * @param string $source $source
     *
     * @return mixed
     *
     * @throws Exception
     */
    public static function getInstance($source)
    {
        $arrConfig = config('queue.amqp')[$source];
        $strKey = ($arrConfig ? md5(json_encode($arrConfig)) : 'default');
        if (!isset(self::$arrInstance[$strKey])) {
            self::$arrInstance[$strKey] = new self($arrConfig);
        }
        return self::$arrInstance[$strKey];
    }

    /**
     * 关闭连接
     * @return bool
     */
    public function close()
    {
        if ($this->objChannel && $this->objChannel->isConnected()) {
            $this->objChannel->close();
        }
        if ($this->objConn && $this->objConn->isConnected()) {
            $this->objConn->disconnect();
        }
        return true;
    }

    /**
     * 删除单例实例
     * @param string $source $source
     * @return void
     */
    public static function delInstance($source)
    {
        $arrConfig = config('queue.amqp')[$source];
        $strKey = ($arrConfig ? md5(json_encode($arrConfig)) : 'default');
        if (self::$arrInstance[$strKey]) {
            self::$arrInstance[$strKey] = null;
        }
    }

    /**
     * 设置Exch
     * @param string $strExchangeName 交换器名称
     * @param string $strType AMQP_EX_TYPE_DIRECT（直接投递）、AMQP_EX_TYPE_FANOUT、AMQP_EX_TYPE_HEADERS、AMQP_EX_TYPE_TOPIC
     * @param int $intFlag AMQP_NOPARAM、AMQP_DURABLE（持久化）、AMQP_PASSIVE、AMQP_AUTODELETE，可以多项并用
     * @return boolean
     * @throws AMQPConnectionException
     * @throws AMQPExchangeException
     */
    private function setExchange(
        string $strExchangeName = 'chy',
        string $strType = AMQP_EX_TYPE_DIRECT,
        int $intFlag = AMQP_DURABLE
    ) {
        $blnRe = false;
        if ($this->objChannel) {
            if (!isset($this->arrExchange[$strExchangeName])) {
                $this->arrExchange[$strExchangeName] = new \AMQPExchange($this->objChannel);
                $this->arrExchange[$strExchangeName]->setName($strExchangeName);
                $this->arrExchange[$strExchangeName]->setType($strType);
                $this->arrExchange[$strExchangeName]->setFlags($intFlag);
                $this->arrExchange[$strExchangeName]->declareExchange();
            }
            $blnRe = true;
        }
        return $blnRe;
    }

    /**
     * 设置队列，并绑定交换机
     * @param string $strQueueName 队列名
     * @param string $strBindRouteKey 为空时，与队列名一致
     * @param string $strExchangeName 交换器名称，默认ydlm
     * @param string $strUnbindRouteKey 解除绑定的Routekey
     * @param int $intFlag AMQP_DURABLE（持久化）、AMQP_PASSIVE、AMQP_EXCLUSIVE、AMQP_AUTODELETE，可以多项并用
     * @return boolean
     *
     * @throws AMQPConnectionException
     * @throws AMQPQueueException
     */
    private function setQueue(
        string $strQueueName,
        string $strBindRouteKey = '',
        string $strExchangeName = 'chy',
        string $strUnbindRouteKey = '',
        int $intFlag = AMQP_DURABLE
    ) {
        $blnRe = false;
        if ($this->objChannel) {
            if (!isset($this->arrQueue[$strQueueName])) {
                $this->arrQueue[$strQueueName] = new AMQPQueue($this->objChannel);
                $this->arrQueue[$strQueueName]->setName($strQueueName);
                $this->arrQueue[$strQueueName]->setFlags($intFlag);
                $this->arrQueue[$strQueueName]->declareQueue();
                strlen($strBindRouteKey) || $strBindRouteKey = $strQueueName;
                $this->arrQueue[$strQueueName]->bind($strExchangeName, $strBindRouteKey);
            }
            strlen($strUnbindRouteKey) && $this->arrQueue[$strQueueName]->unbind($strExchangeName, $strUnbindRouteKey);
            $blnRe = true;
        }
        return $blnRe;
    }

    /**
     * 功    能: 取队列中未处理的消息个数
     * @param string $strQueueName $strQueueName
     * @param int $intFlag $intFlag
     * @return false or int
     * @throws AMQPConnectionException
     * @throws AMQPQueueException
     */
    public function getQueueLen(string $strQueueName, int $intFlag = AMQP_DURABLE)
    {
        $intRe = false;
        if ($this->objChannel) {
            if (!isset($this->arrQueue[$strQueueName])) {
                $this->arrQueue[$strQueueName] = new AMQPQueue($this->objChannel);
                $this->arrQueue[$strQueueName]->setName($strQueueName);
                $this->arrQueue[$strQueueName]->setFlags($intFlag);
            }
            $intRe = $this->arrQueue[$strQueueName]->declareQueue();
        }
        return $intRe;
    }

    /**
     * 生产者发布一条消息
     * @param string $strMsg 消息体
     * @param string $strQueueName $strQueueName
     * @param string $strRouteKey 交换器投递的路由
     * @param string $strExchangeName 交换器，默认是ydlm
     * @param array $arrAttr 'delivery_mode' => 2 表示消息持久化
     * @param int $intFlag AMQP_NOPARAM、AMQP_MANDATORY、AMQP_IMMEDIATE，可以多项并用
     * @return bool 投递是否成功
     *
     * @throws AMQPConnectionException
     * @throws AMQPExchangeException
     * @throws AMQPQueueException
     */
    public function putMsg(
        string $strMsg,
        string $strQueueName,
        string $strRouteKey,
        string $strExchangeName = 'chy',
        array $arrAttr = [],
        int $intFlag = AMQP_NOPARAM
    ) {
        $blnRe = false;
        if (strlen($strMsg)) {
            if ($this->setExchange($strExchangeName)
                && $this->setQueue($strQueueName, $strRouteKey, $strExchangeName)) {
                $arrAttr || $arrAttr = ['delivery_mode' => 2, 'content_type' => 'application/json'];
                $blnRe = $this->arrExchange[$strExchangeName]->publish($strMsg, $strRouteKey, $intFlag, $arrAttr);
            }
        }
        return $blnRe;
    }

    /**
     * 从队列中获取一条消息
     * @param string $strQueueName $strQueueName
     * @param string $strRouteKey $strRouteKey
     * @param string $strExchangeName $strExchangeName
     * @return AMQPEnvelope $arrRe
     * @see AMQPQueue::get()
     */
    public function getMsg(
        string $strQueueName,
        string $strRouteKey = '',
        string $strExchangeName = 'chy'
    ) {
        if ($this->setQueue($strQueueName, $strRouteKey, $strExchangeName)) {
            // 不要传参AMQP_AUTOACK
            return $this->arrQueue[$strQueueName]->get(AMQP_NOPARAM);
        }
        return null;
    }

    /**
     * 确认消息已处理
     * @param string $strQueueName $strQueueName
     * @param int $intMsgId $intMsgId
     * @param string $strRouteKey $strRouteKey
     * @param string $strExchangeName $strExchangeName
     * @param int  $intFlag AMQP_NOPARAM，AMQP_MULTIPLE
     * @see AMQPQueue::ack()
     *
     * @return bool $blnRe
     *
     * @throws AMQPConnectionException
     * @throws AMQPQueueException
     */
    public function ackMsg(
        string $strQueueName,
        int $intMsgId,
        string $strRouteKey = '',
        string $strExchangeName = 'chy',
        $intFlag = AMQP_NOPARAM
    ) {
        $blnRe = false;
        if ($this->setQueue($strQueueName, $strRouteKey, $strExchangeName)) {
            $blnRe = $this->arrQueue[$strQueueName]->ack($intMsgId, $intFlag);
        }
        return $blnRe;
    }

    /**
     * @param string $strQueueName $strQueueName
     * @param int $intMsgId $intMsgId
     * @param string $strRouteKey  $strRouteKey
     * @param string $strExchangeName $strExchangeName
     * @param int $intFlag $intFlag
     * @return bool
     */
    public function reject(
        string $strQueueName,
        int $intMsgId,
        string $strRouteKey = '',
        string $strExchangeName = 'chy',
        $intFlag = AMQP_REQUEUE
    ) {
        $blnRe = false;
        if ($this->setQueue($strQueueName, $strRouteKey, $strExchangeName)) {
            $blnRe = $this->arrQueue[$strQueueName]->reject($intMsgId, $intFlag);
        }
        return $blnRe;
    }

    /**
     * 回调方式消费队列
     * @param string $strQueueName $strQueueName
     * @param callable $callable 回调
     * @param string $strRouteKey $strRouteKey
     * @param string $strExchangeName  $strExchangeName
     * @param int $intFlag $intFlag
     * @see AMQPQueue::consume()
     *
     * @return void
     *
     * @throws AMQPConnectionException
     * @throws AMQPQueueException
     */
    public function consumeMsg(
        string $strQueueName,
        callable $callable,
        string $strRouteKey = '',
        string $strExchangeName = 'chy',
        $intFlag = AMQP_NOPARAM
    ) {
        if ($this->setQueue($strQueueName, $strRouteKey, $strExchangeName)) {
            $this->arrQueue[$strQueueName]->consume($callable, $intFlag);
        }
    }
}
