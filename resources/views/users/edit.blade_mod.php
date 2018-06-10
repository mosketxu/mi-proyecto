@extends('layout')

@section('title',"Editando el usuario $user->name")
@section('content')
    <h1>Editando detalle del usuario: {{$user->name}}</h1>

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

    <form method="POST" action="{{ url("usuarios/{user->id}") }}">
	<form method="POST" action="{{ url("usuarios/{$user->id}") }}">

        { method_field('PUT') }     
        { csrf_field() } 
        <div class="form-group">
            <label for="name" id="name">Nombre:</label>
            <input type="text" class="form-control" name="name" placeholder="Alex Arregui" required value="{{ old('name', $user->name) }} ">
            <div class="valid-feedback">Looks good!</div>
            <div class="invalid-feedback">Please choose a name.</div>
        @if ($errors->has('name'))
                <p>{{ $errors->first('name')}}</p>
            @endif
            <label for="email" id="email">email:</label>
            @if ($errors->has('email'))
                <p>{{ $errors->first('email')}}</p>
            @endif

            <div class="input-group mb-2 mr-sm-2">
                <div class="input-group-prepend">
                    <div class="input-group-text">@</div>
                </div>
                <input type="email" class="form-control" name="email" placeholder="alex@example.com" required value="{{ old('email',$user->email) }}" >
                <div class="valid-feedback">Looks good!</div>
                <div class="invalid-feedback">Please choose a valid email.</div>
            </div>
            <label for="password" id="password">Password:</label>
            <input type="password" name="password" class="form-control" required aria-describedby="passwordHelpBlock" >
            <small id="passwordHelpBlock" class="form-text text-muted">
                Your password must be 8-20 characters long, contain letters and numbers, and must not contain spaces, special characters, or emoji.
            </small>
            <div class="valid-feedback">Looks good!</div>
            <div class="invalid-feedback">Please choose a valid password.</div>
        <button type="submit" class="btn btn-primary">Actualizar usuario</button>
        </div>
    </form>

    {{-- <a href="{{url()->previous()}}">Volver</a> --}}
    {{-- <a href="{{url('/usuarios')}}">Volver al listado de usuarios</a> --}}
    {{-- <a href="{{action('UserController@index')}}">Volver al listado de usuarios</a> --}}
    <a href="{{route('users.index')}}">Volver al listado de usuarios</a>

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