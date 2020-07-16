<?php
/**
 * 跨域处理
 */

namespace App\Http\Middleware;

use Closure;
use Exception;
use Illuminate\Http\Request;

class CorsControl
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request request
     * @param Closure $next next
     * @return mixed
     * @throws Exception
     */
    public function handle($request, Closure $next)
    {
        $CORSDomain = $_SERVER['HTTP_ORIGIN'];
        $httpScheam = ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https')) ? 'https://' : 'http://';
        header('Access-Control-Allow-Origin: ' . $httpScheam . $CORSDomain);
        header('Access-Control-Allow-Headers: NOT,*');
        header('Access-Control-Allow-Methods: *');
        //header('Access-Control-Allow-Credentials: true');
        header("Access-Control-Max-Age: 3600");
        return $next($request);
    }
}
