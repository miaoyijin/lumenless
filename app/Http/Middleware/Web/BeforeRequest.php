<?php
/**
 * 解密参数并注入到标准输入输出中
 */
namespace App\Http\Middleware\Web;

use Closure;
use Illuminate\Http\Request;
use infra\contracts\ProtocolInterface;
use infra\librarys\protocoll\RsaProtocoll;

class BeforeRequest
{
    /**
     * Handle an incoming request.
     *
     * @param  Request  $request request
     * @param Closure $next next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        /**
         * @var RsaProtocoll  $protocol
         */
        $protocol = app(ProtocolInterface::class);
        $raw = $request->getContent();
        $requestData = $protocol->convertInput($raw);
        $request->merge($requestData ?? []);
        return $next($request);
    }
}
