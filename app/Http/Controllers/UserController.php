<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use Illuminate\Validation\Rule;

// use Illuminate\Support\Facades\DB; como no uso el constructor de consultas sino eloquent no me hace falta el facade DB

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
        return view('users.create'); 
        // return 'Crear nuevo usuario';
    } 

    public function store()
    {
        $data=request()->validate([
            'name'=>'required',
            'email'=>'required|email|unique:users,email',
            'password'=>'required','min:6'
        ],[
            'name.required'=>'El campo nombre es obligatorio',
            'email.required',
            'password.required',
        ]);

        
        User::create([
            'name'=>$data['name'],
            'email'=>$data['email'],
            'password'=>bcrypt($data['password'])
        ]);
        
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
			unset($data['password']);  //quita el indice de arrat asocitivo de $data, es decir no lo valida
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