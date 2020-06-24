<?php

namespace App\Console\Commands\web;

use Throwable;
use Exception;
use AMQPEnvelope;
use infra\librarys\utils\Functions;
use infra\librarys\utils\AppConst;
use App\Console\Commands\LockTrait;
use App\Console\Commands\BaseCommand;
use Illuminate\Support\Facades\Log;
use infra\dependencies\AmqpDependencies;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * 日志消费的处理类
 * @author yangd
 */
class ConsumeRecordCommand extends BaseCommand
{
    /**
     * 命令行执行命令
     * @var string
     */
    protected $signature = 'consume:record';

    /**
     * 命令描述
     * @var string
     */
    protected $description = '常驻进程，用于处理日志消费';

    /** @var AmqpDependencies */
    private $mqInstance;
    /**
     * @var CoinsService
     */
    private $coinService;

    /**
     * @var PowerChangeRecord
     */
    private $powerRecord;
    /**
     * @var UserRecord
     */
    private $userRecord;
    /**
     * @var OrderRecord
     */
    private $orderRecord;

    private $lockKey = AppConst::KEY_CONSUME_RECORD;

    /**
     * ConsumeRecordCommand constructor.
     * @param CoinsService $coinService coinService
     * @param PowerChangeRecord $powerRecord powerRecord
     * @param UserRecord $userRecord userRecord
     * @param OrderRecord $orderRecord orderRecord
     */
    public function __construct(
        CoinsService $coinService,
        PowerChangeRecord $powerRecord,
        UserRecord $userRecord,
        OrderRecord $orderRecord
    ) {
        parent::__construct();
        $this->coinService = $coinService;
        $this->powerRecord = $powerRecord;
        $this->userRecord = $userRecord;
        $this->orderRecord = $orderRecord;
    }



    /**
     * Execute the console command.
     * @param InputInterface  $input input
     * @param OutputInterface $output output
     * @return mixed
     */
    protected function handle(InputInterface $input, OutputInterface $output) : int
    {
        if (!$this->before($this->lockKey)) {
            return 0;
        }

        try {
            $this->mqInstance = AmqpDependencies::getInstance(AppConst::PROVERB_MQ_LABEL);
        } catch (Exception $e) {
            $this->error($e->getMessage());
            return -1;
        }

        try {
            loop:
            $this->canSyncAgain() && $this->sync($this->lockKey);

            $envelope = $this->mqInstance->getMsg(AppConst::PROVERB_MQ_RECORD_QUEUE);
            if (!$envelope) {
                sleep(5);
                goto loop;
            }
            // 初始化dealer，根据日志的router key获取指定的dealer，然后deal日志，提供了ack回调机制
            if ($envelope->getDeliveryTag()) {
                Functions::isDev() && $this->info(sprintf("收到消息 %d", $envelope->getDeliveryTag()));
            }
            $routerKey = $envelope->getRoutingKey();
            Functions::isDev() && $this->info($routerKey);
            $this->deal($envelope, $routerKey);
            goto loop;
        } catch (Exception $e) {
            Log::stack(['mqerror'])->error("异步消费脚本出现异常退出：" . $e->getMessage());
            return -1;
        }
        return 0;
    }

    /**
     * @param AMQPEnvelope $envelope envelope
     * @param string $routerKey routerKey
     * @return mixed
     */
    private function deal(AMQPEnvelope $envelope, $routerKey)
    {
        //todo
    }
}
