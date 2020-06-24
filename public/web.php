<?php

/*
|--------------------------------------------------------------------------
| Create The Application
|--------------------------------------------------------------------------
|
| First we need to get an application instance. This creates an instance
| of the application / container and bootstraps the application so it
| is ready to receive HTTP / Console requests from the environment.
|
*/
$app = require __DIR__.'/../bootstrap/app.php';
$isDev = \infra\librarys\utils\Functions::isDev();
$CORSDomain = 'chengyu.2345.cn';//跨域domain
if (!empty($_SERVER['HTTP_ORIGIN'])) {
    $CORSDomain = $_SERVER['HTTP_ORIGIN'];
}
if ($isDev) {
    //如果沒有接入公司网关则$is_dev判断需要去掉
    $http_type = ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https')) ? 'https://' : 'http://';
    header('Access-Control-Allow-Origin: ' . $CORSDomain);
    header('Access-Control-Allow-Headers: NOT,*');
    header('Access-Control-Allow-Methods: POST,GET,OPTIONS');
    header('Access-Control-Allow-Credentials: true');
    header("Access-Control-Max-Age: 3600");
}
if (strtolower($_SERVER['REQUEST_METHOD']) == 'options') {
    exit('ok');
}

/*
|--------------------------------------------------------------------------
| Run The Application
|--------------------------------------------------------------------------
|
| Once we have the application, we can handle the incoming request
| through the kernel, and send the associated response back to
| the client's browser allowing them to enjoy the creative
| and wonderful application we have prepared for them.
|
*/
$app->run();
