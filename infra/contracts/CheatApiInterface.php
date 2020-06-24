<?php
namespace infra\contracts;

/**
 * 实时反作弊交互接口
 * @author yangd
 */
interface CheatApiInterface
{
    /**
     * 反作弊
     * @param array $cheatData cheatData
     * @param string $ip ip
     * @return bool true表示通过反作弊，false表示未通过
     */
    public function realtimeCheat(array $cheatData, string $ip);
}
