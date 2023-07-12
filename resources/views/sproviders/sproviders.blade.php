@extends('layouts.principal')

@section('headJs')
<script>
    function GlobalData(){
        this.lProviders = <?php echo json_encode($lProviders); ?>;
        this.oUser = <?php echo json_encode($oUser); ?>;
        this.createRoute = <?php echo json_encode(route('sproviders.create')); ?>;
        this.updateRoute = <?php echo json_encode(route('sproviders.update')); ?>;
        this.deleteRoute = <?php echo json_encode(route('sproviders.delete')); ?>;
        this.getProviderRoute = <?php echo json_encode(route('sproviders.getProvider')); ?>;
    }
    var oServerData = new GlobalData();
    var indexesProvidersTable = {
                'id_provider': 0,
                'provider_short_name': 1,
                'provider_name': 2,
                'provider_rfc': 3,
                'provider_email': 4,
                'user': 5,
                'created': 6,
                'updated': 7,
            };
</script>
@endsection

@section('content')
<div class="card" id="sproviders">
    <div class="card-header">
        <h3>Proveedores</h3>
    </div>
    <div class="card-body">

        @include('sproviders.modal_providers_form')

        <div class="grid-margin">
            @include('layouts.buttons', ['create' => true, 'edit' => true, 'delete' => true])
        </div>

        <div class="table-responsive">
            <table class="display expandable-table dataTable no-footer" id="table_providers" width="100%" cellspacing="0">
                <thead>
                    <th>id_provider</th>
                    <th>short_name</th>
                    <th>Proveedor</th>
                    <th>RFC</th>
                    <th>Email</th>
                    <th>Usuario</th>
                    <th>Fecha creación</th>
                    <th>Fecha actualización</th>
                </thead>
                <tbody>
                    
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@section('scripts')
    @include('layouts.table_jsControll', [
                                            'table_id' => 'table_providers',
                                            'colTargets' => [0, 1],
                                            'colTargetsSercheable' => [],
                                            'select' => true,
                                            'create_modal' => true,
                                            'edit_modal' => true,
                                            'delete' => true,
                                        ] )
    <script type="text/javascript" src="{{ asset('myApp/SProviders/vue_sproviders.js') }}"></script>
    <script type="text/javascript" src="{{ asset('myApp/Utils/datatablesUtils.js') }}"></script>
    <script type="text/javascript">
        $(document).ready(function() {
            drawTable('table_providers', oServerData.lProviders);
        })
    </script>
@endsection