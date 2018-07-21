@extends('layout')

@section('title',"Crear usuario")

@section('content')
    <div class="card">
        <h4 class="card-header"> Crear usuario </h4>
        <div class="card-body">

            @include('shared._errors')
            
            <form class="needs-validation" novalidate method="POST" action="{{route('users.crear')}}">
            {{-- <form method="POST" action="{{url('usuarios/crear')}}"> --}}
            {{-- <form method="POST" action="{{url('usuarios')}}"> --}}

                @include('users._fields')
               
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


// si lo activo no funciona la inclusion de la plantilla de errores
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