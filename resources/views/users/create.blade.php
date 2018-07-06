@extends('layout')

@section('title',"Crear usuario")

@section('content')
    <div class="card">
        <h4 class="card-header"> Crear usuario </h4>
        <div class="card-body">

            @if ($errors->any())
                <div class="alert alert-danger">
                    <h6>Por favor, corrige los errores:</h6>
                    <ul>
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach    
                    </ul>    
                </div>
            @endif
            
            <form class="needs-validation" novalidate method="POST" action="{{route('users.crear')}}">
            {{-- <form method="POST" action="{{url('usuarios/crear')}}"> --}}
            {{-- <form method="POST" action="{{url('usuarios')}}"> --}}
                {{-- {!!csrf_field()!!}  es el token para evitar que nos hagan post desde sitios de terceros. Es seguridad --}}
                {!!csrf_field()!!} 
                <div class="form-group">
                    <label for="name" >Nombre:</label>
                    <input type="text" class="form-control" name="name" id="name" placeholder="nombre usuario" required value={{ old('name') }} >
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
                        <input type="email" class="form-control" name="email"  id="email" placeholder="email@example.com" required value={{ old('email') }} >
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
                    <textarea class="form-control" name="bio" id="bio" placeholder="tu biografia" required >{{ old('bio') }}</textarea>
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
                            <option value="{{ $profession->id }}"{{ old('profession_id') == $profession->id ? ' selected' :'' }}> 
                                 {{$profession->title}}
                            </option> 
                        @endforeach
                    </select>
                    <div class="valid-feedback">Looks good!</div>
                    <div class="invalid-feedback">Please us tell something about you.</div>
                </div>
                
                <div class="form-group">
                    <label for="otraProfesion" >Otra Profesión:</label>
                    <input type="text" class="form-control" name="otraProfesion" id="otraProfesion" placeholder="pon tu profesión si no está en la lista"  value={{ old('otraProfesion') }} >
                    <div class="valid-feedback">Looks good!</div>
                    <div class="invalid-feedback">Please choose a url.</div>
                </div>
                
                <div class="form-group">
                    <label for="twitter" >twitter:</label>
                    <input type="text" class="form-control" name="twitter" id="twitter" placeholder="https://twitter.com/alexarregui"  value={{ old('twitter') }} >
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
                            {{ old("skills.{$skill->id}") ? 'checked' : '' }}> {{-- como es lioso lo hago de esta otra manera asegurandome que he puesto el id del skill en el name --}}
                        <label class="form-check-label" for=" skill_{{ $skill->id }}">{{ $skill->name }}</label>
                    </div>
                    @endforeach
                </div>
               
                <div class="form-group mt-4">
                    <button type="submit" class="btn btn-primary">Crear usuario</button>
                    {{-- <a href="{{url()->previous()}}">Volver</a> --}}
                    {{-- <a href="{{url('/usuarios')}}">Volver al listado de usuarios</a> --}}
                    {{-- <a href="{{action('UserController@index')}}">Volver al listado de usuarios</a> --}}
                    <a href="{{route('users.index')}}" class="btn btn-link">Volver al listado de usuarios</a>
                </div>
            </form>
        </div>
    </div>
@endsection

<script>
        // Example starter JavaScript for disabling form submissions if there are invalid fields
        (function() {
          'use strict';
          window.addEventListener('load', function() {
            // Fetch all the forms we want to apply custom Bootstrap validation styles to
            var forms = document.getElementsByClassName('needs-validation');
            // Loop over them and prevent submission
            var validation = Array.prototype.filter.call(forms, function(form) {
              form.addEventListener('submit', function(event) {
                if (form.checkValidity() === false) {
                  event.preventDefault();
                  event.stopPropagation();
                }
                form.classList.add('was-validated');
              }, false);
            });
          }, false);
        })();
        </script>