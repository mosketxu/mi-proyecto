<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
   // return view('welcome');
   return 'Home';
});

Route::get('/usuarios', function () {
    // return view('welcome');
    return 'Usuarios ';
 });

Route::get('/usuarios/nuevo', function () {
    return 'Crear nuevo usuario';
});

Route::get('/usuarios/{id}', function($id){
    return "Mostrando detalle del usuario: {$id}";
})->where('id','[0-9]+');

Route::get('/usuarios/{id}/edit', function($id){
    return "Editando detalle del usuario: {$id}";
})->where('id','[0-9]+');

Route::get('/saludo/{name}/{nickname?}',function($name,$nickname=null){
    $name=ucFirst($name);
    if ($nickname){
        return "Hola {$name}, tu nick es {$nickname}";
    }
    else{
        return "Hola {$name},";
    }
});


 