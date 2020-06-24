<?php
/***
 * ͨ公用服务注入
 */

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Redis\RedisServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->register(RedisServiceProvider::class);
    }
}
