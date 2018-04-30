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

Route::get('/usuarios', 'UserController@index');

Route::get('/usuarios/nuevo', 'UserController@create');

Route::get('/usuarios/{id}', 'UserController@show')
    ->where('id','[0-9]+');

Route::get('/usuarios/{id}/edit', 'UserController@edit')
    ->where('id','[0-9]+');

Route::get('/saludo/{name}','WelcomeUserController@nameWithoutNick');

Route::get('/saludo/{name}/{nickname}','WelcomeUserController@nameWithNick');

 