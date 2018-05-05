<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;


class UserController extends Controller
{
    public function index(){
        if (request()->has('empty')){
            $users=[];
        }
        else{
            $users=['Joel','Alex','Juan'];
        }
        // opcion típica de pasar datos
/*         return view('users',[
            'users' => $users,
            'title' => 'Listado de Usuarios'
        ]);
 */    
        // opcion compact de pasar datos y uso de dd que es parecido a un var_dump y luego die

        // dd(compact('title','users'));

        $title= 'Listado de Usuarios';
        return view('users.index',compact('title','users'));
}

    public function show($id){
        // return view('show',['id'=>$id]);
        return view('users.show',compact('id'));
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