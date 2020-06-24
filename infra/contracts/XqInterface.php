<?php
namespace infra\contracts;

/**
 * 星球交互接口
 * @author yangd
 */
interface XqInterface
{
    /**
     * 获取用户信息
     * @param int|string $passId
     * @return mixed
     */
    public function getGoldSum($passId = '');

    /**
     * 发金币
     * @param array $data data
     * @return mixed
     */
    public function addCoin(array $data);

    /**
     * 对总账
     * @param array $data data
     * @return mixed
     */
    public function checkTotalAccount(array $data);
}
