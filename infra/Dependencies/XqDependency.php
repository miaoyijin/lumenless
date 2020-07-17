<?php

namespace infra\dependencies;

use Exception;
use infra\librarys\proxy\Http;
use infra\contracts\XqInterface;
use infra\exceptions\HttpRequestFailed;

/**
 * Class XqDependency
 * @package dependency
 */
class XqDependency implements XqInterface
{
    /**
     * @var string baseUrl
     */
    private $baseUrl;
    /**
     * @var string domain
     */
    private $domain;
    /**
     * @var string 签名key
     */
    private $signKey;
    /**
     * @var string 项目标识
     */
    private $project;
    /**
     * @var Http
     */
    private $client;

    private $urlGetInfo = '/userInfo/apiGetInfo';
    private $urlAddCoin = '/taskFinish/apiVisit';
    private $urlCheck = '/accountInfo/apiGetInfo';

    /**
     * XqDependency constructor.
     * @param Http $http http请求类
     */
    public function __construct(Http $http)
    {
        $this->client = $http;

        $this->baseUrl = config('dependency.xq.baseUrl');
        $this->domain = config('dependency.xq.domain');
        $this->signKey = config('dependency.xq.signKey');
        $this->project = config('dependency.xq.project');

        $this->client = $http;
        $this->client->withOptions([
            'headers' => [
                'Host' => $this->domain,
                'XQ-HEADER-INFO' => 'zyxqv2'
            ],
            'verify' => false,
            'expect' => false,
        ]);
    }

    /**
     * 获取用户信息
     * @param int|string $passId passId
     * @return array
     * @throws HttpRequestFailed
     */
    public function getGoldSum($passId = '')
    {
        $params = ['passid' => $passId ?? ''];
        $params = $this->makeSign($params);
        $url = $this->buildUrl($this->urlGetInfo);

        $response = $this->client->post($url, $params, 2, 2);
        $data = [];
        if ($response->getStatusCode() == 200) {
            $json = json_decode($response->getBody(), true);
            $json && $data = $json['data'] ?? [];
        }
        empty($data['uid']) && $data['uid'] = $passId;
        empty($data['passid']) && $data['passid'] = $passId;
        empty($data['remain_gold']) && $data['remain_gold'] = 1;
        return $data;
    }

    /**
     * 给用户发金币
     * @param array $data data
     * @return array
     * @throws HttpRequestFailed
     */
    public function addCoin(array $data)
    {
        $params = ['data' => json_encode($data)];
        $params = $this->makeSign($params);
        $url = $this->buildUrl($this->urlAddCoin);
        $response = $this->client->post($url, $params, 2, 2);
        if ($response->getStatusCode() != 200) {
            return [];
        }
        $json = json_decode($response->getBody(), true);
        return $json['data'] ?? [];
    }

    /**
     * 对总账
     * @param array $data data
     * @return mixed
     * @throws Exception
     */
    public function checkTotalAccount(array $data)
    {
        $params = $this->makeSign($data);
        $url = $this->buildUrl($this->urlCheck);

        $response = $this->client->post($url, $params, 3, 3);
        if ($response->getStatusCode() != 200) {
            throw new Exception("请求对账接口异常; {$response->getReasonPhrase()}");
        }
        $json = json_decode($response->getBody(), true);
        $data = $json ? $json : [];
        if (empty($data['code']) || $data['code'] != 200) {
            $message = sprintf("请求对账接口异常; %s", $data['msg'] ?? '');
            throw new Exception($message);
        }
        return $data['account_res'] ?? 1;
    }

    /**
     * 生成token
     * @param array $data data
     * @return array
     */
    protected function makeSign(array $data): array
    {
        $data["project"] = $this->project;
        $data["timestamp"] = time();
        ksort($data);
        $string = http_build_query($data);
        $data["token"] = strtoupper(md5($string . "&key={$this->signKey}"));
        return $data;
    }

    /**
     * @param string $url url
     * @return string
     */
    private function buildUrl(string $url)
    {
        return sprintf("%s%s?project=%s", $this->baseUrl, $url, $this->project);
    }
}
