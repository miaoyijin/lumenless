<?php
/**
 * @return bool
 */

namespace infra\librarys\utils;

trait AutoMake
{
    /**
     * 自动加载，根据需要在扩展
     * @param $name
     * @return \Laravel\Lumen\Application|mixed
     */
    public function __get($name)
    {
        $class = 'domains\home\services\\' . ucfirst($name);
        return app($class);
    }
}
