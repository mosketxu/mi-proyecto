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
        'name', 'email', 'password',//'profession_id'
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
         //el cast siguiente no me hará falta porque he cambiado la detección de si es admin con el campo role
        //     'is_admin'=>'boolean'   //sin hacer esto, el mysql crea el campo is_admin como tinyInt
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

    public function skills(){
        //es importante la nomeclatura. minusculas y separado con guion bajo. 
        //la convecion de las relaciones muchos a muchos con tablas pivote es el nombre de los modelos en minusculas singular y ordenados de manera alfabetica y separados por un guion,
        //asi que busca la relacion con la tabla skill_user no user_skill, porque la s viene antes que la u
        //si quiero que la tabla se llame realmente user_skill lo tenfo que indicar a la hora de la relacion
        return $this->belongsToMany(Skill::class, 'user_skill'); 
    }
    
    // cero la relacion entre User (estoy en el modelo ya) y UserProfile indicandole que el user tiene un perfil 
    public function profile(){
        // return $this->hasOne(UserProfile::class);

        // el problema de dejarlo tal y como está en la linea anterior es que cuando uso las plantillas de blade y uso la misma para edit y para create
        // cuando devuelvo el valor antiguo en el caso de create uso {{ old('bio', $user->profile->bio) }} y todo va bien
        // pero cuando intento crear un nuevo usuario da un error de que intento acceder a una propiedad de un objeto y este no existe.
        // esto es porque al ser nuevo no existe el objeto. Lo soluciono poniendo ->withDefault();
        // lo que hace esto es crear una instancia del perfil usuario y lo asigna al usuario en cuestion. Algo parecido a lo que hago creando new User en User Controller.
        
        // return $this->hasOne(UserProfile::class)->withDefault();

        //inlcuso podría poner un perfil por defecto en caso de que no lo tuviera:
        return $this->hasOne(UserProfile::class)->withDefault([
            'bio'=>'programador',
            ]);
    }

    public function isAdmin(){
        return $this->is_admin ; // como lo he cambiado por role uso la ss instruccion
        return $this->role==='admin';  //isAdmin devolvera verdadero si el role del usuario es admin
    }
}
