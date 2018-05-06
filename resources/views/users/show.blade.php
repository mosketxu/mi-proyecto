@extends('layout')

@section('title',"Usuario {$user->id}")
@section('content')
    <h1> Usuario #{{$user->id}} </h1>
    <p>Nombre del usuario: {{$user->name}}</p>
    <p>Correo del usuario: {{$user->email}}</p>
@endsection