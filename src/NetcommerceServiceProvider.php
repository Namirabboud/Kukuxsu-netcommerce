<?php

namespace Kukuxsu\Netcommerce;

use Illuminate\Support\ServiceProvider;


class NetcommerceServiceProvider extends ServiceProvider
{


    /**
     * Bootstrap services.
     *
     * @return void
     */

    public function boot()
    {
        $this->loadRoutesFrom(__DIR__.'/routes.php');
        $this->loadViewsFrom(__DIR__.'/Views', 'base');


        $this->publishes([
            __DIR__.'/Traits' => base_path('app/Http/Traits'),
        ]);
    }
}
