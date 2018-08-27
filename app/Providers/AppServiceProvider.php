<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\View;

use App\{Profession,Skill};
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

        // para evitar duplicar codigo usamos loa viewcomposer
        // para ello debo registrar el facade View use Illuminate\Support\Facades\View;
        // y llamo al metodo composer. Dentro llamo a la vista o las vistas, es decir, una cadena o un array a las que afectara el view composer
        // debo importar el modelo composer y el modelo skil
        // y como segundo argumento una funcion anonima donde voy a colocar la logica

        View::composer(['users.create','users.edit'],function($view){
            $professions= Profession::OrderBy('title','ASC')->get(); 
            $skills=Skill::OrderBy('name','ASC')->get(); 
            $roles=trans('users.roles'); 

            $view->with(compact('professions','skills','roles'));
        });

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
