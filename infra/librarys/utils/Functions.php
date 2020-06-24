<?php
/**
 * @return bool
 */

namespace infra\librarys\utils;

class Functions
{
    /**
     * 判断是否dev环境状态
     * @return bool
     */
    public static function isDev()
    {
        //dev,test  测试环境
        //pre,prod,production 生产环境
        if (in_array(config('app.env'), ['dev', 'test'])) {
            return true;
        }
        return false;
    }

    /**
     * @return string
     */
    public static function NotAllow()
    {
        return json_encode(['code' => 401, 'msg' => 'not allowed']);
    }
}
