<?php

namespace Kukuxsu\Ecommerce;

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
        $this->loadViewsFrom(__DIR__.'/views', 'base');


        $this->publishes([
            __DIR__.'/Traits' => base_path('app/Http/Traits'),
        ]);
    }
}
