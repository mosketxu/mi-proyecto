<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\User;
use App\Profession;
use App\Skill;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\DB;
use Illuminate\Foundation\Testing\RefreshDatabase;


/*
    En la lección https://styde.net/optimizar-y-reorganizar-pruebas-automatizadas-en-laravel/ modifico todo este fichero cambiando por varios archivos
    Además hago muchas otras cosas.
    Luego borraría este fichero, pero lo dejo como documentacion
    Aprovecho y en destino las dejo limpias
    Lo detallo paso a paso

    1) Crear una clase de prueba para cada una de las funcionalidades del módulo con: 
        php artisan make:test Admin/ListUsersTest
        php artisan make:test Admin/CreateUsersTest
        php artisan make:test Admin/UpdateUsersTest
        php artisan make:test Admin/DeleteUsersTest
        php artisan make:test Admin/ShowUsersTest
    
    2) corto it_shows_the_users_list y pego en ListUsersTest
        pongo el trait RefreshDatabase al principio 
        quito arriba la linea use Illuminate\Foundation\Testing\WithFaker;
        importo las clases que necesito en ese caso App\User
        hago lo mismo con
            it_shows_a_default_message_if_the_users_list_is_empty

    3/ mejoro la velocidad en TestCase.php veo trait CreatesApplication 
        abro el fichero CreatesApplication y justo después de que se haga el bootstrap de la apliacion colocamos el facade
        Has::setRounds(4);  ponemos 4 o 5         Mi version de laravel ya lo tenía así
        
        otra cosa sería generar una contraseña con tinker y dejarla en userFactory ya encriptadad en lugar de encriptarla cada vez
        
        bcrypt('secret');   $2y$10$Mi5V57ZEvrfrEvq5rRw23.HKE5ueV0SfPtBoOgZa77K91eh4tW52C

    4) copio it_displays_the_user_details en ShowUsersTest
        Recordar el trait RefreshDatabase e importar las clases User
    5) copio it_displays_a_404_error_if_the_user_is_not_found en ShowUsersTest

    6) copio en CreatesUsersTest los siguientes, importando las clases y el trait RefreshDatabase
        it_loads_the_new_users_page
        it_creates_a_new_user
        the_twitter_field_is_optional
        the_role_field_is_optional
        the_role_must_be_valid
        the_profession_id_field_is_optional
        the_name_is_required
        the_email_is_required
        the_email_must_be_valid
        the_email_must_be_unique
        the_password_is_required
        the_password_must_be_valid
        the_profession_must_be_valid
        only_not_deleted_professions_can_be_selected
        the_skills_must_be_an_array
        the_skills_must_be_valid

    7) copio en DeleteUsersTest
        it_deletes_a_user

    8)  copio en UpdateUsersTest pero ya quito el when porque al estar en el fichero correcto no hará falta
            it_loads_the_edit_user_page_when
            it_updates_a_user_when
            the_name_is_required_when
            the_email_must_be_valid_when
            the_email_must_be_unique_when
            the_users_email_can_stay_the_same_when
            the_password_is_optional_when
    9) copio en CreateUsersTest mi prueba de 
        the_profession_field_is_requiered_if_otra_profession_is_null y

    10) y todas las demas

    11) si ejecuto tengo un problema con el getValidData porque no lo encuentra
        temporalmente lo copio a final de CreateUSersTEst aunque luego lo muevo de ahí

        Dentro del getValidData no le gusta así que quita Esto lo hace en CreateUsersTest y UpdateUsersTest
            $this->profession=factory(Profession::class)->create()
            y dentro del array tambien lo quita aunque debe dejar ='' para que no de error porque en el formRequest veo que es un campo que debe estar present en la Rule
            por ello pongo ='' porque es lo que recibiría de la vista si no lo relleno.
        así que se lo lleva a la prueba donde se crea el usuario en CreateUsersTest en la prueba it_creates_a_new_user y en la UpdateUsersTest en it_updates_a_user no lo hace porque no hay ninguna prueba que haga referecia a Profesion de momento
        y crea la profesion directamente ahí porque  quiere comprobar que el perfil del usuario tiene una profesion asignada
        y lo envío en el post

        the_user_is_redirected_to_the_previous_page_when_the_validation_fails

    12) En este metodo, en lugar de assertDatabaseMissing uso assertDabaseEmpty
        El metodo assertDabaseEmpty lo creamos nosotros en una leccion en \tests\TestCase.php

    13) retoco UpdateUsersTest
        poner el trait Refreshdatabase
        importar las clases

            $this->profession = factory(Profession::class)->create(); // para poder usar esto tengo que declarar la propiedad $profession de la clase al principio del fichero como una propiedad protected

    14) en lugar de usar withoutExceptionHandling en mejor usar handlValidationExceptions donde toque
        pero por defecto poner en todas las pruebas withoutExceptionHandling
        En lugar de ponerlo en todas las pruebas voy a TestCase 
        y sobreescribo el método setup()
        para ello llamo al metodo setup de la clase padre y coloco withoutExceptionHandling para todas las pruebas

        Esto provocará errores. Paro en el primero con  t --stop-on-failure

        en casi todos los errores habrá que poner $this->handleValidationExceptions();

        en el caso de it_displays_a_404_error_if_the_user_is_not_found hace falta decirle
        que necesita el validador de excepciones este activo, porque es el validador de excepciones de laravel
        el que va a crear una redireccion a una pagina 404 cuando se lance una excepcion NOTFOUNDEXCEPTION

        Con esto lo que consigo es que en las pruebas no se arrojen excepciones
        y si se arrojan no quiero que Laravel la maneje, quiero ver el error en la consola
        a menos que explicitamente con el handleValiationExceptions le diga a Laravel que si la maneje

        busco los lugares donde he desactivado las excepciones y lo borro

    15) Hace una introduccion a collision y lo instalo, en principio da mejor los errores como en el caso de los seeders
        
        copio de https://github.com/nunomaduro/collision las lineas
        de phounit adapter en 

        <listeners>
            <listener class="NunoMaduro\Collision\Adapters\Phpunit\Listener" />
        </listeners>

        en phpunit.xml en cualquier sitio. Provoco un error para verlo

    16) Una mejora.
        En lugar de chequear en cada prueba que redirgimos a una pagina anterior y que la 
        tabla users se encuentre vacia
        creo a prueba en CreateUsersTest the_user_is_redirected_to_the_previous_page_when_the_validation_fails
        verifico que esto se cumpla en una prueba y lo quito de las demas.

    17) getValidData lo repito en varios ficheros y eso no mola. Lo quito de las pruebas y lo llevos a TestCase
        pero como los datos son propios del modulo de usuarios cortamos el array y lo reemplzamos por una
            propiedad llamada por ejemplo defaultData o mejor el metodo defaultData() que devolverá lo que contenga la propiedad defaultData
        Para ello decalramos en el propio TestCase la propiedad defaultdata como un array vacio y lo sobreeescribiremos
        en las clases en cuestion; UpdateUsersTest y CreateUsersTest

        Renombramos el metodo en TestCase de getValidData a withData

    18) POr ultimo vamos a crear una nueva clase que en realidad en un trait en la capeta tests
        TestHelpers
        y me llevo los metodos que he creado en TestCase
            assertDatabaseEmpty
            withData
            defaultData

        y en TestCase importo el trait TestHelper


*/

