<?php

namespace Abiside\LibroAzul;

use Illuminate\Support\ServiceProvider;
use Abiside\LibroAzul\Services\LibroAzul;

class LibroAzulServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__.'/config/services.php', 'services'
        );
        $this->app->singleton('libroazul', function() {
            return new LibroAzul();
        });
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
