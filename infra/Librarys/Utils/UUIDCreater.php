<?php

namespace infra\Librarys\Utils;

/**
 * UUID生成器
 *
 */
class UUIDCreater
{
    /**
     * @var int UUID类型
     */
    public const UUID_TYPE_TOKEN = 1;
    public const UUID_TYPE_REGISTER = 2;
    public const UUID_TYPE_FINDPWD = 3;

    /**
     * 获取一个唯一id
     * @return string
     */
    public static function getUuid()
    {
        $userID = UUIDCreater::getRstr(32);
        $type = UUIDCreater::getRstr(32);
        return static::getUserTypeUuid($userID, $type);
    }

    /**
     * 获取TokenUuid
     *
     * @param $account string 用户账号
     * @return string
     */
    public static function getTokenUuid($account)
    {
        return static::getUserTypeUuid($account, self::UUID_TYPE_TOKEN);
    }

    /**
     * 获取RegisterUuid
     *
     * @param $account string 用户账号
     * @return string
     */
    public static function getRegisterUuid($account)
    {
        return static::getUserTypeUuid($account, self::UUID_TYPE_REGISTER);
    }

    /**
     * 获取FindpwdUuid
     *
     * @param $account string 用户账号
     * @return string
     */
    public static function getFindpwdUuid($account)
    {
        return static::getUserTypeUuid($account, self::UUID_TYPE_FINDPWD);
    }

    /**
     * 获取某个类型的Uuid
     *
     * @param $account
     * @param $type
     * @return string
     */
    private static function getUserTypeUuid($account, $type)
    {
        return md5($account . '|' . $type . '|' . microtime(true) . '|' . UUIDCreater::getRstr(32));
    }

    /**
     * 获取随机串(大小写数字)
     *
     * @param int $length default:16
     * @param string $prefix 前缀
     * @return string
     */
    public static function getRstr($length = 16, $prefix = '')
    {
        $chars = str_repeat('ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789', 5);
        return $prefix . substr(str_shuffle($chars), 0, $length);
    }
}
