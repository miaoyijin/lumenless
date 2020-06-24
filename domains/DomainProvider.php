<?php

namespace domains;

use Illuminate\Support\ServiceProvider;
use infra\contracts\ProtocolInterface;
use infra\librarys\protocoll\RsaProtocoll;

class DomainProvider extends ServiceProvider
{
    /**
     * @return void
     */
    public function register()
    {
        $this->app->singleton(ProtocolInterface::class, RsaProtocoll::class);
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //init something here
    }
}
