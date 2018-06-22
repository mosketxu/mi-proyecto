<?php

namespace App\Http\Requests;

use App\User;
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
            'password'=>'required','min:6',
            'bio'=>'required', //para validar esto hacer una prueba con TDD
            //'twitter'=>'nullable|url', //para validar esto hacer una prueba con TDD
             'twitter'=>['nullable','url'], //para validar esto hacer una prueba con TDD
            //'profession_id'=>'', //si dejo esta linea así cuando ejecuto la prueba da un error de base de datos, pero quiero atajar el error antes así que pongo la siguiente linea
            // 'profession_id'=>'exists:professions,id',  // para añadir una condicion de que solo pueda seleccionar profesiones selecccionables siguiente línea con sintaxis nueva para cosas mas complejas
            // indico que quiero que la profession este presente en el campo id  la tabla professions
            // y que ademas sea selectable
            // 'profession_id'=>Rule::exists('professions','id')->where('selectable',true), //si no pongo el where falla la prueba only_selectable_professions_are_valid
            // si ademas quiero que solo se puedan seleccionar las que no están borradas con el softDelete
            'profession_id'=>Rule::exists('professions','id')->where('selectable',true)->whereNull('deleted_at'), //si no pongo el where falla la prueba only_selectable_professions_are_valid
        ];
    }

    public function messages(){
        return [
            'name.required'=>'El campo nombre es obligatorio',
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

            $user = User::create([
                'name'=>$data['name'],
                'email'=>$data['email'],
                'password'=>bcrypt($data['password']),
                'profession_id'=>$data['profession_id'] ?? null,
            ]);
            
            $user->profile()->create([
                'bio'=> $data['bio'],
                // 'twitter'=> array_get($data, 'twitter'), uso la siguiente para ser consistente
                'twitter'=> $data['twitter'] ?? null,
            ]);
        });
    }
}
