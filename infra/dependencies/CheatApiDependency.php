<?php

namespace infra\dependencies;

use infra\contracts\CheatApiInterface;
use infra\exceptions\HttpRequestFailed;
use infra\librarys\proxy\Http;

/**
 * Class CheatApiDependency
 * @package dependency
 */
class CheatApiDependency implements CheatApiInterface
{
    /**
     * @var  string baseUrl
     */
    private $baseUrl;
    /**
     * @var string domain
     */
    private $domain;
    /**
     * @var Http
     */
    private $client;

    private $urlRealTimeCheat = '/cheat_api/realtime_cheat';

    /**
     * 反作弊依赖
     * XqDependency constructor.
     * @param Http $http http请求类
     */
    public function __construct(Http $http)
    {
        $this->baseUrl = config('dependency.cheat.baseUrl');
        $this->domain = config('dependency.cheat.domain');

        $this->client = $http;
        $this->client->withOptions([
            'headers' => [
                'Host' => $this->domain
            ],
            'verify' => false,
            'expect' => false,
        ]);
    }

    /**
     * 获取用户信息
     * @param array $cheatData cheatData
     * @param string $ip ip
     * @return bool
     * @throws HttpRequestFailed
     */
    public function realtimeCheat(array $cheatData, string $ip)
    {
        $params = $cheatData;
        $params['ip'] = $ip;
        $params['score'] = 0;
        if (empty($params['taskid'])) {
            $params['taskid'] = 1;
        }
        if (empty($params['passid'])) {
            return true;
        }
        if (empty($params['client'])) {
            $params['client'] = [];
        }
        if (empty($params['client']['header'])) {
            $params['client']['header'] = [];
        }
        $params['client']['header']['send_time'] = time();
        $url = $this->baseUrl . $this->urlRealTimeCheat;

        $response = $this->client->json()->post($url, $params, 1, 3);
        if ($response->getStatusCode() != 200) {
            return true;
        }
        $json = json_decode($response->getBody(), true);
        //徐永超：刚确认了下，不需要判断违规次数，message=SUCCESS  即不违规
        if (empty($json['message'])) {
            return true;
        } elseif (strtolower($json['message']) == 'success' ) {
            return true;
        } elseif ($json['grant'] == 0 && $json['cheat'] == 0 && $json['freeze'] == 0) {
            return true;
        } else {
            return false;
        }
    }
}
