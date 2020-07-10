<?php
/**
 * 基础server
 */

namespace App\Services;

abstract class BaseService
{

    private static $message = '';

    public const DEFAULT_ERROR = '系统繁忙';

    /**
     * @return mixed
     */
    public static function getMessage()
    {
        return self::$message;
    }

    /**
     * @param string $error error
     * @return bool
     */
    public static function setMessage(string $error)
    {
        self::$message = $error;
        return false;
    }
}
