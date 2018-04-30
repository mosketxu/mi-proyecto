<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index(){
        $users=[
            'Joel',
            'Alex',
            'Juan',
            '<script>alert("Clicker")</script>'
        ];

        // opcion típica de pasar datos
/*         return view('users',[
            'users' => $users,
            'title' => 'Listado de Usuarios'
        ]);
 */    
        // opcion compact de pasar datos y uso de dd que es parecido a un var_dump y luego die

        // dd(compact('title','users'));

        $title= 'Listado de Usuarios';
        return view('users',compact('title','users'));
}

    public function show($id){
        return "Mostrando detalle del usuario: {$id}";
    }

    public function create(){
        return 'Crear nuevo usuario';
    } 

    public function edit($id){
        return "Editando detalle del usuario: {$id}";
    }
}
