<?php

namespace Tests\Browser\Admin;

use Tests\DuskTestCase;
use Laravel\Dusk\Browser;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use App\{Profession, Skill, User};

class CreateUserTest extends DuskTestCase
{

    use DatabaseMigrations;
    /** @test */

    function a_user_can_be_created()
    {
        //Primera prueba tonta y basica
        //para llamar al navegador se usa el metodo browser
        //en el puedo poner tantos objetos browser como quiera
        //debo instanciar la clase use Laravel\Dusk\Browser;
        //lo de poner Browser delante de $browser es porque en alguno IDE funciona el autocompletado y muestra los metodos. En VisualCode parece que no
/*         $this->browse(function(Browser $browser,$browser2,$browser3) {
            //no envio la peticion por POST sino como si estuviera en el navegador, así que pongo la ruta
            // con esta prueba veo que en el navegador de la url usuario/nuevo hay una etiqueta h4 que tiene el texto Crear usuario
            // si no pongo     use DatabaseMigrations; da error. Se puede ver en los screenshoots
            $browser->visit('usuarios/nuevo')
                ->assertSeeIn('h4','Crear usuario'); 
        });
*/

        //Segunda prueba mas interesante. Simulo que introduzco datos en el formulario

        //para poder elegir una profesion simulando un desplegable primero debo crear una profesion, y lo hago con model factory
        $profession=factory(Profession::class)->create(); // y la paso con el use
        //lo mismo para los skills para los que debo pasar el id. Los genero con model factory
        $skillA=factory(Skill::class)->create();
        $skillB=factory(Skill::class)->create();

        $this->browse(function(Browser $browser,$browser2,$browser3) use($profession, $skillA,$skillB)
        {
            $browser->visit('usuarios/nuevo')
                ->type('name', 'Alex Arregui')
                ->type('email','mosketxu@gmail.com')
                ->type('password','1234567')
                ->type('bio','Programador')
                ->select('profession_id',$profession->id)
                ->type('twitter','https://twitter.com/alexarregui')
                ->check("skills[{$skillA->id}]")
                ->check("skills[{$skillB->id}]")
                ->radio('role','user')  //marcamos el radio que tiene el valor user en el id con nombre role
                ->press('Crear usuario') //es como si pulsara el boton que tiene el tecto Crear usuario
                ->assertPathIs('/usuarios') // verifico que me ha redireccionado a usuarios
                ->assertSee('Alex') 
                ->assertSee('mosketxu@gmail.com') ;
        });

                //ahora usamos un segundo navegador en el que si usamos la url usuarios vemos que existe el usuario que acabo de crear

            // esto lo podría hacer si uso un segundo browser, pero no es necesario en este ejemplo porque lo hago en la prueba de arriba
/*                 $browser2->visit('/usuarios') 
                ->assertSee('Alex') 
                ->assertSee('mosketxu@gmail.com') ;
*/     
        //tambien puedo usar las pruebas de las base de datos de Feature. LAs copio de CreateUsersTest modificando los datos
        //pero deben estar fuera de la cadena BRowser
/*         $this->assertCredentials([
            'name' => 'Alex Arregui',
            'email' => 'mosketxu@gmail.com',
            'password' => '1234567',
            'role' => 'user',
        ]);
        $user = User::findByEmail('duilio@styde.net');
        $this->assertDatabaseHas('user_profiles', [
            'bio' => 'Programador',
            'twitter' => 'https://twitter.com/alexarregui',
            'user_id' => $user->id,
            'profession_id' => $profession->id,
        ]);
        $this->assertDatabaseHas('user_skill', [
            'user_id' => $user->id,
            'skill_id' => $skillA->id,
        ]);
        $this->assertDatabaseHas('user_skill', [
            'user_id' => $user->id,
            'skill_id' => $skillB->id,
        ]); */
    }
}