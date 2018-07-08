<?php

namespace App;

class Role
{
    /* Este metodo retorna un listado de roles */
    /* es statico para poder llamarlo sin tener que instanciar la clase*/
    public static function getList(){
        return['admin','user'];
    }
}