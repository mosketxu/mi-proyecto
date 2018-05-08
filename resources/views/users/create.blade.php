@extends('layout')

@section('title',"Crear nuevo usuario")
@section('content')
    <h1>Crear nuevo usuario</h1>

    <form method="POST" action="{{route('users.crear')}}">
        {!!csrf_field()!!}

        <button type="submit">Crear usuario</button>
    
    </form>

    {{-- <a href="{{url()->previous()}}">Volver</a> --}}
    {{-- <a href="{{url('/usuarios')}}">Volver al listado de usuarios</a> --}}
    {{-- <a href="{{action('UserController@index')}}">Volver al listado de usuarios</a> --}}
    <a href="{{route('users.index')}}">Volver al listado de usuarios</a>

@endsection