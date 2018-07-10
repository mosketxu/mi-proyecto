<?php

namespace Tests\Feature\Admin;

use App\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ShowUsersTest extends TestCase
{

    use RefreshDatabase;

    /** @test */
    function it_displays_the_user_details()
    {
        $user=factory(User::class)->create([
            'name'=>'Alexander Arregui'
        ]);

        $this->get('/usuarios/'.$user->id)  //veo si lo encuentro 
            ->assertStatus(200)
            ->assertSee('Alexander Arregui');
    }

    /** @test */
    function it_displays_a_404_error_if_the_user_is_not_found()
    {
        $this->withExceptionHandling();

        $this->get('/usuarios/999')
            ->assertStatus(404)
            ->assertSee('Página no encontrada');
    }
        
}
