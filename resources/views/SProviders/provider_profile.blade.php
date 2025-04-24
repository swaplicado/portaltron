@extends('layouts.principal')

@section('headJs')
<script>
    function GlobalData() {
        this.oProvider = <?php echo json_encode($oProvider); ?>;
        this.updateRoute = <?php echo json_encode(route('registerProvider.updateTempProvider')); ?>;
        this.lDocs = <?php echo json_encode($lDocs); ?>;
    }
    var oServerData = new GlobalData();
</script>
@endsection

@section('content')
    <!-- Panel principal-->
    <div id="registerProvider">
        <div class="content-wrapper">
            <div class="loader"></div>
            <div class="hiddeToLoad">
                <!-- Panel content -->
                <div class="container-scroller" v-if="!successUpdate">
                    <div class="container-fluid page-body-wrapper full-page-wrapper">
                        <div class="content-wrapper d-flex align-items-center auth px-0">
                            <div class="row w-100 mx-0">
                                <div class="col-lg-6 mx-auto">
                                    <div class="auth-form-light text-left py-5 px-4 px-sm-5">
                                        <div class="brand-logo">
                                            <img src="{{ asset('images/aeth.png') }}" alt="logo">
                                        </div>
                                        <h4>Hola! @{{ name }}</h4>
                                        <br>
                                        <br>
                                        <form action="#">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group sm-form-group row">
                                                        <label class="col-sm-3 my-col-sm-3 col-form-label ">Razón
                                                            social</label>
                                                        <div class="col-sm-9">
                                                            <input type="text" class="form-control" id="name"
                                                                placeholder="Razón social" v-model="name" disabled>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group sm-form-group row">
                                                        <label class="col-sm-3 my-col-sm-3 col-form-label ">Nombre
                                                            comercial</label>
                                                        <div class="col-sm-9">
                                                            <input type="text" class="form-control" id="shortName"
                                                                placeholder="Nombre comercial" v-model="shortName" disabled>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group sm-form-group row">
                                                        <label class="col-sm-3 my-col-sm-3 col-form-label ">RFC</label>
                                                        <div class="col-sm-9">
                                                            <input type="text" class="form-control" id="rfc"
                                                                placeholder="RFC" v-model="rfc" disabled>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group sm-form-group row">
                                                        <label class="col-sm-3 my-col-sm-3 col-form-label ">Email</label>
                                                        <div class="col-sm-9">
                                                            <input type="text" class="form-control" id="email"
                                                                placeholder="Email" v-model="email">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            @foreach ($lDocs as $doc)
                                                @if ($doc->is_reject)
                                                    <div class="row">
                                                        <div class="col-md-12">
                                                            <div class="form-group sm-form-group row">
                                                                <label class="col-sm-3 my-col-sm-3 col-form-label ">
                                                                    {{ $doc->name }}
                                                                </label>
                                                                <div class="col-sm-9">
                                                                    <input type="file"
                                                                        id="doc_{{ $doc->id_request_type_doc }}"
                                                                        name="doc_{{ $doc->id_request_type_doc }}"
                                                                        class="file-upload-default" accept=".pdf">
                                                                    <div class="input-group col-xs-12">
                                                                        <input type="text"
                                                                            class="form-control file-upload-info" disabled
                                                                            placeholder="Cargar archivo">
                                                                        <span class="input-group-append">
                                                                            <button class="file-upload-browse btn btn-info"
                                                                                type="button">Cargar</button>
                                                                        </span>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endif
                                            @endforeach
                                            <div class="row">
                                                <div class="col-md-6">

                                                </div>
                                                <div class="col-md-6 text-right">
                                                    <button type="button" class="btn btn-primary"
                                                        v-on:click="save()">Guardar</button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- content-wrapper ends -->
                    </div>
                    <!-- page-body-wrapper ends -->
                </div>
                <!-- End Panel content -->
            </div>
        </div>
        <!-- End Panel principal-->
    </div>
@endsection

@section('scripts')
<script src="{{ asset('myApp/SProviders/vue_providerProfile.js') }}"></script>
@endsection
