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
        header('Access-Control-Allow-Origin: ' . ($_SERVER['HTTP_ORIGIN'] ?? '*'));
        header('Access-Control-Allow-Headers: NOT,*');
        header('Access-Control-Allow-Methods: *');
        //header('Access-Control-Allow-Credentials: true');
        header("Access-Control-Max-Age: 3600");
        return $next($request);
    }
}
