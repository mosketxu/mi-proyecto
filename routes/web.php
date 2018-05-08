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

Route::get('/', 'WelcomeController@index')
    ->name('welcome');

    // Route::get('/welcome', 'WelcomeController@index');

Route::get('/saludo/{name}/{nickname}','WelcomeUserController@nameWithNick')
    ->name('welcome.withNick');
  
Route::get('/usuarios', 'UserController@index')
    ->name('users.index');

Route::get('/usuarios/nuevo', 'UserController@create')
     ->name('users.nuevo');

Route::post('/usuarios/crear','UserController@store')
    ->name('users.crear');

// Route::get('/usuarios/{id}', 'UserController@show')
// Modificamos la ruta con el nombre de la funcion del controlador para usar laravel en el controlador
    Route::get('/usuarios/{user}', 'UserController@show')
    // ->where('id','[0-9]+')
    ->where('user','[0-9]+')
    ->name('users.show');

Route::get('/usuarios/{id}/edit', 'UserController@edit')
    ->where('id','[0-9]+')
    ->name('users.edit');

Route::get('/saludo/{name}','WelcomeUserController@nameWithoutNick')
    ->name('welcome.withOutNick');


 