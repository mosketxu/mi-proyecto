@extends('layout')

@section('title',"Usurario {$id}")
@section('content')
    <h1> Usuario #{{$id}} </h1>
    <h2>Mostrando detalle del usuario: {{$id}}</h2>
@endsection