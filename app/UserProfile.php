<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserProfile extends Model
{
    //protected $fillable=['bio','twitter','user_id']; // si lo hago creando la relacion en la migracion create_user_profiles_table
    // protected $fillable=['bio','twitter']; //si lo hago creando la relacion en el modelo User con la funcion profile()

    protected $fillable=['bio','twitter','github','profession_id','otraProfesion']; //si lo hago creando la relacion en el modelo User con la funcion profile()
}
