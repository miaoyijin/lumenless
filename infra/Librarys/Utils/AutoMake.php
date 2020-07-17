<?php
/**
 * @return bool
 */

namespace infra\Librarys\Utils;

use App\Services\IndexService;

/**
 * Trait AutoMake
 * @package infra\librarys\utils
 * @property \App\Services\IndexService
 */
trait AutoMake
{
    /**
     * @var array $instances 实例类
     */
    private $instances;

    //类的映射
    public static $classMap = [
        'indexService' => IndexService::class,
    ];
    /**
     * 自动加载，根据需要在扩展
     * @param  string $name name
     * @return \Laravel\Lumen\Application|mixed
     */
    public function __get($name)
    {
        if (!$this->instances[$name]) {
            $this->instances[$name] = app(self::$classMap[$name]);
        }
        return $this->instances[$name];
    }
}
