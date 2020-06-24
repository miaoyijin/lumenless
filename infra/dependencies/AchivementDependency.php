<?php

namespace infra\dependencies;

use infra\librarys\proxy\Http;
use infra\librarys\utils\AppConst;
use infra\librarys\utils\UUIDCreater;
use infra\contracts\AchivementInterface;
use infra\exceptions\HttpRequestFailed;
use infra\librarys\utils\Functions;

/**
 * 成就体系依赖
 * Class AchivementDependency
 * @package dependency
 */
class AchivementDependency implements AchivementInterface
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
     * @var string
     */
    private $signKey;
    /**
     * @var string
     */
    private $project;

    private $urlGetUser = '/service/User/Achievement';
    private $urlReport = '/service/report';
    private $urlReportInit = '/service/InitAchv';
    private $urlGetAchive = '/api/achievement';

    /**
     * @var Http
     */
    private $client;

    /**
     * XqDependency constructor.
     * @param Http $http http请求类
     */
    public function __construct(Http $http)
    {
        $this->baseUrl = config('dependency.achivement.baseUrl');
        $this->domain = config('dependency.achivement.domain');
        $this->signKey = config('dependency.achivement.signKey');
        $this->project = config('dependency.achivement.project');

        $this->client = $http;
        $this->client->withOptions([
            'headers' => [
                'Host' => $this->domain,
            ],
            'verify'  => false,
            'expect' => false,
        ]);
    }

    /**
     * 获取用户成就信息
     * @param array $data data
     * @return array
     * @throws HttpRequestFailed
     */
    public function getUserAchievement(array $data)
    {
        $params = [
            'passId' => $data['passid'] ?? '',
        ];
        $params = $this->makeSign($params);
        $url = $this->baseUrl . $this->urlGetUser;

        $response = $this->client->get($url, $params, 2, 2);
        if ($response->getStatusCode() != 200) {
            return [];
        }

        //根据原有代码改写
        $body = $response->getBody()->getContents();
        $data = json_decode($body, true);
        if (empty($data) || empty($data['code']) || $data['code'] != 200) {
            return [];
        }
        return $data['data'] ?? [];
    }

    /**
     * 上报成就行为数据
     * @param array $data data
     * @return array
     * @throws HttpRequestFailed
     */
    public function reportAchievement(array $data)
    {
        $uuid = UUIDCreater::getUuid();
        $processList = [];
        foreach ($data as $k => $item) {
            $processList[] = [
                'projectSN'    => $uuid . '-' . ($k + 1),
                'passId'       => $item['passId'],
                'tradingValue' => $item['tradingValue'],
                'code'         => $item['code'],
                'taskId'       => $uuid,
                'tradingTime'  => time(),
                'desc'         => '',
            ];
        }
        $params = [
            'type'        => 'achv',
            'processList' => json_encode($processList),
        ];
        $params = $this->makeSign($params);
        $url = $this->baseUrl . $this->urlReport;
        $response = $this->client->post($url, $params, 2, 2);
        if ($response->getStatusCode() != 200) {
            return [];
        }
        $data = json_decode($response->getBody(), true);
        Functions::log(__METHOD__ . "订单成就：", ['request' => $params, 'result' => $data]);
        return $data ? $data : [];
    }

    /**
     * 初始化成就
     * @param int   $passid passid
     * @param array $data data
     * @return void
     * @throws HttpRequestFailed
     */
    public function reportAchievementInit(int $passid, array $data)
    {
        $uuid = UUIDCreater::getUuid();
        $processList['passId'] = $passid;
        $processList['tradingTime'] = time();
        $processList['achv'] = [];
        foreach ($data as $k => $item) {
            $processList['achv'][] = [
                'projectSN'    => $uuid . '-' . ($k + 1),
                'tradingValue' => $item['tradingValue'],
                'code'         => $item['code'],
            ];
        }
        $params = [
            'processList' => json_encode([$processList]),
        ];
        $params = $this->makeSign($params);
        $url = $this->baseUrl . $this->urlReportInit;
        $response = $this->client->post($url, $params, 2, 2);
        if ($response->getStatusCode() != 200) {
            return [];
        }
        $data = json_decode($response->getBody(), true);
        Functions::log(__METHOD__ . "初始化成就：", ['request' => $params, 'result' => $data]);
        return $data ? $data : [];
    }

    /**
     * 获取成就配置
     * @param array $data data
     * @return array
     * @throws HttpRequestFailed
     */
    public function getAchievementConfig(array $data = [])
    {
        $params = $this->makeSign($data);
        $url = $this->baseUrl . $this->urlGetAchive;

        $list = [
            AppConst::ACHIEVEMENT_CODE_OFFICIER => [],
            AppConst::ACHIEVEMENT_CODE_TREE     => [],
            AppConst::ACHIEVEMENT_CODE_BOX      => [],
        ];
        $response = $this->client->get($url, $params, 2, 2);
        if ($response->getStatusCode() != 200) {
            return $list;
        }
        $json = json_decode($response->getBody(), true);
        if (empty($json['data']['achievement'])) {
            return $list;
        }
        $achievements = $json['data']['achievement'];
        foreach ($achievements as $achievement) {
            switch ($achievement['code']) {
                case AppConst::ACHIEVEMENT_CODE_TREE:
                case AppConst::ACHIEVEMENT_CODE_OFFICIER:
                case AppConst::ACHIEVEMENT_CODE_BOX:
                    $list[$achievement['code']] = $achievement['levelList'] ?? [];
                    break;
            }
        }
        return $list;
    }

    /**
     * 生成token
     * @param array $data data
     * @return array
     */
    protected function makeSign(array $data): array
    {
        $data["project"] = $this->project;
        $data["requestTime"] = time();
        ksort($data);
        $data["sign"] = md5(http_build_query($data) . "{$this->signKey}");
        return $data;
    }

}
