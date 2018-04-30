<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class WelcomeUserController extends Controller
{
    public function nameWithoutNick($name){
        $name=ucFirst($name);
        return "Hola {$name},";
    }
    public function nameWithNick($name,$nickname){
        $name=ucFirst($name);
        return "Hola {$name}, tu nick es {$nickname}";
    }
}
