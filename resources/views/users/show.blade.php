@extends('layout')

@section('title',"Usuario {$user->id}")
@section('content')
    <h1> Usuario #{{$user->id}} </h1>
    <p>Nombre del usuario: {{$user->name}}</p>
    <p>Correo del usuario: {{$user->email
    }}</p>

    <p>
        {{-- <a href="{{url()->previous()}}">Volver</a> --}}
        {{-- <a href="{{url('/usuarios')}}">Volver al listado de usuarios</a> --}}
        <a href="{{action('UserController@index')}}">Volver al listado de usuarios</a>
    </p>
@endsection