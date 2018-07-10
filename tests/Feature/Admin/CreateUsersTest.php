<?php

namespace Tests\Feature\Admin;

use App\{Profession,Skill,User};
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CreateUsersTest extends TestCase
{
    use RefreshDatabase;

    protected $defaultData=[
        'name'=>'Alex',
        'email'=>'alexa@alex.com',
        'password'=>'123456',
        'bio'=>'Programador de Laravel y Vue.js',
        'profession_id'=>'',
        'otraProfesion'=>'otra profesion distinta de la lista' ,
        'twitter'=>'https://twitter.com/alexarregui',
        'role'=>'user',
    ];


    /** @test */
    function it_loads_the_new_users_page()
    {
        $profession=factory(Profession::class)->create();

        $skillA=factory(Skill::class)->create();
        $skillB=factory(Skill::class)->create();

        $this->get('/usuarios/nuevo')
            ->assertStatus(200)
            ->assertSee('Crear usuario')
            ->assertViewHas('professions',function($professions) use ($profession){ //verifico que la vista tiene la variable profession y que es la que le estoy pasando aqui
                return $professions->contains($profession);                                //si la funcion anonima retorna verdadero la prueba pasa, sino no pasa
            })
            ->assertViewHas('skills',function($skills) use ($skillA,$skillB){ //verifico que la vista tiene la variable profession y que es la que le estoy pasando aqui
                return $skills->contains($skillA) && $skills->contains($skillB);                                //si la funcion anonima retorna verdadero la prueba pasa, sino no pasa
            });
    }

    /** @test */
    function it_creates_a_new_user()
    {
        $profession=factory(Profession::class)->create();

        $skillA=factory(Skill::class)->create();
        $skillB=factory(Skill::class)->create();
        $skillC=factory(Skill::class)->create();

        $this->post('/usuarios', $this->withData([
            'skills'=>[$skillA->id,$skillB->id],
            'profession_id'=>$profession->id,
        ]))->assertRedirect(route('users.index'));;

        $this->assertCredentials([
            'name'=>'Alex',
            'email'=>'alexa@alex.com',
            'password'=>'123456',
            'role'=>'user',
        ]);

        $user=User::findByEmail('alexa@alex.com');

        $this->assertDatabaseHas('user_profiles',[
            'bio'=>'Programador de Laravel y Vue.js',
            'twitter'=>'https://twitter.com/alexarregui',
            'user_id' => $user->id,
            'profession_id'=>$profession->id,
            'otraProfesion'=>'otra profesion distinta de la lista',
            ]);

        $this->assertDatabaseHas('user_skill', [ 
            'user_id'=>$user->id,
            'skill_id'=>$skillA->id,
        ]);

        $this->assertDatabaseHas('user_skill', [
            'user_id'=>$user->id,
            'skill_id'=>$skillB->id,
        ]);

        $this->assertDatabaseMissing('user_skill', [ 
            'user_id'=>$user->id,
            'skill_id'=>$skillC->id,
        ]);
    }

    /** @test */
    function the_twitter_field_is_optionalp()
    {
        $this->handleValidationExceptions();

        $this->post('/usuarios/',$this->withData([
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
    function the_role_field_is_optional()
    {
  
        $this->post('/usuarios/',$this->withData([
            'role'=>null,
        ]))->assertRedirect(route('users.index'));

        $this->assertDatabaseHas('users',[
                'email'=>'alexa@alex.com',
                'role'=>'user',
        ]);
    }

    /** @test */
    function the_role_must_be_valid()
    {
        $this->handleValidationExceptions();
    
        $this->post('/usuarios/',$this->withData([
            'role'=>'role-no*valido',
        ]))->assertSessionHasErrors('role');

        $this->assertDatabaseEmpty('users');
    }

    /** @test */
    function the_profession_field_is_optional()
    {
        $this->post('/usuarios/',$this->withData([
            'profession_id'=>null,
        ]))->assertRedirect(route('users.index'));

        $this->assertCredentials([
            'name'=>'Alex',
            'email'=>'alexa@alex.com',
            'password'=>'123456',
        ]);

        $this->assertDatabaseHas('user_profiles',[
            'bio'=>'Programador de Laravel y Vue.js',
            'user_id' => User::findByEmail('alexa@alex.com')->id,
            'profession_id'=>null // Lo traigo de a user
        ]);
    }

    /** @test */
    function the_profession_must_be_valid()
    {
        $this->withExceptionHandling(); 

        $this->post('/usuarios/',$this->withData([
                'profession_id'=>"999", 
            ]))
            ->assertSessionHasErrors(['profession_id']);

        $this->assertDatabaseEmpty('users');
    }

    // La intencion de esta prueba es que si nosotros enviamos una peticion para enviar el 
    // usuario con datos invalidos el usuario sea redirigido de vuelta
    // entonces puedo quitar el from('usuarios/nuevo') y el assertRedirect()
    /** @test */
    function the_user_is_redirected_to_the_previous_page_when_the_validation_fails()
    {
        $this->handleValidationExceptions();

        $this->from('usuarios/nuevo')
            ->post('/usuarios/',[])
            ->assertRedirect('usuarios/nuevo');

        $this->assertDatabaseEmpty('users');
    }


    /** @test */
    function the_name_is_required()
    {
        $this->handleValidationExceptions();

        $this->post('/usuarios/',$this->withData([
                'name'=>'',
            ]))
            ->assertSessionHasErrors(['name'=>'El campo nombre es obligatorio']);

        $this->assertDatabaseEmpty('users');
    }

    /** @test */
    function the_email_is_required()
    {
        $this->handleValidationExceptions();

        $this->post('/usuarios/',$this->withData([
                'email'=>'',
            ]))
            ->assertSessionHasErrors(['email']);

        $this->assertEquals(0,User::count());
    }

    /** @test */
    function the_email_must_be_valid()
    {
        $this->handleValidationExceptions();

        $this->post('/usuarios/',[
                'name'=>'alex',
                'email'=>'correo-no-valido',
                'password'=>'123456'
            ])
            ->assertSessionHasErrors(['email']);
        $this->assertEquals(0,User::count());
    }

    /** @test */
    function the_email_must_be_unique()
    {
        $this->handleValidationExceptions();

        factory(User::class)->create([
            'email'=>'alex@alex.com'
        ]);

        $this->post('/usuarios/',$this->withData([
            'email'=>'alex@alex.com',
        ]))
        ->assertSessionHasErrors(['email']);

        $this->assertEquals(1,User::count());
    }

    /** @test */
    function the_password_is_required()
    {
        $this->handleValidationExceptions();

        $this->post('/usuarios/',$this->withData([
                'password'=>"",
            ]))
            ->assertSessionHasErrors(['password']);

        $this->assertDatabaseEmpty('users');
    }

    /** @test */
    function the_password_must_be_valid()
    {
        $this->withExceptionHandling(); 

        $this->post('/usuarios/',$this->withData([
                'password'=>"12345",    
            ]))
            ->assertSessionHasErrors(['password']);
        $this->assertDatabaseEmpty('users');
    }

    /** @test */
    function only_not_deleted_profession_can_be_selected()
    {
    
        $deletedProfession  = factory(Profession::class)->create([
            'deleted_at'=> now()->format('Y.m.d'),
        ]);

        $this->handleValidationExceptions();

        $this->post('/usuarios/',$this->withData([
                'profession_id'=>$deletedProfession->id, 
            ]))
            ->assertSessionHasErrors(['profession_id']);

        $this->assertDatabaseEmpty('users');
    }

    /** @test */
   function only_selectable_professions_are_valid()
   {
        $nonselectableProfession= factory(Profession::class)->create([
            'selectable'=> false,
        ]);

        $this->handleValidationExceptions(); 
       $this->post('/usuarios/',$this->withData([
               'profession_id'=>$nonselectableProfession->id, 
           ]))
           ->assertSessionHasErrors(['profession_id']);

       $this->assertDatabaseEmpty('users');
   }



    /** @test */
    function the_skills_must_be_an_array()
    {
        $this->handleValidationExceptions();

        $this->post('/usuarios/',$this->withData([
                'skills'=>"PHP,JS",
            ]))
            ->assertSessionHasErrors(['skills']); 

        $this->assertDatabaseEmpty('users');
    }

    /** @test */
    function the_skills_must_be_valid()
    {
        $this->handleValidationExceptions();

        $skillA = factory(Skill::class)->create() ;
        $skillB = factory(Skill::class)->create() ;

        $this->post('/usuarios/',$this->withData([
                'skills'=>[$skillA->id, $skillB->id + 1] ,
            ]))
            ->assertSessionHasErrors(['skills']); 

        $this->assertDatabaseEmpty('users');
    }

    /** @test */
    function the_profession_field_is_requiered_if_otra_profession_is_null() //esta no la se hacer
    {
        $this->post('/usuarios/',$this->withData([
            'profession_id'=>null,
        ]))->assertRedirect(route('users.index'));

        $this->assertCredentials([
            'name'=>'Alex',
            'email'=>'alexa@alex.com',
            'password'=>'123456',
        ]);

        $this->assertDatabaseHas('user_profiles',[
            'bio'=>'Programador de Laravel y Vue.js',
            'user_id' => User::findByEmail('alexa@alex.com')->id,
            'profession_id'=>null // Lo traigo de a user
        ]);
    }
}
