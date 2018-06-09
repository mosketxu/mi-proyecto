@extends('layout')

@section('title',"Usuarios")

@section('content')
    <h1> {{ $title }} </h1>
    <p>
        <a href="{{ route('users.create') }}">Nuevo usuario</a>
    </p>
    
    <ul>
        @forelse($users as $user)
            <li> 
                {{ $user->name }}, ({{$user->email}})
                {{-- <a href="{{url('/usuarios/'.$user->id)}}">Ver detalles</a> --}}
                {{-- <a href="{{url("/usuarios/{$user->id}")}}">Ver detalles</a> --}}
                {{-- <a href="{{action('UserController@show',['id'=>$user->id])}}">Ver detalles</a> --}}
                
                {{--  elquent nos permite usar los vinculos como estas dos lineas o como las dos siguientes  --}}
                {{--  <a href="{{ route('users.show',['id'=>$user->id]) }}">Ver detalles</a> | 
                <a href="{{ route('users.edit',['id'=>$user->id]) }} ">Editar</a>  --}}

                <a href="{{ route('users.show',$user) }}">Ver detalles</a> | 
                <a href="{{ route('users.edit',$user) }} ">Editar</a>
                {{--  para eliminar no puedo usar un vinculo porque encuentra otra ruta. lo tengo que hacer a traves de un formulario  --}}
                <form action="{{ route('users.destroy',$user) }} " method="POST">
                    {{--  paso el campo del token  --}}
                    {{ csrf_field() }} 
                    {{--  le paso el campo oculto DELETE para que no vaya a la ruta del POST sino del DELETE  --}}
                    {{ method_field('DELETE')}}
                    <button type="submit">Eliminar</button>
                </form>

                <a href="{{ route('users.destroy',$user) }} ">Editar</a>

            </li>
        @empty
            <li>No hay usuarios registrados</li>
        @endforelse
    </ul>

@endsection

@section('sidebar')
    @parent
    <h3>otra side bar</h3>
@endsection