class UsersModuleTest extends TestCase
{
/*      use RefreshDatabase;

    protected $profession;
 */ 

/*     Genero un con nombre Joel y otro con nombre Carlos y 
    chequeo que el status sea 200: The request has succeeded. 
    que en la vista veo el texto "Listado de usuarios" en el archivo de rutas get('/usuarios') me envia al controlador users.index y este me envia a la vista
*/
    /** @test */
    // function it_shows_the_users_list()
    // {
    //     factory(User::class)->create([
    //         'name'=>'Joel',
    //     ]);

    //     factory(User::class)->create([
    //         'name'=>'Carlos',
    //     ]); 
        
    //     $this->get('/usuarios')
    //         ->assertStatus(200)
    //         ->assertSee('Listado de Usuarios')
    //         ->assertSee('Joel')
    //         ->assertSee('Carlos');
    // }


    /** @test */
/*     function it_shows_a_default_message_if_the_users_list_is_empty()
    {
        $this->get('/usuarios')
            ->assertStatus(200)
            ->assertSee('No hay usuarios registrados');
    } */

    /** @test */
/*     function it_displays_the_user_details()
    {
        // primero creo un usuario y lo ademas lo guardo en la vble $user
        $user=factory(User::class)->create([
            'name'=>'Alexander Arregui'
        ]);

        $this->get('/usuarios/'.$user->id)  //veo si lo encuentro 
            ->assertStatus(200)
            ->assertSee('Alexander Arregui');
    }
 */
    /** @test */
/*     function it_displays_a_404_error_if_the_user_is_not_found()
    {
        $this->get('/usuarios/999')
            ->assertStatus(404)
            ->assertSee('Página no encontrada');
    } */
    

