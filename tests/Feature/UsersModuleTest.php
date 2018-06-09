<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\User;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\DB;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UsersModuleTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    function it_shows_the_users_list()
    {
        //Usuario con nombre Joel y Carlos para que funcione el test
        factory(User::class)->create([
            'name'=>'Joel',
            // 'website'=>'miweb.com',
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
    function it_loads_the_new_users_page()
    {
        $this->get('/usuarios/nuevo')
            ->assertStatus(200)
            ->assertSee('Crear nuevo usuario');
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
    function it_displays_a_404_error_if_the_user_is_not_found()
    {
        $this->get('/usuarios/999')
            ->assertStatus(404)
            ->assertSee('Página no encontrada');
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

        $this->post('/usuarios/',[
            'name'=>'Alex',
            'email'=>'alexa@alex.com',
            'password'=>'123456'
            ])->assertRedirect(route('users.index'));

        // $this->assertDatabaseHas('users',[
        //     'name'=>'Alex',
        //     'email'=>'alexa@alex.com',
        //     'password'=>'123456'
        // ]);

        $this->assertCredentials([
            'name'=>'Alex',
            'email'=>'alexa@alex.com',
            'password'=>'123456'
        ]);

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
            ->post('/usuarios/',[
                'name'=>'',
                'email'=>'alexa@alex.com',
                'password'=>'123456'
            ])
            ->assertRedirect('usuarios/nuevo')
            ->assertSessionHasErrors(['name'=>'El campo nombre es obligatorio']);

        $this->assertDatabaseMissing('users',[
            'email'=>'alexa@alex.com',
        ]);
    }

    /** @test */
    function the_email_is_required()
    {
        $this->from('usuarios/nuevo')
            ->post('/usuarios/',[
                'name'=>'alex',
                'email'=>'',
                'password'=>'123456'
            ])
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

        $this->from('usuarios/nuevo')
            ->post('/usuarios/',[
                'name'=>'alex',
                'email'=>'alex@alex.com',
                'password'=>'123456'
            ])
            ->assertRedirect('usuarios/nuevo')
            ->assertSessionHasErrors(['email']);
        
        $this->assertEquals(1,User::count());
    }
    
    /** @test */
    function the_password_is_required()
    {
        $this->from('usuarios/nuevo')
            ->post('/usuarios/',[
                'name'=>'alex',
                'email'=>'alex@alex.com',
                'password'=>''
            ])
            ->assertRedirect('usuarios/nuevo')
            ->assertSessionHasErrors(['password']);

        $this->assertEquals(0,User::count());
    }
    /** @test */
    function the_password_has_six_char_or_more()
    {
        self::markTestIncomplete();
        return;

        $this->from('usuarios/nuevo')
            ->post('/usuarios/',[
                'name'=>'alex',
                'email'=>'alex@alex.com',
                'password'=>'12345'
            ])
            ->assertRedirect('usuarios/nuevo')
            ->assertSessionHasErrors(['password']);

        $this->assertEquals(0,User::count());
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
    function the_password_is_required_when_updating_a_user()
    {
        $user=factory(User::class)->create();
        
        $this->from("usuarios/{$user->id}/editar")
            ->put("usuarios/{$user->id}",[
                'name'=>'alex',
                'email'=>'alex@alex.com',
                'password'=>''
            ])
            ->assertRedirect("usuarios/{$user->id}/editar")
            ->assertSessionHasErrors(['password']);

        $this->assertDatabaseMissing('users',['email'=>'alex@alex.com']);
    }

    /** @test */
    function the_password_has_six_char_or_more_when_updating_a_user()
    {
        self::markTestIncomplete();
        return;

        

        $this->from('usuarios/nuevo')
            ->post('/usuarios/',[
                'name'=>'alex',
                'email'=>'alex@alex.com',
                'password'=>'12345'
            ])
            ->assertRedirect('usuarios/nuevo')
            ->assertSessionHasErrors(['password']);

        $this->assertEquals(0,User::count());
    }
}