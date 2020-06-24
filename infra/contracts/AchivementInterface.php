<?php

namespace infra\contracts;

/**
 * 成长体系交互接口
 * @author yangd
 */
interface AchivementInterface
{
    /**
     * 获取用户成就信息
     * @param array $data data
     * @return array
     */
    public function getUserAchievement(array $data);

    /**
     * 上报成就行为数据
     * @param array $data data
     * @return array
     */
    public function reportAchievement(array $data);

    /**
     * 上报成就行为数据 初始化
     * @param int $passid passid
     * @param array $data data
     * @return mixed
     */
    public function reportAchievementInit(int $passid,array $data);

    /**
     * 获取成就配置
     * @param array $data data
     * @return array
     */
    public function getAchievementConfig(array $data = []);
}
