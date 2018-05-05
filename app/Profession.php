<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Profession extends Model
{
    //// protected $table='myProfessions"; por si la tabla no se llama professions

    // si no quisiera los campo created_at y modified_at lo debo comentar en el ProfessionSeeder y
    // ademas añadir la siguiente sentencia, ya que sino Laravel los busca al ser una exension de Eloquent

    // public $timestamps=false;

    //si no pongo la siguiente instruccion a la hora de intentar crear un registro desde un model me daría error
    //se ponen sólo los campos que permitimos rellenar.
    //como ejemplo en User tenemos protected $fillable = ['name', 'email', 'password',];

    protected $fillable=['title'];
}