    /** @test */
/*     function it_loads_the_new_users_page()
    {
        $this->withoutExceptionHandling();
        $profession=factory(Profession::class)->create();

        // $otraProfesion=factory(OtraProfession::class)->create();

        $skillA=factory(Skill::class)->create();
        $skillB=factory(Skill::class)->create();
        

        $this->get('/usuarios/nuevo')
            ->assertStatus(200)
            ->assertSee('Crear usuario')
            ->assertViewHas('professions',function($professions) use ($profession){ //verifico que la vista tiene la variable profession y que es la que le estoy pasando aqui
                return $professions->contains($profession);                                //si la funcion anonima retorna verdadero la prueba pasa, sino no pasa
            })
            // ->assertViewHas('otrasProfesiones',function($otrasProfesiones) use ($otraProfesion){ 
            //     return $otrasProfesiones->contains($otraProfesion);                              
            // })
            ->assertViewHas('skills',function($skills) use ($skillA,$skillB){ //verifico que la vista tiene la variable profession y que es la que le estoy pasando aqui
                return $skills->contains($skillA) && $skills->contains($skillB);                                //si la funcion anonima retorna verdadero la prueba pasa, sino no pasa
            });
        }
     */


    /** @test */
/*
    function it_creates_a_new_user()
    {
        // $this->post('/usuarios/',[
        //     'name'=>'Alex',
        //     'email'=>'alex@alex.com',
        //     'password'=>'123456'
        // ])->assertSee('Procesando información...');
        

        //Para no repetir código y que quede todo más limpio
        // me llevo lo comentado siguiente a una funcion en este mismo fichero llamada getValidData al final

        // $this->post('/usuarios/',[
        //     'name'=>'Alex',
        //     'email'=>'alexa@alex.com',
        //     'password'=>'123456',
        //     'bio'=>'Programador de Laravel y Vue.js',
        //     'twitter'=>'https://twitter.com/alexarregui',
        //     ])->assertRedirect(route('users.index'));

        $this->withoutExceptionHandling();

        // para poder pasar los skills los tengo que generar primero

        $skillA=factory(Skill::class)->create();
        $skillB=factory(Skill::class)->create();
        $skillC=factory(Skill::class)->create();

        $this->post('/usuarios', $this->getValidData([
            'skills'=>[$skillA->id,$skillB->id],
        ]))->assertRedirect(route('users.index'));;

        // $this->assertDatabaseHas('users',[ 'name'=>'Alex', 'email'=>'alexa@alex.com', 'password'=>'123456']);
        // lo mismo con assertCredentials
        $this->assertCredentials([
            'name'=>'Alex',
            'email'=>'alexa@alex.com',
            'password'=>'123456',
            // 'profession_id'=>$this->profession->id, // lo llevo a user_profiles
            'role'=>'user',
        ]);

        $user=User::findByEmail('alexa@alex.com');

        $this->assertDatabaseHas('user_profiles',[
            'bio'=>'Programador de Laravel y Vue.js',
            'twitter'=>'https://twitter.com/alexarregui',
            'user_id' => $user->id,
            'profession_id'=>$this->profession->id, // lo traigo de user
            'otraProfesion'=>'otra profesion distinta de la lista',
            ]);

        //chequeo que la llave foranea de user_id corresponda con la habilidad que fue creada con el modelo factory
        $this->assertDatabaseHas('user_skill', [ 
            'user_id'=>$user->id,
            'skill_id'=>$skillA->id,
        ]);

        //como un usuario puede tener muchas habilidades y estoy enviando la peticion post con 2 habilidades espero un segundo registro
        $this->assertDatabaseHas('user_skill', [ //chequeo que la llave foranea de user_id corresponda con la habilidad que fue creada con el modelo factory
            'user_id'=>$user->id,
            'skill_id'=>$skillB->id,
        ]);

        // la tercera habilidad que he creado no la voy a enviar por lo que no debería estar en la bbdd. Por eso pongo Missing
        $this->assertDatabaseMissing('user_skill', [ 
            'user_id'=>$user->id,
            'skill_id'=>$skillC->id,
        ]);
    }
 */

