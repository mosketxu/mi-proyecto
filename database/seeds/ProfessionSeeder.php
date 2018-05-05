<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use \App\Profession;

class ProfessionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        // directamente con codigo sql
        // Entre otros problemas permite inyeccion de sql
        // DB::insert('INSERT INTO professions (title) VALUES ("Desarrollador Back-ends")');

        // directamente con codigo sql y parametros dinamicos con el componente PDO de PHP
        // Con marcadores
        // DB::insert('INSERT INTO professions (title) VALUES (?)',['Desarrollador Back-end3']);

        // usando como marcador un parametro de sustitucion
        // DB::insert('INSERT INTO professions (title) VALUES (:title)',['title'=>'Desarrollador Back-end4']);

        // con metodo DB:insert
        // DB::table('professions')->insert([
        //     'title'=>'Desarrollador Back-end',
        // ]);

        // DB::table('professions')->insert([
        //     'title'=>'Desarrollador Front-end',
        // ]);

        // DB::table('professions')->insert([
        //     'title'=>'Diseñador web',
        // ]);

/*         // creo y borro una profession
        DB::table('professions')->insert([
            'title'=>'borrar',
        ]);

        DB::table('professions'
            )->where('title', 'borrar')->delete(); */

        // creo profesiones con Eloquent
        \App\Profession::create([
            'title'=>'Desarrollador Back-end',
        ]);

        // Si pongo al principio use \App\Profession me evito la ruta
        Profession::create([
            'title'=>'Desarrollador Front-end',
        ]);

        Profession::create([
            'title'=>'Diseñador web',
        ]);

    }
}
