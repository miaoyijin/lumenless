<?php

namespace App\Http\Middleware\Web;

use Closure;
use infra\librarys\utils\Functions;
use Illuminate\Http\Request;
use infra\contracts\ProtocolInterface;
use Illuminate\Contracts\Auth\Factory as Auth;

class Authenticate
{
    /**
     * The authentication guard factory instance.
     *
     * @var Auth
     */
    protected $auth;

    /**
     * Create a new middleware instance.
     *
     * @param Auth $auth auth
     * @return void
     */
    public function __construct(Auth $auth)
    {
        $this->auth = $auth;
    }

    /**
     * Handle an incoming request.
     *
     * @param  Request  $request request
     * @param Closure $next next
     * @param  string|null  $guard guard
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {
        if ($this->auth->guard($guard)->guest()) {
            $protocol = app(ProtocolInterface::class);
            return response($protocol->convertOutput(Functions::NotAllow()), 401);
        }
        return $next($request);
    }
}
