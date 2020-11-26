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
        $this->app->bind('libroazul', function() {
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
