@extends('layout')

@section('title',"Editando el usuario $user->name")
@section('content')
    <h1>Editando detalle del usuario: {{$user->name}}</h1>

    @include('shared._errors')

    <form class="needs-validation" novalidate method="POST" action="{{ url("usuarios/{$user->id}") }}">
    {{--  <form method="POST" action="{{ url("usuarios/{$user->id}") }}">  --}}

        {{ method_field('PUT') }}     

        @include('users._fields')

        <div class="form-group mt-4">
            <button type="submit" class="btn btn-primary">Actualizar usuario</button>
            {{-- <a href="{{url()->previous()}}">Volver</a> --}}
            {{-- <a href="{{url('/usuarios')}}">Volver al listado de usuarios</a> --}}
            {{-- <a href="{{action('UserController@index')}}">Volver al listado de usuarios</a> --}}
            <a href="{{route('users.index')}}" class="btn btn-link">Volver al listado de usuarios</a>
        </div>
    </form>

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