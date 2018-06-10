@extends('layout')

@section('title',"Usuarios")

@section('content')
<div class="d-flex justify-content-between align-items-end mb-2">
    <h1 class="pb-1"> {{ $title }} </h1>
    <p>
        <a href="{{ route('users.create') }}" class="btn btn-primary">Nuevo usuario</a>
    </p>
</div>
    
    @if ($users->isNotEmpty())
        <table class="table">
            <thead class="thead-dark">
                <tr>
                    <th scope="col">#</th>
                    <th scope="col">Name</th>
                    <th scope="col">Email</th>
                    <th scope="col">Acciones</th>
                </tr>
            </thead>
        <tbody>
            @foreach ($users as $user)
            <tr>
                <th scope="row">{{ $user->id }}</th>
                <td>{{ $user->name }}</td>
                <td>{{ $user->email }}</td>
                <td>
                    <form action="{{ route('users.destroy',$user) }} " method="POST">
                        {{ csrf_field() }} 
                        {{ method_field('DELETE')}}
                        <a href="{{ route('users.show',$user) }}" class="btn btn-link"><span class="oi oi-eye"></span></a> 
                        <a href="{{ route('users.edit',$user) }} " class="btn btn-link"><span class="oi oi-pencil"></span></a>
                        <button type="submit" class="btn btn-link"><span class="oi oi-trash"></span></button>
                    </form>
                </td>
            </tr>
        </tbody>
        @endforeach
        </table>
    @else
        <p>No hay usuarios registrados</p>
    @endif


    {{-- comento el formulario original porque lo hemos mejorado con la tabla --}}
      {{-- <ul>
        @forelse($users as $user)
            <li> 
                {{ $user->name }}, ({{$user->email}})
                {{-- <a href="{{url('/usuarios/'.$user->id)}}">Ver detalles</a> --}}
                {{-- <a href="{{url("/usuarios/{$user->id}")}}">Ver detalles</a> --}}
                {{-- <a href="{{action('UserController@show',['id'=>$user->id])}}">Ver detalles</a> --}}
                
                {{--  elquent nos permite usar los vinculos como estas dos lineas o como las dos siguientes  --}}
                {{--  <a href="{{ route('users.show',['id'=>$user->id]) }}">Ver detalles</a> | 
                <a href="{{ route('users.edit',['id'=>$user->id]) }} ">Editar</a>  --}}

                {{-- <a href="{{ route('users.show',$user) }}">Ver detalles</a> | 
                <a href="{{ route('users.edit',$user) }} ">Editar</a> --}}
                {{--  para eliminar no puedo usar un vinculo porque encuentra otra ruta. lo tengo que hacer a traves de un formulario  --}}
                {{-- <form action="{{ route('users.destroy',$user) }} " method="POST"> --}}
                    {{--  paso el campo del token  --}}
                    {{-- {{ csrf_field() }}  --}}
                    {{--  le paso el campo oculto DELETE para que no vaya a la ruta del POST sino del DELETE  --}}
                    {{-- {{ method_field('DELETE')}} --}}
                    {{-- <button type="submit">Eliminar</button> --}}
                {{-- </form> --}}
            {{-- </li> --}}
        {{-- @empty --}}
            {{-- <li>No hay usuarios registrados</li> --}}
        {{-- @endforelse --}}
    {{-- </ul> --}}

@endsection

@section('sidebar')
    @parent
    <h3>otra side bar</h3>
@endsection