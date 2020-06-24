<?php

namespace infra\librarys\proxy;

use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Support\Facades\Log;
use infra\exceptions\HttpRequestFailed;
use Psr\Http\Message\ResponseInterface;
use Symfony\Component\ErrorHandler\Exception\FlattenException;

/**
 * Class Http
 * @package infra\proxy
 *
 * 对 guzzle 的封装，主要目的是统一处理日志和debug信息，对业务开发更加友好
 *
 * @see https://guzzle-cn.readthedocs.io/zh_CN/latest
 */
class Http
{
    protected $connectTimeout = 10;//连接超时时间，单位秒
    protected $requestTimeout = 30;//默认超时时间，单位秒
    protected $retryInterval = 10000;//失败重试间隔时间，单位微秒，10毫秒

    /**
     * @var Client
     */
    private $client;

    private $withJson = false;
    private $withMulti = false;
    private $options = [];

    //打开 debug 模式之后，fd 将会被设置，用来记录 debug 信息
    private $debug = false;
    private $fd;

    public function __construct()
    {
        if (config("app.debug")) {
            $this->debug();
        }
    }

    /**
     * @param array $options
     * @param bool $reNew
     * @return Client
     */
    protected function getClient(array $options = [], bool $reNew = false)
    {
        if (empty($this->client) || $reNew) {
            $this->debug && $options['debug'] = $this->buildDebugStream();
            $this->client = new Client(array_merge([
                'timeout' => $this->requestTimeout,
                'connect_timeout' => $this->connectTimeout,
                'http_errors' => false,
            ], $options));
        }
        return $this->client;
    }

    private function buildDebugStream()
    {
        if (!$this->fd) {
            $this->fd = fopen("php://temp", 'r+');
        }
        return $this->fd;
    }

    public function getDebugInfo()
    {
        if ($this->debug===false || !$this->fd) {
            return '';
        }
        rewind($this->fd);
        $info = stream_get_contents($this->fd);
        ftruncate($this->fd, 0);
        return $info;
    }

    public function debug(bool $debug = true)
    {
        $this->debug = $debug;
        return $this;
    }

    public function json(bool $with = true)
    {
        $this->withJson = $with;
        return $this;
    }

    public function multi(bool $with = true)
    {
        $this->withMulti = $with;
        return $this;
    }

    /**
     * 附加其它请求参数
     * 如 expect 控制"Expect: 100-Continue"报文头的行为
     * @see https://guzzle-cn.readthedocs.io/zh_CN/latest/request-options.html
     * @param array $options
     * @return $this
     */
    public function withOptions(array $options)
    {
        $this->options = $options;
        return $this;
    }

    public function addOptions(array $options)
    {
        $this->options = array_merge($this->options, $options);
        return $this;
    }

    /**
     * @param $url
     * @param $params
     * @param int $times 失败后最大重试次数
     * @param int $timeout 等待超时时间
     * @return ResponseInterface
     * @throws HttpRequestFailed
     */
    public function post($url, $params, $times = 1, $timeout = 0)
    {
        return $this->request('post', $url, $params, $times, $timeout);
    }

    /**
     * @param $url
     * @param $params
     * @param int $times 失败后最大重试次数
     * @param int $timeout 等待超时时间
     * @return ResponseInterface
     * @throws HttpRequestFailed
     */
    public function get($url, $params = [], $times = 1, $timeout = 0)
    {
        return $this->request('get', $url, $params, $times, $timeout);
    }

    /**
     * @param $url
     * @param array $params
     * @param int $times
     * @param int $timeout
     * @return ResponseInterface
     * @throws HttpRequestFailed
     */
    public function put($url, $params = [], $times = 1, $timeout = 0)
    {
        return $this->request('put', $url, $params, $times, $timeout);
    }

