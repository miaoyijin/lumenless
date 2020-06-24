<?php

//注册前台专用中间件或服务

/*
|--------------------------------------------------------------------------
| Register Container Bindings
|--------------------------------------------------------------------------
| 在此处绑定前台专用对象或实例
| Now we will register a few bindings in the service container. We will
| register the exception handler and the console kernel. You may add
| your own bindings here if you like or you can make another file.
|
*/
//$app->singleton(
//    Illuminate\Contracts\Debug\ExceptionHandler::class,
//    App\Exceptions\Handler::class
//);

/*
|--------------------------------------------------------------------------
| Register Middleware
|--------------------------------------------------------------------------
| 注册前台中间件
| Next, we will register the middleware with the application. These can
| be global middleware that run before and after each request into a
| route or middleware that'll be assigned to some specific routes.
|
*/

$app->routeMiddleware([
    'auth' => App\Http\Middleware\Web\Authenticate::class,
    'beforeRequest' => App\Http\Middleware\Web\BeforeRequest::class,
    'endRequest' => App\Http\Middleware\Web\EndRequest::class,
]);

$app->register(App\Providers\WebServiceProvider::class);

return $app;
