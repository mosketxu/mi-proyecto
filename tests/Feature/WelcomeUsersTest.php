<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class WelcomeUsersTest extends TestCase
{
    /** @test      */
    public function it_welcomes_users_with_nickname()
    {
        $this->get('/saludo/alex/mosketxu')
            ->assertStatus(200)
            ->assertSee('Hola Alex, tu nick es mosketxu');
    }

    /** @test      */
    public function it_welcomes_users_without_nickname()
    {
        $this->get('/saludo/alex')
            ->assertStatus(200)
            ->assertSee('Hola Alex,');
    }
}
