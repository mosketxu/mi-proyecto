<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
// use Illuminate\Support\Facades\DB; //me hace falta para el metodo createUSer pero como lo he quitado no me hace falta

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
        'is_admin'=>'boolean'   //sin hacer esto, el mysql crea el campo is_admin como tinyInt
    ];

    // me llevo la logica de creacion de usuario al formRequest
    // public static function createUser($data){
    //     DB::transaction(function()use ($data) {
    //         $user = User::create([
    //             'name'=>$data['name'],
    //             'email'=>$data['email'],
    //             'password'=>bcrypt($data['password'])
    //         ]);
            
    //         //si lo hago creando la relacion en la migracion create_user_profiles_table
    //         // UserProfile::create([
    //         //     'bio'=> $data['bio'],
    //         //     'twitter'=> $data['twitter'],
    //         //     'user_id'=> $user->id,  
    //         // ]);
    
    //         //si lo hago creando la relacion en el modelo User con la funcion profile()
    //         //No hace falta que cree la llave foránea
    //         $user->profile()->create([
    //             'bio'=> $data['bio'],
    //             'twitter'=> $data['twitter'],
    //             // 'user_id'=> $user->id,  // De esta manera no tengo que indicar este campo porque Eloquent lo va a hacer por mi. 
    //         ]);
    
    //     });
        
    // }
    
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
    
    // cero la relacion entre User (estoy en el modelo ya) y UserProfile indicandole que el user tiene un perfil 
    public function profile(){
        return $this->hasOne(UserProfile::class);
    }

    public function isAdmin(){
        return $this->is_admin ;
    }
}
