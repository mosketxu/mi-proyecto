<?php

namespace Tests\Feature\Admin;

use App\{User,Profession};
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UpdateUsersTest extends TestCase
{

    use RefreshDatabase;

    protected $defaultData=[
        'name'=>'Alex',
        'email'=>'alexa@alex.com',
        'password'=>'123456',
        'bio'=>'Programador de Laravel y Vue.js',
        'otraProfesion'=>'otra profesion distinta de la lista' ,
        'profession_id'=>'',
        'twitter'=>'https://twitter.com/alexarregui',
        'role'=>'user',
    ];



    /** @test */
    function it_loads_the_edit_users_page()
    {
        $user=factory(User::class)->create();

        $this->get("/usuarios/{$user->id}/editar")
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

        $this->profession = factory(Profession::class)->create(); // para poder usar esto tengo que declarar la propiedad $profession de la clase al principio del fichero como una propiedad protected


        $user=factory(User::class)->create();

        $this->put("/usuarios/{$user->id}",[
            'name'=>'alex',
            'email'=>'alexa@alex.com',
            'password'=>'123456'
            ])->assertRedirect("/usuarios/{$user->id}");

        $this->assertCredentials([
            'name'=>'alex',
            'email'=>'alexa@alex.com',
            'password'=>'123456'
        ]);
    }
    
    /** @test */
     function the_name_is_required()
    {
        $this->handleValidationExceptions();

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
     function the_email_must_be_valid()
    {

        $this->handleValidationExceptions();

        $this->from('usuarios/nuevo')
             ->post('/usuarios/',$this->withData([
                 'email'=>'correo-no-valido',
             ]))
             ->assertRedirect('usuarios/nuevo')
             ->assertSessionHasErrors(['email']);
         $this->assertEquals(0,User::count());
     }

    /** @test */
     function the_email_is_required()
     {
        $this->handleValidationExceptions();

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
    function the_email_can_stay_the_same(){ 

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
    function the_email_must_be_unique()
    {
        $this->handleValidationExceptions();

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

    /** @test */
    function the_password_is_optional(){ 
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
            ->assertRedirect("usuarios/{$user->id}") ;

        $this->assertCredentials([
            'name'=>'alex',
            'email'=>'alex@alex.com',
            'password'=>$oldPassword,
        ]);
    }
}