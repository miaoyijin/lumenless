<?php

namespace  infra\Librarys\Driver;

use Illuminate\Support\Facades\Redis as RedisConnecter;

/**
 * Class Redis
 * @method static get
 * @method static mGet
 * @method static hMGet
 * @method static hGetAll
 * @method static set
 * @method static hMSet
 * @package infra\librarys\driver
 */
class Redis{
    //modify by self
    private static $readFunc = [
        'get',
        'mGet',
        'hMGet',
        'hGetAll',
    ];

    private static $writeFunc = [
        'set',
        'hMSet',
    ];
    private static $connections = ['write' => null , 'read' => null];

    public function __callStatic($method, $arguments)
    {
        if (!in_array($method, array_merge(self::$writeFunc, self::$readFunc))) {
            throw new \RedisException('forbid method');
        }
        if (isset(self::$writeFunc[$method])) {
            return self::getWriteConn()->command($method, $arguments);
        } else {
            return self::getReadConn()->command($method, $arguments);
        }
    }

    /**
     * 获取写的redis
     * @return RedisConnecter
     */
   private static function getWriteConn()
   {
       if (empty(self::$connections['write'])) {
           self::$connections['write'] = RedisConnecter::connection();
       }
       return self::$connections['write'];
   }

    /**
     * 获取读的redis
     * @return RedisConnecter
     */
    private static function getReadConn()
    {
        if (empty(self::$connections['read'])) {
            $slaves = config('database.redis.slaves');
            if ($slaves) {
                $index = (mt_rand(0, 1000) % count($slaves));
                self::$connections['read'] = RedisConnecter::connection($slaves[$index]);
            } else {
                return self::getWriteConn();
            }
        }
        return self::$connections['read'];
    }
}