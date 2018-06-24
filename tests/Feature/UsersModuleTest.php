<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\User;
use App\Profession;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\DB;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UsersModuleTest extends TestCase
{
    use RefreshDatabase;

    protected $profession;

    /** @test */
    function it_shows_the_users_list()
    {
        //Usuario con nombre Joel y Carlos para que funcione el test
        factory(User::class)->create([
            'name'=>'Joel',
        ]);

        factory(User::class)->create([
            'name'=>'Carlos',
        ]); 
        
        $this->get('/usuarios')
            ->assertStatus(200)
            ->assertSee('Listado de Usuarios')
            ->assertSee('Joel')
            ->assertSee('Carlos');
    }

    /** @test */
    function it_shows_a_default_message_if_the_users_list_is_empty()
    {
        // DB::table('users')->truncate();

        $this->get('/usuarios')
            ->assertStatus(200)
            ->assertSee('No hay usuarios registrados');
    }

    /** @test */
    function it_displays_the_user_details()
    {
        $user=factory(User::class)->create([
            'name'=>'Alexander Arregui'
        ]);

        $this->get('/usuarios/'.$user->id)
            ->assertStatus(200)
            ->assertSee('Alexander Arregui');
    }

    /** @test */
    function it_displays_a_404_error_if_the_user_is_not_found()
    {
        $this->get('/usuarios/999')
            ->assertStatus(404)
            ->assertSee('Página no encontrada');
    }
    
    /** @test */
    function it_loads_the_new_users_page()
    {
        $profession=factory(Profession::class)->create();

        $this->get('/usuarios/nuevo')
            ->assertStatus(200)
            ->assertSee('Crear usuario')
            ->assertViewHas('professions',function($professions) use ($profession){ //verifico que la vista tiene la variable profession y que es la que le estoy pasando aqui
                return $professions->contains($profession);                                //si la funcion anonima retorna verdadero la prueba pasa, sino no pasa
            });
    }

    /** @test */
    function it_creates_a_new_user()
    {
        // $this->post('/usuarios/',[
        //     'name'=>'Alex',
        //     'email'=>'alex@alex.com',
        //     'password'=>'123456'
        // ])->assertSee('Procesando información...');
        
        $this->withoutExceptionHandling();

        //Para no repetir código y que quede todo más limpio
        // me llevo lo comentado siguiente a una funcion en este mismo fichero llamada getValidData al final

        // $this->post('/usuarios/',[
        //     'name'=>'Alex',
        //     'email'=>'alexa@alex.com',
        //     'password'=>'123456',
        //     'bio'=>'Programador de Laravel y Vue.js',
        //     'twitter'=>'https://twitter.com/alexarregui',
        //     ])->assertRedirect(route('users.index'));

        $this->post('/usuarios', $this->getValidData())->assertRedirect(route('users.index'));;

        // dd(User::all());
        // dd(User::first());

            // $this->assertDatabaseHas('users',[
        //     'name'=>'Alex',
        //     'email'=>'alexa@alex.com',
        //     'password'=>'123456'
        // ]);

        $this->assertCredentials([
            'name'=>'Alex',
            'email'=>'alexa@alex.com',
            'password'=>'123456',
            // 'profession_id'=>$this->profession->id, // lo llevo a user_profiles
        ]);

        $this->assertDatabaseHas('user_profiles',[
            'bio'=>'Programador de Laravel y Vue.js',
            'twitter'=>'https://twitter.com/alexarregui',
            'user_id' => User::findByEmail('alexa@alex.com')->id,
            'profession_id'=>$this->profession->id, // lo traigo de user
            'otraProfesion'=>'otra profesion distinta de la lista',
            ]);
    }

        /** @test */
        function the_twitter_field_is_optional()
        {
            $this->withoutExceptionHandling();
    
            // $this->post('/usuarios/',[
            //     'name'=>'Alex',
            //     'email'=>'alexa@alex.com',
            //     'password'=>'123456',
            //     'bio'=>'Programador de Laravel y Vue.js',
            //     // 'twitter'=>null, tengo que ver qué pasa no solo si es null, sino tambien que pasa si no lo envío
            //     ])->assertRedirect(route('users.index'));
    
            // en lugar de lo de arriba uso el metodo getValidData definido al final
            // asegurandome que paso el campo de twitter en null
            $this->post('/usuarios/',$this->getValidData([
                'twitter'=>null,
            ]))->assertRedirect(route('users.index'));

            $this->assertCredentials([
                'name'=>'Alex',
                'email'=>'alexa@alex.com',
                'password'=>'123456'
            ]);
    
            $this->assertDatabaseHas('user_profiles',[
                'bio'=>'Programador de Laravel y Vue.js',
                'twitter'=>null,
                'user_id' => User::findByEmail('alexa@alex.com')->id,
            ]);
        }

    /** @test */
    function the_profession_field_is_optional()
        {
            $this->withoutExceptionHandling();
    
            // asegurandome que paso el campo de profession_id en null
            $this->post('/usuarios/',$this->getValidData([
                'profession_id'=>null,
            ]))->assertRedirect(route('users.index'));

            $this->assertCredentials([
                'name'=>'Alex',
                'email'=>'alexa@alex.com',
                'password'=>'123456',
                // 'profession_id'=>null // en el caso twitter estaba en la siguiente assert. Me lo llevo de a user_profiles
            ]);
    
            $this->assertDatabaseHas('user_profiles',[
                'bio'=>'Programador de Laravel y Vue.js',
                //'twitter'=>null, // ojo hay que quitar esto de aqui porque no estoy validando Twiter sino profession_id, y además esta en a tabla users no en la tabla profiles, asi que lo pongo arriba
                'user_id' => User::findByEmail('alexa@alex.com')->id,
                'profession_id'=>null // Lo traigo de a user
            ]);
        }
        

    /** @test */
    function the_profession_field_is_requiered_if_otra_profession_is_null()
        {
            $this->withoutExceptionHandling();
    
            // asegurandome que paso el campo de profession_id en null
            $this->post('/usuarios/',$this->getValidData([
                'profession_id'=>null,
            ]))->assertRedirect(route('users.index'));

            $this->assertCredentials([
                'name'=>'Alex',
                'email'=>'alexa@alex.com',
                'password'=>'123456',
                // 'profession_id'=>null // en el caso twitter estaba en la siguiente assert. Me lo llevo de a user_profiles
            ]);
    
            $this->assertDatabaseHas('user_profiles',[
                'bio'=>'Programador de Laravel y Vue.js',
                //'twitter'=>null, // ojo hay que quitar esto de aqui porque no estoy validando Twiter sino profession_id, y además esta en a tabla users no en la tabla profiles, asi que lo pongo arriba
                'user_id' => User::findByEmail('alexa@alex.com')->id,
                'profession_id'=>null // Lo traigo de a user
            ]);
        }
        

    /** @test */
    function it_loads_the_edit_users_page()
    {
        $this->withoutExceptionHandling();

        $user=factory(User::class)->create();

        $this->get("/usuarios/{$user->id}/editar") //usuarios/5/editar OJO COMILLAS DOBLES
            ->assertStatus(200)
            ->assertViewIs('users.edit')
            ->assertSee("Editando detalle del usuario: $user->name")
            ->assertViewHas('user',function ($viewUser) use ($user) {
                return $viewUser->id=== $user->id;
            });
    }


    /** @test */
    function it_updates_a_user()
    {
        $this->withoutExceptionHandling();

        $user=factory(User::class)->create();

        $this->put("/usuarios/{$user->id}",[
            'name'=>'alex',
            'email'=>'alexa@alex.com',
            'password'=>'123456'
            ])->assertRedirect("/usuarios/{$user->id}");
            // ->assertRedirect(route('users.index',));

        $this->assertCredentials([
            'name'=>'alex',
            'email'=>'alexa@alex.com',
            'password'=>'123456'
        ]);

    }

   
    /** @test */
    function it_loads_the_edit_user_page()
    {
        $this->withoutExceptionHandling();

        $user=factory(User::class)->create();

        $this->get("/usuarios/{$user->id}/editar") //usuarios/5/editar
            ->assertStatus(200)
            ->assertViewIs('users.edit')
            ->assertSee('Editando detalle del usuario')
            ->assertViewHas('user',function($viewUser) use($user){
                return $viewUser->id===$user->id;
            });
    }

    /** @test */
    function the_name_is_required()
    {
        // $this->withoutExceptionHandling();

        $this->from('usuarios/nuevo')
            ->post('/usuarios/',$this->getValidData([
                'name'=>'',
            ]))
            ->assertRedirect('usuarios/nuevo')
            ->assertSessionHasErrors(['name'=>'El campo nombre es obligatorio']);

        // susituyo lo siguiente por la funcion assertDatabaseEmpty que he añadido en tests/TestCase.php
        // $this->assertDatabaseMissing('users',[
        //     'email'=>'alexa@alex.com',
        // ]);
        $this->assertDatabaseEmpty('users');
    }

    /** @test */
    function the_email_is_required()
    {
        $this->from('usuarios/nuevo')
            ->post('/usuarios/',$this->getValidData([
                'email'=>'',
            ]))
            ->assertRedirect('usuarios/nuevo')
            ->assertSessionHasErrors(['email']);

        // $this->assertDatabaseMissing('users',[
        //     'name'=>'alex',
        // ]);
        // en lugar de la comprobacion anterior verificando que no se ha creado un usuario con ese nombre
        // casi en mejor verificar el conteo de registros
        $this->assertEquals(0,User::count());
    }

    /** @test */
    function the_email_must_be_valid()
    {
        $this->from('usuarios/nuevo')
            ->post('/usuarios/',[
                'name'=>'alex',
                'email'=>'correo-no-valido',
                'password'=>'123456'
            ])
            ->assertRedirect('usuarios/nuevo')
            ->assertSessionHasErrors(['email']);
        $this->assertEquals(0,User::count());
    }

    /** @test */
    function the_email_must_be_unique()
    {
        // $this->withoutExceptionHandling();

        factory(User::class)->create([
            'email'=>'alex@alex.com'
        ]);

        // $this->from('usuarios/nuevo')
        //     ->post('/usuarios/',[
        //         'name'=>'alex',
        //         'email'=>'alex@alex.com',
        //         'password'=>'123456'
        //     ])
        //     ->assertRedirect('usuarios/nuevo')
        //     ->assertSessionHasErrors(['email']);

        //con getValidData
        $this->from('usuarios/nuevo')
        ->post('/usuarios/',$this->getValidData([
            'email'=>'alex@alex.com',
        ]))
        ->assertRedirect('usuarios/nuevo')
        ->assertSessionHasErrors(['email']);

        $this->assertEquals(1,User::count()); //aqui no puedo usar la funcion assertDatabaseEmpty('users'); porque la cuenta debe ser 1, no estar vacia
    }
    
    /** @test */
    function the_password_is_required()
    {
        // $this->from('usuarios/nuevo')
        //     ->post('/usuarios/',[
        //         'name'=>'alex',
        //         'email'=>'alex@alex.com',
        //         'password'=>''
        //     ])
        //     ->assertRedirect('usuarios/nuevo')
        //     ->assertSessionHasErrors(['password']);

        // con el metodo getValidData
        $this->from('usuarios/nuevo')
            ->post('/usuarios/',$this->getvalidData([
                'password'=>"",
            ]))
            ->assertRedirect('usuarios/nuevo')
            ->assertSessionHasErrors(['password']);

        // sustituyo la verifiacion por la de assertDatabaseEmpty
        // $this->assertEquals(0,User::count());
        $this->assertDatabaseEmpty('users');

    }

    /** @test */
    function the_profession_must_be_valid()
    {
        // $this->withoutExceptionHandling();
        $this->withExceptionHandling(); // es lo mismo que comentar la linea de arriba y ya esta
        // intento crear una profesion que no exista en la base de datos
        // con el metodo getValidData
        $this->from('usuarios/nuevo')
            ->post('/usuarios/',$this->getvalidData([
                'profession_id'=>"999", 
            ]))
            ->assertRedirect('usuarios/nuevo')
            ->assertSessionHasErrors(['profession_id']);

        // $this->assertEquals(0,User::count());
        $this->assertDatabaseEmpty('users');

    }
    
   /** @test */
   function only_selectable_professions_are_valid()
   {
        $nonselectableProfession= factory(Profession::class)->create([
            'selectable'=> false,
        ]);

        $this->handleValidationExceptions(); // es lo mismo que $this->withoutExceptionHandling();
    // intento crear una profesion que no exista en la base de datos
    // con el metodo getValidData
       $this->from('usuarios/nuevo')
           ->post('/usuarios/',$this->getvalidData([
               'profession_id'=>$nonselectableProfession->id, 
           ]))
           ->assertRedirect('usuarios/nuevo')
           ->assertSessionHasErrors(['profession_id']);

       // $this->assertEquals(0,User::count());
       $this->assertDatabaseEmpty('users');

   }

   /** @test */
   function only_not_deleted_profession_can_be_selected()
   {
    
        // Usando el metodo softDelete es decir en lugar de borrar marco como borrado pero no lo borro de la BBDD
        // para ello debo añadir el campo deleted_at a la base de datos en la migraction y al modelo tambien
    
        $deletedProfession  = factory(Profession::class)->create([
            'deleted_at'=> now()->format('Y.m.d'),  //una profesion eliminada tendría una fecha en el campo deleted_at, una no eliminada tendría este campo como null
        ]);

        $this->handleValidationExceptions(); // es lo mismo que $this->withoutExceptionHandling();
    // intento crear una profesion que no exista en la base de datos
    // con el metodo getValidData
       $this->from('usuarios/nuevo')
           ->post('/usuarios/',$this->getvalidData([
               'profession_id'=>$deletedProfession->id, 
           ]))
           ->assertRedirect('usuarios/nuevo')
           ->assertSessionHasErrors(['profession_id']);

       // $this->assertEquals(0,User::count());
       $this->assertDatabaseEmpty('users');

   }


    /** @test */
    function the_name_is_required_when_updating_a_user()
    {
        // $this->withoutExceptionHandling();

        $user= factory(User::class)->create();

        $this->from("/usuarios/{$user->id}/editar")
            ->put("/usuarios/{$user->id}",[
                'name'=>'',
                'email'=>'alexa@alex.com',
                'password'=>'123456'
            ])
            ->assertRedirect("usuarios/{$user->id}/editar")
            ->assertSessionHasErrors(['name']);

        $this->assertDatabaseMissing('users',['email'=>'alexa@alex.com']);
    }
    
    /** @test */
    function the_email_is_required_when_updating_a_user()
    {
        // $this->withoutExceptionHandling();

        $user= factory(User::class)->create();

        $this->from("/usuarios/{$user->id}/editar")
            ->put("/usuarios/{$user->id}",[
                'name'=>'alex',
                'email'=>'correo-no-valido',
                'password'=>'123456'
            ])
            ->assertRedirect("usuarios/{$user->id}/editar")
            ->assertSessionHasErrors(['email']);

        $this->assertDatabaseMissing('users',['name'=>'alex']);
    }

    /** @test */
    function the_email_must_be_valid_when_updating_a_user()
    {
        $this->from('usuarios/nuevo')
            ->post('/usuarios/',$this->getValidData([
                'email'=>'correo-no-valido',
            ]))
            ->assertRedirect('usuarios/nuevo')
            ->assertSessionHasErrors(['email']);
        $this->assertEquals(0,User::count());
    }

    /** @test */
    function the_email_can_stay_the_same_when_updating_a_user(){ 

        $user=factory(User::class)->create([
            'email'=>'alex@alex.com'
        ]);
        
        $this->from("usuarios/{$user->id}/editar") 
            ->put("usuarios/{$user->id}",[
                'name'=>'alex a',
                'email'=>'alex@alex.com',
                'password'=>'12345678'
            ])
            ->assertRedirect("usuarios/{$user->id}") ;//users.show

        $this->assertDatabaseHas('users',[
            'name'=>'alex a',
            'email'=>'alex@alex.com',
        ]);
    }
    
        /** @test */
    function the_email_must_be_unique_when_updating_the_user()
    {
        // $this->withoutExceptionHandling();

        factory(User::class)->create([
            'email'=>'existing-email@example.com', 
        ]);

        $user=factory(User::class)->create([
            'email'=>'alex@alex.com'
        ]);

        $this->from("usuarios/{$user->id}/editar")
            ->put("usuarios/{$user->id}",[
                'name'=>'alex',
                'email'=>'existing-email@example.com',
                'password'=>'123456'
            ])
            ->assertRedirect("usuarios/{$user->id}/editar")
            ->assertSessionHasErrors(['email']);
        
        //
    }

    /** @test */
    function the_password_is_optional_when_updating_a_user(){ 
        $oldPassword="clave_antigua";

        $user=factory(User::class)->create([
            'password'=>bcrypt($oldPassword)
        ]);
        
        $this->from("usuarios/{$user->id}/editar") 
            ->put("usuarios/{$user->id}",[
                'name'=>'alex',
                'email'=>'alex@alex.com',
                'password'=>''
            ])
            ->assertRedirect("usuarios/{$user->id}") ;//users.show

        $this->assertCredentials([
            'name'=>'alex',
            'email'=>'alex@alex.com',
            'password'=>$oldPassword, //muy importante
        ]);
    }

    /** @test **/
    function it_deletes_a_user()
    {
        $this->withoutExceptionHandling();

        $user=factory(User::class)->create();

        $this->delete("usuarios/{$user->id}")
            ->assertRedirect('usuarios');

        $this->assertDatabaseMissing('users', [
            'id'=>$user->id,
        ]);             

        // otra forma de validar
        // $this->assertSame(0,User::count()); //creo un usuario y luego lo elimino, así que el contador es el mismo
    }

    // /**
    //  * @return array
    //  */

    // protected function getValidData():array
    // {
    //     return[
    //         'name'=>'Alex',
    //         'email'=>'alexa@alex.com',
    //         'password'=>'123456',
    //         'bio'=>'Programador de Laravel y Vue.js',
    //         'twitter'=>'https://twitter.com/alexarregui',
    //     ];
    // }

    // si quiero la posibilidad de modificar algunos datos o eliminarlos lo hago de la siguiente manera
    protected function getValidData(array $custom=[])
    {

        $this->profession = factory(Profession::class)->create(); // para poder usar esto tengo que declarar la propiedad $profession de la clase al principio del fichero como una propiedad protected
            
        // array_filter filtra los campos que son null. De esta manera, si el campo de twitter es null lo elimino por completo
        // array_merge combina los atributos  que envío a la función con lo que tengo predefinidos
        // dando prioridad a los $custom
        // return array_filter(array_merge([
        return array_merge([
            'name'=>'Alex',
            'email'=>'alexa@alex.com',
            'password'=>'123456',
            'profession_id'=>$this->profession->id,
            'otraProfesion'=>'otra profesion distinta de la lista' ,
            'bio'=>'Programador de Laravel y Vue.js',
            'twitter'=>'https://twitter.com/alexarregui',
        ], $custom);
    }
}