<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // Viene de create.blade
        // Para simplificar en lugar de usar @component registro el componente en AppServiceProvider.php
        //     Para ello debo llamar en ese fichero al facade blade, use Illuminate\Support\Facades\Blade;
        //     y luego poner en boot Blade::component('shared._card','card');
        //     de esta manera puedo sustituir @component('shared._card') por @card y @endcomponent por @endcard
             
        Blade::component('shared._card','card');
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
