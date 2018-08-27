{{-- {!!csrf_field()!!}  es el token para evitar que nos hagan post desde sitios de terceros. Es seguridad contra ataques cross site forgery --}}
{!!csrf_field()!!} 

{{-- como he traido el html de la vista de create cuando hago la plantilla para edit no muestra los valores, así que debo añadir
despues de old las variables que toquen, por ejemplo value={{ old('name'),$user->name }}  --}}
{{-- OJO CUANDO AÑADO  EL VALOR DEBO PORNER DOBLES COMILLAS: MAL value={{ old('name'),$user->name }} BIEN value="{{ old('name'),$user->name }}" --}}
<div class="form-group">
    <label for="name" >Nombre:</label>
    <input type="text" class="form-control" name="name" id="name" placeholder="nombre usuario" required value="{{ old('name', $user->name) }}" >
    <div class="valid-feedback">Looks good!</div>
    <div class="invalid-feedback">Please choose a name.</div>
    {{--  @if ($errors->has('name'))
    <p>{{ $errors->first('name')}}</p>
    @endif  --}}
</div>

<div class="form-group">
    <label for="email">email:</label>
    {{--  @if ($errors->has('email'))
    <p>{{ $errors->first('email')}}</p>
    @endif  --}}
    
    <div class="input-group mb-2 mr-sm-2">
        <div class="input-group-prepend">
            <div class="input-group-text">@</div>
        </div>
        <input type="email" class="form-control" name="email"  id="email" placeholder="email@example.com" required value="{{ old('email', $user->email) }}" >
        <div class="valid-feedback">Looks good!</div>
        <div class="invalid-feedback">Please choose a valid email.</div>
    </div>
</div>

<div class="form-group">
    <label for="password" >Password:</label>
    <input type="password" name="password" class="form-control" id="password" required aria-describedby="passwordHelpBlock" >
    <small id="passwordHelpBlock" class="form-text text-muted">
        Your password must be 8-20 characters long, contain letters and numbers, and must not contain spaces, special characters, or emoji.
    </small>
    <div class="valid-feedback">Looks good!</div>
    <div class="invalid-feedback">Please choose a valid password.</div>
</div>

<div class="form-group">
    <label for="bio" >bio:</label>
    <textarea class="form-control" name="bio" id="bio" placeholder="tu biografia" required >{{ old('bio', $user->profile->bio) }}</textarea>
    <div class="valid-feedback">Looks good!</div>
    <div class="invalid-feedback">Please us tell something about you.</div>
</div>

