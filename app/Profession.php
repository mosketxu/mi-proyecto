<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Profession extends Model
{
    //// protected $table='myProfessions"; por si la tabla no se llama professions

    // si no quisiera los campo created_at y modified_at lo debo comentar en el ProfessionSeeder y
    // ademas añadir la siguiente sentencia, ya que sino Laravel los busca al ser una exension de Eloquent

    // public $timestamps=false;
}
