@extends('layouts.principal')

@section('headJs')
<script>
    function GlobalData(){
        this.lProviders = <?php echo json_encode($lProviders); ?>;
        this.oArea = <?php echo json_encode($oArea); ?>;
        this.getProviderRoute = <?php echo json_encode(route('sproviders.getProvider')); ?>;
        this.lConstants = <?php echo json_encode($lConstants); ?>;
        this.approveRoute = <?php echo json_encode(route('sproviders.approve')); ?>;
        this.rejectRoute = <?php echo json_encode(route('sproviders.reject')); ?>;
        this.requireModifyRoute = <?php echo json_encode(route('sproviders.requireModify')); ?>;
        this.lStatus = <?php echo json_encode($lStatus); ?>;
        this.voboDocRoute = <?php echo json_encode(route('voboDocs.voboDoc')); ?>;
    }
    var oServerData = new GlobalData();
    var indexesProvidersTable = {
                'id_provider': 0,
                'status_id': 1, 
                'provider_short_name': 2,
                'provider_name': 3,
                'provider_rfc': 4,
                'provider_email': 5,
                'user': 6,
                'status': 7,
                'created': 8,
                'updated': 9,
            };
</script>
@endsection

@section('content')
<div class="card" id="sproviders">
    <div class="card-header">
        <h3>Proveedores</h3>
    </div>
    <div class="card-body">

        @include('sproviders.modal_authorize_provider')

        <div class="grid-margin">
            @include('layouts.buttons', ['show' => true])
            <span class="nobreak">
                <label for="status_filter">Filtrar estatus: </label>
                <select class="select2-class form-control" name="status_filter" id="status_filter"></select>
            </span>
        </div>

        <div class="table-responsive">
            <table class="display expandable-table dataTable no-footer" id="table_providers" width="100%" cellspacing="0">
                <thead>
                    <th>id_provider</th>
                    <th>status_id</th>
                    <th>short_name</th>
                    <th style="text-align: center">Proveedor</th>
                    <th style="text-align: center">RFC</th>
                    <th style="text-align: center">Correo/th>
                    <th style="text-align: center">Usuario</th>
                    <th style="text-align: center">Estatus</th>
                    <th style="text-align: center">Fecha creación</th>
                    <th style="text-align: center">Fecha actualización</th>
                </thead>
                <tbody>
                    
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
        var self;
        $(document).ready(function () {
            //filtros para datatables
            $.fn.dataTable.ext.search.push(
                function( settings, data, dataIndex ) {
                    let col_status = null;
                    col_status = parseInt( data[indexesProvidersTable.status_id] );

                    if(settings.nTable.id == 'table_providers'){
                        let iStatus = parseInt( $('#status_filter').val(), 10 );
                        return iStatus == col_status || iStatus == 0;
                    }

                    return true;
                }
            );
            
            $('#status_filter').change( function() {
                table['table_providers'].draw();
            });
        });
    </script>

    @include('layouts.table_jsControll', [
                                            'table_id' => 'table_providers',
                                            'colTargets' => [0,2],
                                            'colTargetsSercheable' => [1],
                                            'select' => true,
                                            'show' => true,
                                            'colTargetsAlignCenter' =>[3,4,5,6,7,8,9],
                                            // 'edit_modal' => true,
                                            // 'delete' => true,
                                        ] )
    <script type="text/javascript" src="{{ asset('myApp/SProviders/vue_sproviders.js') }}"></script>
    <script type="text/javascript" src="{{ asset('myApp/Utils/datatablesUtils.js') }}"></script>
    <script type="text/javascript">
        $(document).ready(function() {
            // drawTable('table_providers', oServerData.lProviders);
            drawTableJson('table_providers', oServerData.lProviders, 
                'id_provider',
                'status_provider_id',
                'provider_short_name',
                'provider_name',
                'provider_rfc',
                'provider_email',
                'username',
                'status',
                'created',
                'updated'
            );
        })
    </script>
@endsection