    /**
     * @param $type
     * @param $url
     * @param array $params
     * @param int $times
     * @param int $timeout
     * @return ResponseInterface
     * @throws HttpRequestFailed
     */
    private function request($type, $url, $params = [], $times = 1, $timeout = 0)
    {
        if ($type == 'post' || $type == 'put') {
            if ($this->withJson) {
                $options['json'] = $params;
            } elseif ($this->withMulti) {
                $options['multipart'] = $params;
            } else {
                $options['form_params'] = $params;
            }
        } else {
            $options['query'] = $params;
        }
        $timeout && $options['timeout'] = $timeout;
        $this->options && $options = array_merge($options, $this->options);

        $client = $this->getClient();
        $remain = $times;
        $exception = null;
        while ($remain > 0) {
            $timeStart = microtime(true);
            $exception = null;
            try {
                switch ($type) {
                    case 'post':
                        $response = $client->post($url, $options);
                        break;
                    case 'put':
                        $response = $client->put($url, $options);
                        break;
                    case 'get':
                    default:
                        $response = $client->get($url, $options);
                }

                $this->logAfterResponse($response, $type, $url, $timeStart, $remain, $times, $options);
                return $response;
            } catch (ClientException $exception) {
                $response = $exception->getResponse();

                $this->logAfterResponse($response, $type, $url, $timeStart, $remain, $times, $options);
                return $response;
            } catch (Exception $exception) {
                $message = $this->buildMessage(0, $type, $url, $timeStart, $remain, $times);
                $data = [
                    'url' => $url,
                    'method' => $type,
                    'params' => $params,
                    'exception' => FlattenException::create($exception)->getAsString(),
                    'debug' => $this->getDebugInfo()
                ];
                $error = "Error2: {$exception->getMessage()}; {$message}";
                Log::stack(['dependency'])->error($error, $data);
            }
            usleep($this->retryInterval);
            $remain--;
        }

        //已经尝试 n 次，依然失败
        $message = "已经尝试 {$times} 次，{$url} 接口调用仍然失败; should-alert, {$exception->getMessage()}";
        Log::stack(['dependency'])->error($message, [
            'url' => $url,
            'method' => $type,
            'params' => $params,
            'debug' => $this->getDebugInfo()
        ]);

        throw new HttpRequestFailed($message);
    }

    /**
     * @param string $code
     * @param string $type
     * @param string $url
     * @param float $timeStart
     * @param int $remain
     * @param int $times
     * @return string
     */
    private function buildMessage($code, $type, $url, $timeStart, $remain, $times)
    {
        $items = parse_url($url);
        $host = $items['host'] ?? 'null';
        $path = $items['path'] ?? 'null';
        $index = $times - $remain + 1;
        $type = strtoupper($type);
        $timeUsage = microtime(true) - $timeStart;
        return sprintf("%.2fms, [%s] %s %s/%s, %d/%d", $timeUsage * 1000, $code, $type, $host, $path, $index, $times);
    }

    /**
     * @param ResponseInterface|null $response
     * @param string $type
     * @param string $url
     * @param float $timeStart
     * @param int $remain
     * @param int $times
     * @param array $params
     */
    private function logAfterResponse(?ResponseInterface $response, $type, $url, $timeStart, $remain, $times, $params)
    {
        $responseParam = $response ? [
            'code' => $response->getStatusCode(),
            'phrase' => $response->getReasonPhrase(),
            'body' => $response->getBody()->read(1024),
            'headers' => $response->getHeaders()
        ] : [];
        $response->getBody()->rewind();

        $code = $response->getStatusCode();
        $message = $this->buildMessage($code, $type, $url, $timeStart, $remain, $times);
        $context = [
            'url' => $url,
            'params' => $params,
            'response' => $responseParam,
            'debug' => $this->getDebugInfo()
        ];
        if ($code == 200) {
            Log::stack(['dependency'])->info($message, $context);
        } else {
            Log::stack(['dependency'])->warning($message, $context);
        }
    }

    public function __destruct()
    {
        if (!$this->fd) {
            return ;
        }
        fclose($this->fd);
    }

}
