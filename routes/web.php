<?php

Route::get('/', 'WelcomeController@index')
    ->name('welcome');

    // Route::get('/welcome', 'WelcomeController@index');

Route::get('/saludo/{name}/{nickname}','WelcomeUserController@nameWithNick')
    ->name('welcome.withNick');
  
Route::get('/usuarios', 'UserController@index')
    ->name('users.index');

Route::get('/usuarios/nuevo', 'UserController@create')
     ->name('users.create');

Route::post('/usuarios','UserController@store')
     ->name('users.crear');

// Route::get('/usuarios/{id}', 'UserController@show')
// Modificamos la ruta con el nombre de la funcion del controlador para usar laravel en el controlador
    // Route::get('/usuarios/{user}', 'UserController@show')
    // ->where('id','[0-9]+')
    // ->name('users.show');

Route::get('/usuarios/{user}', 'UserController@show')
    ->where('user','[0-9]+')
    ->name('users.show');
    
Route::get('/usuarios/{user}/editar', 'UserController@edit')
    ->where('user','[0-9]+')
    ->name('users.edit');

Route::put('/usuarios/{user}', 'UserController@update')
    ->where('user','[0-9]+')
    ->name('users.update');

Route::delete('/usuarios/{user}', 'UserController@destroy');
    

Route::get('/saludo/{name}','WelcomeUserController@nameWithoutNick')
    ->name('welcome.withOutNick');


 