<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',//'created_at','updated_at',
    ];

    protected $casts=[
        'is_admin'=>'boolean'       //sin hacer esto, el mysql crea el campo is_admin como tinyInt
    ];

    public static function findByEmail($email){
        // Es equivalente poner User y poner Static porque estoy en el modelo User
        // return User::where(compact('email'))->first();
        return static::where(compact('email'))->first();
    }

    public function profession(){ 
        // si respeto la nomenclatura de Laravel/Eloquent lo relaciona buscando una comlumna que se llame profession_id en la tabla users
        return $this->belongsTo(Profession::class);
        // si no hay campo profession_id hay que enviar el campo en la sentencia
        // return $this->belongsTo(Profession::class,'nombreDelCampo');
        
    }    

    public function isAdmin(){
        return $this->is_admin ;
    }
}
