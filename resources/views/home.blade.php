@extends('layouts.principal')

@section('content')
<div class="card shadow mb-4">
    <div class="card-header">
    
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-1">
            </div>
            <div class="col-md-10" style="text-align: center">
                <figure>
                    <blockquote class="blockquote">
                    <h1>Bienvenido</h1>
                    @if(!\Auth::user()->is_provider())
                        <h1>{{\Auth::user()->names}}</h1>
                    @else
                        <h1>{{\Auth::user()->getProviderData()->provider_short_name}}</h1>
                    @endif
                    <h1> a PP</h1>
                    </blockquote>
                    <figcaption class="blockquote-footer" style="padding-left: 7%">
                        @if(!\Auth::user()->is_provider())
                            Bienvenido {{\Auth::user()->full_name}} a Portal proveedores
                        @else
                            Bienvenido {{\Auth::user()->getProviderData()->provider_name}} a Portal proveedores
                        @endif
                    </figcaption>
                </figure>
            </div>
            <div class="col-md-1">
            </div>
        </div>
    </div>
</div>  
@endsection
