<!-- Fixed navbar -->
{{-- Las rutas si son simples se pueden hacer con el helper url, pero si son complicadas mejor con el helper route. El helper action no lo usa el chico de styde
Uso las dos como ejemplo.
Tambien hay un ejemplo donde se llama a una variable en index.blade.php
 --}}
 
 <nav class="navbar navbar-expand-md navbar-dark fixed-top bg-dark">
        <a class="navbar-brand" href={{url('/')}}>mi Proyecto</a>   {{-- con helper url --}}
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarCollapse" aria-controls="navbarCollapse" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarCollapse">
            <ul class="navbar-nav mr-auto">
                <li class="nav-item active">
                <a class="nav-link" href={{route('welcome')}}>Home <span class="sr-only">(current)</span></a> {{-- con helper route --}}
                </li>
                <li class="nav-item">
                <a class="nav-link" href={{url('/usuarios')}}>Usuarios</a> {{-- con helper url --}}
                </li>
                <li class="nav-item">
                <a class="nav-link disabled" href="#">Disabled</a>
                </li>
            </ul>
            <form class="form-inline mt-2 mt-md-0">
                <input class="form-control mr-sm-2" type="text" placeholder="Search" aria-label="Search">
                <button class="btn btn-outline-success my-2 my-sm-0" type="submit">Search</button>
            </form>
        </div>
    </nav>