         /** @test */
/*          function the_twitter_field_is_optional()
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
    */ 


        //si paso el role en nulo espero que se cree con el role de user
        //para que la prueba pase tengo que poner el Rules que role es nullable
        // y ademas tengo que decirle cual es el valor por defecto si no informo del campo
        // $user->role=$data['role'] ?? 'user';  
        // ademas si uso asserCredentials necesito pasar el password
        // asi que uso el assertDabaseHas
        /** @test */
    
    /*     function the_role_field_is_optional()
        {
            $this->withoutExceptionHandling();
    
            $this->post('/usuarios/',$this->getValidData([
                'role'=>null,
            ]))->assertRedirect(route('users.index'));

            // si uso el metodo assertCredentiasl tengo que pasar el password
            //mejor uso el assertDatabaseHAs
            // $this->assertCredentials([  
            //     'email'=>'alexa@alex.com',
            //     'role'=>'user',
            // ]);
            $this->assertDatabaseHas('users',[
                'email'=>'alexa@alex.com',
                'role'=>'user',
            ]);
        }
 */
        //si meto un role que no existe espero un error en ese campo 
        // y verifico que no se haya añadido nada a la base de datos con el assertDatabaseEmpty
        // si ejecuto la prueba vemos que no ningun error, buno dice false es true y la prueba no pasa
        // para que pase modifico las rules y digo que 'role'=>'in:admin,user'
        // da error ValidationException: The given data was invalid. Es decir, que no atrapa la excepcion Fuerzo a que si tenga en cuenta las excepciones
        // aunque la prueba pasa lo le gusta usar lo de 'in:admin,user' porque poddemo usar los roles en otras partes
        // lo que hace Duilio es crear una nueva clase llamada Role en el directorio principal es decir en App\
        // recordar que hay que llamar a la clase con el use
        
        /** @test */
/*         function the_role_must_be_valid()
        {
            // $this->withoutExceptionHandling();
            $this->handleValidationExceptions();
    
            $this->post('/usuarios/',$this->getValidData([
                'role'=>'role-no*valido',
            ]))->assertSessionHasErrors('role');

            // $this->assertDatabasemissing('users',[
            //     'email'=>'alexa@alex.com',
            // ]);

            $this->assertDatabaseEmpty('users');
        }
 */

     /** @test */
/*      function the_profession_field_is_optional()
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
 */


     /** @test */
/*       function the_name_is_required()
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
 */ 
     /** @test */
/*      function the_email_is_required()
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
 */ 
     /** @test */
/*      function the_email_must_be_valid()
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
  */
     /** @test */
/*      function the_email_must_be_unique()
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
 */ 


