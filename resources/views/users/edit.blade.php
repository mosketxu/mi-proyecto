@extends('layout')

@section('title',"Editando el usuario $user->name")

{{-- OPCION 1: antes de usar @include _errors ni include _fields tenia un if con los errores
                Como no estaba desarrollada la he quitado 
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
                --}}

{{-- OPCION 2: usando @include _errors  @include _fields.              
        Quito el if de los errores y lo llevo a _error.blade.php
        El guion bajo es para indicar que no es una vista completa, sino una subvista

        Incluyo todos los campos en _fields y los llamo desde allí, para que me sirva junto con create.blade.php
    --}}


{{-- @section('content')
    <h1>Editando detalle del usuario: {{$user->name}}</h1>

    @include('shared._errors')

    <form class="needs-validation" novalidate method="POST" action="{{ url("usuarios/{$user->id}") }}">
    {{--  <form method="POST" action="{{ url("usuarios/{$user->id}") }}">  --}}

        {{-- {{ method_field('PUT') }}     

        @include('users._fields')

        <div class="form-group mt-4">
            <button type="submit" class="btn btn-primary">Actualizar usuario</button> --}}
            {{-- <a href="{{url()->previous()}}">Volver</a> --}}
            {{-- <a href="{{url('/usuarios')}}">Volver al listado de usuarios</a> --}}
            {{-- <a href="{{action('UserController@index')}}">Volver al listado de usuarios</a> --}}
            {{-- <a href="{{route('users.index')}}" class="btn btn-link">Volver al listado de usuarios</a>
        </div>
    </form> --}}

{{-- @endsection  --}}

{{-- OPCION 3: con _card. Mismas indicaciones que para create.blade --}}

@section('content')
    @component('shared._card')

        {{-- en este caso pongo $slot en varias lineas para poder mandar la vble del nombre --}}
        @slot('header')
            Editando detalle del usuario: {{$user->name}} 
        @endslot

        @include('shared._errors')

        {{-- De nuevo no meto lo que sigue en un slot, con lo que equivale a que esté en la vble $slot --}}
        <form class="needs-validation" novalidate method="POST" action="{{ url("usuarios/{$user->id}") }}">

            {{ method_field('PUT') }}     
    
            @include('users._fields')
    
            <div class="form-group mt-4">
                <button type="submit" class="btn btn-primary">Actualizar usuario</button>
                <a href="{{route('users.index')}}" class="btn btn-link">Volver al listado de usuarios</a>
            </div>
        </form>
    
    @endcomponent

@endsection


{{-- <script>
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
</script> --}}