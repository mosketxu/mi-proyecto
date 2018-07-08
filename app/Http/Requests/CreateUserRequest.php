<?php

namespace App\Http\Requests;

use App\User;
use App\Role;
use Illuminate\Support\Facades\DB; //me hace falta para el metodo createUser que he traido del modelo User
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreateUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name'=>'required',
            'email'=>'required|email|unique:users,email',
            'password'=>['required','string','min:6',], 
            //-'role'=>'in:admin,user', // en lugar de esto hemos creado una nueva class en App/ llamada Role donde tengo el metodo getList que me da los roles
            // implode convierte el array en una cadena de texto, con lo que el resultado es el mismo
            //'role'=>['nullable','in:'.implode(',',Role::getList())],  //pero tambien podemos hacerlo con sintaxis orientada a obejto con la clase Rule
            'role'=>['nullable',Rule::in(Role::getList()) ],
            // si quiero poner coondiciones a la contraseña uso expresiones regulares.
            // 'password'=>['required','string','min:6','regex:/^(?=\w*\d)(?=\w*[A-Z])(?=\w*[a-z])\S{8,16}$/' ], //La contraseña debe tener al entre 8 y 16 caracteres, al menos un dígito, al menos una minúscula y al menos una mayúscula.             NO puede tener otros símbolos.
            'bio'=>'required', //para validar esto hacer una prueba con TDD
            'twitter'=>['nullable','present','url'], //si pongo 'present' debo quitar array_filter del getValidaData, sino no pasan las pruebas
            //  'twitter'=>['nullable','url'], //para validar esto hacer una prueba con TDD
            //'profession_id'=>'', //si dejo esta linea así cuando ejecuto la prueba da un error de base de datos, pero quiero atajar el error antes así que pongo la siguiente linea
            // 'profession_id'=>'exists:professions,id',  // para añadir una condicion de que solo pueda seleccionar profesiones selecccionables siguiente línea con sintaxis nueva para cosas mas complejas
            // indico que quiero que la profession este presente en el campo id  la tabla professions
            // y que ademas sea selectable
            // 'profession_id'=>Rule::exists('professions','id')->where('selectable',true), //si no pongo el where falla la prueba only_selectable_professions_are_valid
            // si ademas quiero que solo se puedan seleccionar las que no están borradas con el softDelete
            // 'profession_id'=>[
            //     'nullable', 'present', // si pongo present debo quitar array_filter del getValidaData, sino no pasan las pruebas
            //     Rule::exists('professions','id')->where('selectable',true)->whereNull('deleted_at')
            // ], //si no pongo el where falla la prueba only_selectable_professions_are_valid
            'profession_id'=>[
                'nullable', 
                'present', // si pongo present debo quitar array_filter del getValidaData, sino no pasan las pruebas
                Rule::exists('professions','id')->where('selectable',true)->whereNull('deleted_at'),
                'required_if:otraProfesion,==,""'
            ], //si no pongo el where falla la prueba only_selectable_professions_are_valid
