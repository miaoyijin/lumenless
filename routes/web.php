<?php

/** @var Router $router */

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

use Laravel\Lumen\Routing\Router;
use \Illuminate\Http\Request;
$router->group(['prefix' => '/', 'middleware' => 'corsControl|beforeRequest|auth|endRequest', 'namespace' => 'Web'],
    function () use($router) {
    $router->options('{path:.*}', function (Request $request) {
        //跨域处理
        return [];
    });
    $router->post('/index', 'indexController@index');
});
$router->post('/tools/rsapkdecode', 'Web\ToolsController@rsaPKeyDecode');
$router->post('/tools/rsaikdecode', 'Web\ToolsController@rsaiKeyDecode');
//服务端容错路由
$router->get('/', 'Controller@index');
$router->post('/', 'Controller@index');
$router->get('{path:.*}', function (Request $request) {
    return '404';
});
$router->post('{path:.*}', function (Request $request) {
    return '404';
});
//容错路由