<div class="form-group">
    <label for="profession_id" >Profesion:</label>
    <select class="form-control" name="profession_id" id="profession_id">
        <option value=""> Selecciona una profesión</option>
        {{-- @foreach (App\Profession::all() as $profession) // si queremos que este ordenado no puedo usar all, debo usar get --}}
        {{-- @foreach (App\Profession::OrderBy('title','ASC')->get() as $profession) // aunque mejor que aqui por limpieza y por si hago consultas mas complicadas lo llevo al metodo create de UserController --}}
        @foreach($professions as $profession)
            <option value="{{ $profession->id }}"{{ old('profession_id', $user->profile->profession_id) == $profession->id ? ' selected' :'' }}> 
                {{$profession->title}}
            </option> 
        @endforeach
    </select>
    <div class="valid-feedback">Looks good!</div>
    <div class="invalid-feedback">Please us tell something about you.</div>
</div>

<div class="form-group">
    <label for="otraProfesion" >Otra Profesión:</label>
    <input type="text" class="form-control" name="otraProfesion" id="otraProfesion" placeholder="pon tu profesión si no está en la lista"  
                value="{{ old('otraProfesion',$user->profile->otraProfesion) }}" >
    <div class="valid-feedback">Looks good!</div>
    <div class="invalid-feedback">Please choose a profession.</div>
</div>

<div class="form-group">
    <label for="twitter" >twitter:</label>
    <input type="text" class="form-control" name="twitter" id="twitter" placeholder="https://twitter.com/alexarregui"  
                value="{{ old('twitter',$user->profile-> twitter) }}" >
    <div class="valid-feedback">Looks good!</div>
    <div class="invalid-feedback">Please choose a url.</div>
</div>

<div class="form-group">
    <h5>Habilidades</h5>
    @foreach($skills as $skill)
    <div class="form-check form-check-inline">
        <input  name="skills[{{ $skill->id }}]" 
            class="form-check-input" 
            type="checkbox" 
            id=" skill_{{ $skill->id }}" 
            value="{{ $skill->id}}"
            {{-- obtengo el valor del campo skills y pregunto si el skill está en el array de skills que he retornado cuando se recarga la pagina tras un error de validadcion
            Si hay alguno seleccionado todo va bien
            pero si no hay nada seleccionado da error en vista porque in_array espera un array en el segundo argumento y recibe null
            Para evitarlo me aseguro de que no sea null con el is_array --}} --}}
            {{--  {{ is_array(old('skills')) && in_array($skill->id,old('skills')) ? 'checked' : '' }}>    --}}

            {{-- una solucion menos engorrosa es usar el helper array_wrap de laravel que detecta si hay null y en ese caso retorna un array vacio --}}
            {{-- {{ in_array($skill->id,array_wrap(old('skills'))) ? 'checked' : '' }} --}}

            {{-- otra solucion sería buscar el valor del checkbox en concreto y no de todos--}}
            {{-- {{ old("skills.{$skill->id}") ? 'checked' : '' }}> --}} {{-- como es lioso lo hago de esta otra manera asegurandome que he puesto el id del skill en el name --}}

{{--             finalmente para poder llevar los valores que viene del edit pregunto si el formulario tiene errores.
            Si los tiene verifico si el usuario ha marcado la habilidad en concreto (estoy en un foreach) y 
            si no hay error reviso en las habilidades guardadas en la bbdd --}}            
            {{ $errors->any() ? old("skills.{$skill->id}") : $user->skills->contains($skill) ? 'checked' : '' }}>  

        <label class="form-check-label" for=" skill_{{ $skill->id }}">{{ $skill->name }}</label>
    </div>
    @endforeach
</div>

<div class="form-group">
    <h5 class="mt-3">Roles</h5>
    {{-- opcion 1  con el listado de roles en el mismo foereach --}}
    {{--   al poner $role=>$name recupero tanto el indice ($role) como el nombre ($name), el indice al ser array asociativo es 'admin' o 'user', y el nombre será 'Admin' o 'Usuario' --}}
    {{-- @foreach(['admin'=>'Admin','user'=>'Usuario'] as $role=>$name)  --}}
    {{-- <div class="form-check form-check-inline"> --}}
        {{--  el id será entonce role_admin y role_user --}}
        {{-- <input class="form-check-input" type="radio" name="role" id="role_{{ $role }}" value="{{ $role }}">  --}}
        {{-- <label class="form-check-label" for="role_{{ $role }}">{{ $name }}</label> --}}
    {{-- </div> --}}
    {{-- @endforeach --}}

    {{-- opcion 2 Usando idiomas. --}}
    {{-- En resouces\lang\en esta la carpeta con los idiomas en ingles. De momento sigo ahí --}}
    {{-- En esa carpeta creo un archivo llamado users.php donde retorno un array asociativo con la llave 'roles' que a su vez tendrá otro array ['admin'=>'Admin','user'=>'Usuario'] --}}
    {{-- para poder usarlo uso el helper trans para obtener esta traduccion --}}
    {{-- llamo al users.roles donde users se corresponde con el archibo que he creado en la carpeta \resources\lang\en y roles a la llave que busco--}}
    {{-- Laravel buscara ese archivo y me devuelve el valor de roles, en este caso el array asociativo --}}
    {{-- @foreach(trans('users.roles') as $role=>$name) 
    <div class="form-check form-check-inline">
        <input class="form-check-input" type="radio" name="role" id="role_{{ $role }}" value="{{ $role }}"> 
        <label class="form-check-label" for="role_{{ $role }}">{{ $name }}</label>
    </div>
    @endforeach --}}

    {{-- opcion 3 Simplificando a partir de los idiomas --}}
    {{-- Mando la variable $roles desde el controlador UserController --}}
    {{-- @foreach($roles as $role=>$name) 
    <div class="form-check form-check-inline">
        <input class="form-check-input" type="radio" name="role" id="role_{{ $role }}" value="{{ $role }}"> 
        <label class="form-check-label" for="role_{{ $role }}">{{ $name }}</label>
    </div>
    @endforeach --}}

    {{-- Una vez lista la vista debo controlar que tenga memoria cuando hay un error de validacion --}}
    @foreach($roles as $role=>$name) 
    <div class="form-check form-check-inline">
        <input  class="form-check-input" 
                type="radio" 
                name="role" 
                id="role_{{ $role }}" 
                value="{{ $role }}"
                {{-- busco el valor si viene de edit en lugar de create --}}
                {{-- {{ old('role')== $role ? 'checked' : '' }}>  --}}
                {{ old('role', $user->role)== $role ? 'checked' : '' }}>

        <label class="form-check-label" for="role_{{ $role }}">{{ $name }}</label>
    </div>
    @endforeach

    {{-- voy a las pruebas y en getvalidata envio el role user como user por defecto y así en la verificacion de las credenciales del usuario quiero verificar que sea user--}}
    {{-- ejecuto la prueba it_creates_a_new_user --}}
    {{-- Primer error: Unknown column 'role' in 'where clause' --}}
        {{-- debo añadirla a la tabla en la migracion y revisar el model user por el metodo isAdmin--}}
    {{-- Segundo Error: Field 'role' doesn't have a default value --}}
        {{-- Es porque el campo role no está como nullable en la migracion y no guardo ningun valor en el usuario  --}}
        {{-- En el formrequest al momento de crear el usuario le paso el role 'role'=>$data['role'] --}}
        {{-- Para ello previamente definir el role en la Rules aunque sea 'role'=>'' --}}
    {{-- tecer error: Field 'role' doesn't have a default value --}}
        {{-- hay que decir que role es fillable en el modelo user PERO LO HACEMOS DE OTRA MANERA  --}}
        {{-- Lo que vamos a hacer es crear el usuario de otra manera en el metodo createUser del formRequest.  --}}
        {{-- En lugar de $user=User::create([ etc hago un new user i.e. $user=new User([ etc  --}}
        {{-- esto crea el modelo pero no lo guarda en la base de datos y --}}
        {{-- luego agrego en un paso aparte su role y --}}
        {{-- finalmente guarda el usuario utilizando save() --}}
    {{-- La prueba pasa --}}
    {{-- Explicacion de porque hace lo de role así.
    En este caso no sería necesario porque no pasamos todos los datos de forma arbitraria, sino que los estoy asignando uno a uno, el name, el email, la pass encriptada
    Es decir digo cuales son fillables y cuales no
    Fillable nos protege si estamos pasando datos de manera masiva al modelo con el metodo all() del obejto request $user=new User($this->all()); --}}

    {{-- ahora hacemos la validacion --}}
    {{-- En UsersModuleTest hago las pruebas
        the_role_is_optional
        the_role_must_be_valid en esta prueba hemos creado una nueva clase Role con el metodo getList() que usamos en la rule con un implode
        las documento allí
        y esta pruebas pasan PERO
        CUANDO EJECUTO TODAS LAS DEMAS FALLAN UN MONTON. --}}
    
    {{-- Como hay mucho errores para detectar el primero hacemos t --stop-on-failure --}}
    {{-- Error en it_shows_the_users_list Field 'role' doesn't have a default value --}}
    {{-- es debido a que en el UserFactory no estamos creando los roles. Estamos creando usuarios pero sin informar los roles --}}
    {{-- colocamos el role por defecto user en UserFactory --}}

    {{-- se arreglan muchos errores pero queda uno en the_role_field_is_optional da ValidationException: the given data is invalid --}}
    {{-- esto es debido a que nuestro campo role no tiene la regla nullable --}}
    {{-- ya pasan las pruebas --}}

    {{-- Para probar el formulario hay que hacer un php artisan migrate: fress --seed --}}

</div>