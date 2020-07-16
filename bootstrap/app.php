<?php
use App\Exceptions\Handler;
use domains\DomainProvider;
use Laravel\Lumen\Application;
use Illuminate\Contracts\Debug\ExceptionHandler;
use Illuminate\Contracts\Console\Kernel;
use Laravel\Lumen\Bootstrap\LoadEnvironmentVariables;
use App\Console\Kernel as ConsoleKernel;
use App\Providers\AppServiceProvider;

require_once __DIR__.'/../vendor/autoload.php';
(new LoadEnvironmentVariables(dirname(dirname(__DIR__))  . '/' . basename(dirname(__DIR__)) . '.conf'))->bootstrap();
date_default_timezone_set(env('APP_TIMEZONE', 'PRC'));

/*
|--------------------------------------------------------------------------
| Create The Application
|--------------------------------------------------------------------------
|
| Here we will load the environment and create the application instance
| that serves as the central piece of this framework. We'll use this
| application as an "IoC" container and router for this framework.
|
*/
$app = new Application(dirname(__DIR__));

$app->withFacades();

$app->withEloquent();
/*
|--------------------------------------------------------------------------
| Register Container Bindings
|--------------------------------------------------------------------------
|
| Now we will register a few bindings in the service container. We will
| register the exception handler and the console kernel. You may add
| your own bindings here if you like or you can make another file.
|
*/

$app->singleton(ExceptionHandler::class, Handler::class);

$app->singleton(Kernel::class, ConsoleKernel::class);

/*
|--------------------------------------------------------------------------
| Register Config Files
|--------------------------------------------------------------------------
|
| Now we will register the "app" configuration file. If the file exists in
| your configuration directory it will be loaded; otherwise, we'll load
| the default version. You may register other files below as needed.
|
*/

$app->configure('app');
$app->configure('queue');
$app->configure('alert');
$app->configure('dependency');

/*
|--------------------------------------------------------------------------
| Register Middleware
|--------------------------------------------------------------------------
|
| Next, we will register the middleware with the application. These can
| be global middleware that run before and after each request into a
| route or middleware that'll be assigned to some specific routes.
|
*/
/*
|--------------------------------------------------------------------------
| Register Service Providers
|--------------------------------------------------------------------------
|
| Here we will register all of the application's service providers which
| are used to bind services into the container. Service providers are
| totally optional, so you are not required to uncomment this line.
|
*/
$app->register(AppServiceProvider::class);
$app->register(Illuminate\Mail\MailServiceProvider::class);
$app->alias('mailer', Illuminate\Mail\Mailer::class);
$app->alias('mailer', Illuminate\Contracts\Mail\Mailer::class);
$app->alias('mailer', Illuminate\Contracts\Mail\MailQueue::class);

define('ARTISAN_BINARY', 'art');
//分别为前后台注册不同的服务和中间件
require __DIR__ . "/web.php";

/*
|--------------------------------------------------------------------------
| Load The Application Routes
|--------------------------------------------------------------------------
|
| Next we will include the routes file so that they can all be added to
| the application. This will provide all of the URLs the application
| can respond to, as well as the controllers that may handle them.
|
*/

$app->router->group(['namespace' => 'App\Http\Controllers'], function ($router) {
    require __DIR__.'/../routes/web.php';
});

$app->routeMiddleware([
    'corsControl' => App\Http\Middleware\CorsControl::class,
]);


return $app;
