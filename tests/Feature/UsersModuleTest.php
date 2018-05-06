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
        $this->get('/usuarios/5/edit')
            ->assertStatus(200)
            ->assertSee('Editando detalle del usuario: 5');
    }

}
