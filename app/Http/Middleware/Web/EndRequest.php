<?php
/**
 * 加密输出内容
 */
namespace App\Http\Middleware\Web;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use infra\contracts\ProtocolInterface;
use infra\librarys\protocoll\RsaProtocoll;

class EndRequest
{
    /**
     * Handle an incoming request.
     *
     * @param  Request  $request request
     * @param Closure $next next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        /**
         * @var Response $response
         */
        $response = $next($request);
        /**输出加密处理 @var RsaProtocoll $protocol */
        $protocol = app(ProtocolInterface::class);
        $data = $protocol->convertOutput($response->getContent());
        $response->setContent($data);
        return $response;
    }
}
