@extends('layouts.principal')

@section('content')
<div class="row w-100 mx-0 d-flex align-items-center justify-content-center">
    <div class="col-md-6 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Nuevo Proveedor</h4>
                <form class="forms-sample" href="#">
                    <div class="form-group">
                        <label for="provider_name">Nombre proveedor</label>
                        <input type="text" class="form-control" id="provider_name" placeholder="Username">
                    </div>
                    <div class="form-group">
                        <label for="provider_short_name">Nombre comercial</label>
                        <input type="email" class="form-control" id="provider_short_name" placeholder="Email">
                    </div>
                    <div class="form-group">
                        <label for="provider_rfc">RFC</label>
                        <input type="text" class="form-control" id="provider_rfc" placeholder="Password">
                    </div>
                    <div class="form-group">
                        <label for="provider_email">Email</label>
                        <input type="email" class="form-control" id="provider_email" placeholder="Password">
                    </div>
                    <button type="submit" class="btn btn-primary mr-2">Submit</button>
                    <button class="btn btn-light">Cancel</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection