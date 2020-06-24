<?php


namespace infra\dependencies;

use infra\exceptions\HttpRequestFailed;
use infra\librarys\proxy\Http;
use infra\librarys\utils\Functions;

/**
 * Class RefLabelDependency
 * @package infra\dependencies
 */
class RefLabelDependency
{
    /**
     * @var Http
     */
    private $client;

    private $baseUrl;
    private $domain;
    private $headers;

    private $urlQuery = '/api/rest?ctl=refLabel&act=query';

    /**
     * RefLabelDependency constructor.
     * @param Http $http http
     */
    public function __construct(Http $http)
    {
        $this->baseUrl = config('dependency.ref.baseUrl');
        $this->domain = config('dependency.ref.domain');
        $this->headers = config('dependency.ref.headers');

        $headers = ['Host' => $this->domain];
        if ($this->headers) {
            $items = explode(';', $this->headers);
            foreach ($items as $item) {
                $pair = explode(':', $item);
                if (count($pair) < 2) {
                    continue;
                }
                $headers[trim($pair[0])] = trim($pair[1]);
            }
        }
        $this->client = $http;
        $this->client->withOptions([
            'headers' => $headers,
            'verify'  => false,
            'expect' => false,
        ]);
    }

    /**
     * 允许上报的字段
     * @var array
     */
    private $refLabelFilter = [
        2 => 1,
        8 => 1,
    ];

    /**
     * 查询任务
     * @param array  $data data
     * @param string $refLabelUnique 唯一来源标识：格式为 refLabel_taskId
     * @return bool
     * @throws HttpRequestFailed
     */
    public function query(array $data, string $refLabelUnique = '')
    {
        if (empty($refLabelUnique) || !$this->refLabelFilter($refLabelUnique)) {
            return false;
        }
        $params = [
            'timestamp' => time(),
            'body'      => [
                'businessData' => ['refLabelUnique' => $refLabelUnique],
                'commonRequest' => $data['commonRequest'] ?? $data['body']['commonRequest'] ?? [],
            ],
        ];
        $url = $this->baseUrl . $this->urlQuery;
        $response = $this->client->json()->post($url, $params, 2, 2);
        if ($response->getStatusCode() != 200) {
            return false;
        }
        $json = json_decode($response->getBody()->getContents(), true);
        Functions::log(__METHOD__, ['request' => $params, 'response' => $json]);
        if (empty($json['response']['code']) || $json['response']['code'] != 1) {
            return false;
        }
        if (!empty($json['response']['data']['prizeCnt']) && $json['response']['data']['prizeCnt'] > 0) {
            return true;
        }
        return false;
    }

    /**
     * 过滤不需要要上报的数据
     * @param string $reflabel 来源
     * @return bool
     */
    public function refLabelFilter(string $reflabel): bool
    {
        if ($reflabel) {
            $first = substr($reflabel, 0, 1);
            return isset($this->refLabelFilter[$first]);
        }
        return false;
    }
}
