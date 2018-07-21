@section('content')
    <div class="card">
        {{-- $header es la vble que llega a traves del slot de create.blade (y edit.blade) --}}
        <h4 class="card-header"> {{$header }} </h4>  
        <div class="card-body">
            {{-- una opcion es devolver en create.blade y en edti.blade el slot content,  --}}
            {{-- {{ $content }}   --}}

            {{-- pero si dejo el contenido sin meter en ningun slot ese mismo contenido se guarda en la vble por defecto @slot que es la que uso aqui --}}
            {{ $slot }}
        </div>
    </div>
@endsection
