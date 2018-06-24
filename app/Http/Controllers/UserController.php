<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
// use App\User;
// use App\UserProfile;
// uso mejor notacion de PHP 7
use App\{User, UserProfile, Profession};
use Illuminate\Validation\Rule;
use App\Http\Requests\CreateUserRequest;

//use Illuminate\Support\Facades\DB; //si no no uso el constructor de consultas sino eloquent no me hace falta el facade DB. Pero lo uso en store para el rollback de las transacciones pero como me lo he llevado al modelo User lo vuelvo a comentar

class UserController extends Controller
{
    public function index(){
        // if (request()->has('empty')){
        //     $users=[];
        // }
        // else{
            // $users= DB::table('users')->get();
            $users=User::all();    
    // }
        // opcion típica de pasar datos
/*         return view('users',[
            'users' => $users,
            'title' => 'Listado de Usuarios'
        ]);
 */    
        // opcion compact de pasar datos y uso de dd que es parecido a un var_dump y luego die

        // dd(compact('title','users'));

        $title= 'Listado de Usuarios';
        // return view('users.index')
        //     ->with('users',User::all())
        //     ->with('title','Listado de usuarios con otro title');
        
        return view('users.index',compact('title','users'));
}

    // public function show($id){ // la manera habitual pero Laravel lo mejora con la siguiente sentecia pero hay que corregir las rutas
    public function show(User $user){ // recuerda corregir las rutas
        // return view('show',['id'=>$id]);

        // exit('linea no alcanzada');

        // lo quito si uso el metodo de laravel en la funcion y modifico la ruta
        // $user=User::findOrFail($id);
        // dd($user);
/*      Como uso el metodo findOrFail en lugar del metodo fins no hace falta el condicional
        
        exit('linea no alcanzada');

        if ($user==null){
           return response()->view('errors.404',[],404); //el segundo argumento son los datos pero no hay en este caso. El tercero el error que quiero mostrar,i.e, 404
        }
 */
        // dd($user);
        return view('users.show',compact('user')); 
    }

    public function create(){

        $professions= Profession::OrderBy('title','ASC')->get(); // pongo use Profession al principio

        return view('users.create',compact('professions')); 
        // return 'Crear nuevo usuario';
    } 

    // public function store()  //sin el form request
    public function store(CreateUserRequest $request)
    {
        // si no uso FormRequest
        // $data=request()->validate([
        //     'name'=>'required',
        //     'email'=>'required|email|unique:users,email',
        //     'password'=>'required','min:6',
        //     'bio'=>'required', //para validar esto hacer una prueba con TDD
        //     'twitter'=>'nullable|url', //para validar esto hacer una prueba con TDD
        // ],[
        //     'name.required'=>'El campo nombre es obligatorio',
        //     'email.required',
        //     'password.required',
        // ]);

//        $request->save(); // con el FormRequest no me hace falta lo de arriba. LLamo al metodo save y si ha funcionado
                          // tambien llama al metodo User::createUser($data) pero solo si lo ha pasado
                            // quito la llamada a User::createUser($data) de unas lineas mas abao

        $request->createUser();  //cambio el nombre del metodo save() a createUser() y asi meteré la logica de creacion directamente en él
        
        // si meto toda la logica de la transaccion dentro de DB::transaction, podré hacer un rollback. Aunque el codigo queda un poco feo. Así que creare un nuevo metodo (despues de comentado)
//        DB::transaction(function()use ($data) {
//            $user = User::create([
//                'name'=>$data['name'],
//                'email'=>$data['email'],
//                'password'=>bcrypt($data['password'])
//            ]);
//            
//            //si lo hago creando la relacion en la migracion create_user_profiles_table
//            // UserProfile::create([
//            //     'bio'=> $data['bio'],
//            //     'twitter'=> $data['twitter'],
//            //     'user_id'=> $user->id,  
//            // ]);
//    
//            //si lo hago creando la relacion en el modelo User con la funcion profile()
//            //No hace falta que cree la llave foránea
//            $user->profile()->create([
//                'bio'=> $data['bio'],
//                'twitter'=> $data['twitter'],
//                // 'user_id'=> $user->id,  // De esta manera no tengo que indicar este campo porque Eloquent lo va a hacer por mi. 
//                'github'=>'https://github.com/mosketxu',
//            ]);
//    
//        });

        // Creo un nuevo metodo en el modelo User para dejar todo el codigo de arriba limpio

        // User::createUser($data); lo quito porque lo llamo desde el formrequest 

        return redirect()->route('users.index');
    } 

    public function edit(User $user){
        // return view('edit',['id'=>$id]);
        return view('users.edit',['user'=>$user]);
    }
    
    public function update(User $user){ 

        // dd('actualizar usuario');
        
        $data=request()->validate([
            'name'=>'required',
            // 'email'=>'required|email|unique:users,email,'.$user->id,  // en unique los parametros vienen por comas: primero la tabla, luego la columna y luego el ide del usuario que queremos excluir de la validacion
            // desde Laravel 5.3 puedo definir la reglas con sintaxis orientada a objetos
            'email'=>['required','email',Rule::unique('users')->ignore($user->id)], // si el nombre del campo del array coincide con el de la columna lo puedo quitar, sino sería como sigue: Rule::unique('users','email',)->ignore($user->id)]
            'password'=>'nullable|min:6',
        ]);

		if ($data['password'] != null){
			$data['password']=bcrypt($data['password']);
		} else {
			unset($data['password']);  //quita el indice de array asociativo de $data, es decir no lo valida
		}

        $user->update($data);

        return redirect()->route('users.show',['user'=>$user]);
        // return redirect()->route('users.index');
    }

    public function destroy(User $user){

        $user->delete();
        return redirect()->route('users.index');
        // return redirect()->route('usuarios'); es lo mismo
    } 


}