//            'otraProfesion'=>'nullable',
            'otraProfesion'=>[
                'nullable', 
                'required_if:profession_id,==,""',
            ],
            'skills'=>[
                'array',    //como espera un array lo debo marcar
                Rule::exists('skills','id')    // sin esta regla falla la prueba the_skills_must_be_valid porque da un error de llave foranea en la bbdd
            ],
        ];
    }

    public function messages(){
        return [
            'name.required'=>'El campo nombre es obligatorio',
            'otraProfesion.required_if'=>"Obligatorio si profesion no esta seleccionado",
            'profession_id.required_if'=>"Obligatorio si otraprofesion no esta seleccionado",
        ];
    }

    // cambio el nombre del metodo save() por el de createUser() y meteré dentro la lógica de creacion
    // public function save(){
    //     User::createUser($this->validated()); //esto devuelve un array con los datos que han sido validados
    // }

    public function createUser()
    {
        //si tuviera el metodo createUser en el modelo User usaria la linea siguiente
        // User::createUser($this->validated()); //esto devuelve un array con los datos que han sido validados

        // en lugar de tener el metodo createUser en el modelo User he traido aqui la logica de validacion
        // DB::transaction(function() {
            // $data=$this->validated(); si uso el $this en el resto no me hace falta usar el $data
            // parece que la que mas le gusta a Duilio es la del array_get que dejo comentado a final


            // Hay varias maneras de hacerlo. Las numero comentadas y sigo con la del final
            
            // Opcion 1
            // $user = User::create([
            //     // 'name'=>$data['name'],
            //     // 'email'=>$data['email'],
            //     // 'password'=>bcrypt($data['password'])
            //     'name'=>$this->name,
            //     'email'=>$this->email,
            //     'password'=>bcrypt($this->password),
            // ]);
            
            // $user->profile()->create([
            //     //'bio'=> $data['bio'],
            //     'bio'=> $this->bio,
                
                // con operador fusion null me aseguro de que el campo twitter es opcional
                // 'twitter'=> $data['twitter'] ?? null, //quiere decir que uso el campo Twitter si esta definido y sino lo fuerzo a null. Uso el operador de fusion de null php7
                //el operador de fusion de null ?? null es equivalente a
                // 'twitter'=>isset($data['twitter']) ? $data('twitter') : null ,
                // mejor operador null

                //tambien me puedo asegurar con el helper array_get de Laravel
                // 'twitter'=> array_get($data, 'twitter'),

                // otra forma es acceder directamente al metodo con $this
                // 'twitter'=>$this->twitter,
                // en este caso sustiimos $data por $this en todo el createUser

            // completa pero comentada
        DB::transaction(function() {

            $data=$this->validated(); 


            //OPcion 1: Antes de usar role.
            // $user = User::create([
            //     'name'=>$data['name'],
            //     'email'=>$data['email'],
            //     'password'=>bcrypt($data['password']),
            //     // 'profession_id'=>$data['profession_id'], //  ?? null,  //si uso present puedo quitar el ?? null. Me lo llevo a user profile
            //     'role'=>$data['role'],
            // ]);

            //OPcion 2: Cuando empiezo a usar role
            // en lugar de usar el modelo y guardarlo en la base de dato
            // creo el modelo sin guardar, luego en un segundo paso creo el role y en un tercero lo guardo
            $user = new User([
                'name'=>$data['name'],
                'email'=>$data['email'],
                'password'=>bcrypt($data['password']),
                'role'=>$data['role'],
            ]);

            $user->role=$data['role'] ?? 'user';  //tengo que decirle cual es el valor por defecto si no informo del campo, sino no pasa la prueba

            $user->save();

            // A la hora de rellenar el UserProfile yo entiendo mejor devolver el $user->id, pero lo que ha hecho Duilio es crear el metodo profile en el modelo user y tirar de ahí. Dice que es mas limpio
            // UserProfile::create([
            //     'bio'=> $data['bio'],
            //     'twitter'=> $data['twitter'],
            //     'user_id'=> $user->id,  
            // ]);
            $user->profile()->create([
                // no relleno la clave foranea de user_id porque se hace en el metodo profile del modelo user, a mi me sale mas facil lo que pongo en el bloque comentado anterior
                'bio'=> $data['bio'],
                'otraProfesion'=>$data['otraProfesion'],
                // 'twitter'=> array_get($data, 'twitter'), uso la siguiente para ser consistente
                'twitter'=> $data['twitter'], // ?? null,   //si uso present puedo quitar el ?? null
                'profession_id'=>$data['profession_id'], //  ?? null,  //si uso present puedo quitar el ?? null. lo traigo de user
                ]);

            // tengo que adjuntar los skills del usuario. Lo puedo hacer con el metodo attach
            // llamo al metodo skills, para eso lo tengo que crear en userController
            // si pongo esto me protejo de que skills este vacio
            // $user->skills()->attach($data['skills'] ?? []);  
            // otra forma
            if (!empty ($data['skills']))           // $user->skills()->attach($data['skills'] ?? []); 
            {
                $user->skills()->attach($data['skills']); //el usuario lo acabo de crear, es nnuevo. POr eso puedo usar attach. Si fuera un usuario que ya existe, por ejemplo en un update, usaria el metodo sync
            }
        });
    }
}
