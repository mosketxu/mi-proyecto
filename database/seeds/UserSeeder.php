<?php

use App\User;
use App\Profession;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
/*         Seleccionar de forma manual y marcadores para evitar inyeccion SQL
        $professions=DB::select('SELECT id FROM professions WHERE title=?',['Desarrollador Back-end']);
        dd($professions); */

/*         // Seleccionar usando el constructor de consultas devuelve un objeto de la clase  Collection que encapsula el array de datos
        $professions=DB::table('professions')
            ->select('id')->take(1)->get();
            // dd($professions);
            // y asi podemos usar metodos de objetos como first
            $professions->first();
            dd($professions);
 */

/*         // Seleccionar con where
        $profession=DB::table('professions')->select('id')->where('title','=','Desarrollador Back-end')->first();
        dd($profession); */

        // quitando el metodo select de DB:table se obtienen todas las columnas

        // $professionId=DB::table('professions')
        //     ->where('title','Desarrollador Back-end')
        //     ->value('id');

        // Sin Eloquent
/*         $professionId=DB::table('professions')
             ->whereTitle('Desarrollador Back-end')
             ->value('id');
 */
        // Sin Eloquent
        // DB::table('users')->insert([
        //     'name'=>'Alex Arregui',
        //     'email'=>'mosketxu@gmail.com',
        //     'password'=>bcrypt('laravel'),
        //     // 'profession_id'=>$professionId
        // ]);

        // Con Eloquent

        // $professionId=\App\Profession::where('title','Desarrollador Back-end')->value('id');
        $professionId=Profession::where('title','Desarrollador Back-end')->value('id');


        // Podemos retonar un resultado dependiendo de su id mediante el método find():
        // $profession = Profession::find(1);
        	
        // $professions= App\Profession::all();
        // dd($professions);

        //no paso el nombre del la tabla porque el modelo tiene la convencion para que lo encuentre. Se llama User en mayuscula
        // Si creo la tabla con otro nombre , pe en Professions como myProfessions debo escribir la siguiente sentencia en el modelo Professions
        // protected $table='myProfessions";
        // lo dejo comentado en el modelo como ejemplo
        User::create([
            'name'=>'Alex Arregui',
            'email'=>'mosketxu@gmail.com',
            'password'=>bcrypt('laravel'),
            'profession_id'=>$professionId
        ]);

        User::create([
            'name'=>'Otro Usuario',
            'email'=>'no@gmail.com',
            'password'=>bcrypt('laravel'),
            'profession_id'=>$professionId
        ]);


    }
}
