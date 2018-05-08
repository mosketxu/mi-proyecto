<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
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

    public function edit($id){
        // return view('edit',['id'=>$id]);
        return view('users.edit',compact('id'));
    }
}