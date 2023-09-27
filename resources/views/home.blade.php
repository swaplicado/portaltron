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
                    <h1>{{\Auth::user()->names}}</h1>
                    <h1> a PP</h1>
                    </blockquote>
                    <figcaption class="blockquote-footer" style="padding-left: 7%">
                        Bienvenido {{\Auth::user()->full_name}} a Portal proveedores
                    </figcaption>
                </figure>
            </div>
            <div class="col-md-1">
            </div>
        </div>
    </div>
</div>  
@endsection