     /** @test */
/*      function the_password_is_required()
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
  */
     /** @test */
/*      function the_password_must_be_valid()
     {
         $this->withExceptionHandling(); 
         // Pruebo que pasa si envío un password que no cumpla las reglas, por ejemplo que tenga menos de 6 caracteres
         $this->from('usuarios/nuevo')
             ->post('/usuarios/',$this->getvalidData([
                 'password'=>"12345",    
             ]))
             ->assertRedirect('usuarios/nuevo')
             ->assertSessionHasErrors(['password']);
         $this->assertDatabaseEmpty('users');
     }
  */
     /** @test */
/*      function the_profession_must_be_valid()
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
 */     

     /** @test */
/*     function only_not_deleted_profession_can_be_selected()
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
 */

    /** @test */
/*     function only_selectable_professions_are_valid()
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
 */ 
 
    /* mando algo que no sea un array para que de error. Luego pongo una regla para evitarlo y asi la prueba pasa. Lo compruebo verificando que no se ha creado nada */
    /** @test */
/*     function the_skills_must_be_an_array()
    {
        // $this->withoutExceptionHandling(); 
        $this->handleValidationExceptions();

        $this->from('usuarios/nuevo')
            ->post('/usuarios/',$this->getvalidData([
                'skills'=>"PHP,JS", //el usuario manipulando chrome con F12 manda una cadena de texto en lugar de un array
            ]))
            ->assertRedirect('usuarios/nuevo')
            ->assertSessionHasErrors(['skills']); //en ese caso esperariamos ver un error en el campo skills

        // $this->assertEquals(0,User::count());
        $this->assertDatabaseEmpty('users');

    }
 */
        /** @test */
/*         function the_skills_must_be_valid()
        {
            // $this->withoutExceptionHandling(); 
            $this->handleValidationExceptions();

            $skillA = factory(Skill::class)->create() ;
            $skillB = factory(Skill::class)->create() ;
    
            $this->from('usuarios/nuevo')
                ->post('/usuarios/',$this->getvalidData([
                    'skills'=>[$skillA->id, $skillB->id + 1] , //envio el id de la habilidad A y el de la habilidad B mas 1. Debería dar error porque este segundo no existe
                ]))                                            //entonces dara un error de foreign key no valido
                ->assertRedirect('usuarios/nuevo')
                ->assertSessionHasErrors(['skills']); //en ese caso esperariamos ver un error en el campo skills
    
            // $this->assertEquals(0,User::count());
            $this->assertDatabaseEmpty('users');
    
        }
 */    

     /** @test */
/*      function it_loads_the_edit_users_page()
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
 */ 

     /** @test */
/*      function it_updates_a_user()
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
 */ 

     /** @test */
/*      function the_name_is_required_when_updating_a_user()
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
 */ 


     /** @test */
/*      function the_email_must_be_valid_when_updating_a_user()
     {
         $this->from('usuarios/nuevo')
             ->post('/usuarios/',$this->getValidData([
                 'email'=>'correo-no-valido',
             ]))
             ->assertRedirect('usuarios/nuevo')
             ->assertSessionHasErrors(['email']);
         $this->assertEquals(0,User::count());
     }
 */ 

     /** @test */
/*      function the_email_is_required_when_updating_a_user()
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
 */ 

     /** @test */
/*      function the_email_can_stay_the_same_when_updating_a_user(){ 
 
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
 */     

     /** @test */
/*     function the_email_must_be_unique_when_updating_the_user()
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
    }
 
 */ 

     /** @test */
/*      function the_password_is_optional_when_updating_a_user(){ 
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
 */
    /** @test */
/*     function the_profession_field_is_requiered_if_otra_profession_is_null() //esta no la se hacer
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
 */        
   

    /** @test **/
/*     function it_deletes_a_user()
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
 */
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
 /*    protected function getValidData(array $custom=[])
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
            'role'=>'user',  //por defecto digo que es un usuario normal, no admin
        ], $custom);
    }
 */

    /** @test */
    public function Something()
    {
    // Optional: Test anything here, if you want.
        $this->assertTrue(true, 'This should already work.');

    // Stop here and mark this test as incomplete.
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }
}