<?php
/***
 * 前端服务注入
 */
namespace App\Providers;

use Laravel\Lumen\Http\Request;
use infra\librarys\utils\Functions;
use Illuminate\Support\ServiceProvider;

class WebServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //todo
    }

    /**
     * Boot the authentication services for the application.
     *
     * @return void
     */
    public function boot()
    {
        $this->app['auth']->viaRequest('api', function (Request $request) {
            //完成游客和注册用户绑定以及生成游客信息
            /**@var UserService $userSer */
            /** 5分钟内请求失效*/
            if (!Functions::isDev() && $request->post('timestamp') < (time() - 300)) {
                return null;
            }
            if (empty($request->post('passid'))) {
                throw new \Exception('请登录！', 501);
            }
            return true;
        });
    }
}
