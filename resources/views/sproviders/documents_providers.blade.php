@extends('layouts.principal')

@section('headJs')
<script>
    function GlobalData(){
        this.lProviders = <?php echo json_encode($lProviders); ?>;
        this.lConstants = <?php echo json_encode($lConstants); ?>;
        this.getProviderRoute = <?php echo json_encode(route('sproviders.getProvider')); ?>;
        this.approveRoute = <?php echo json_encode(route('sproviders.approve')); ?>;
        this.rejectRoute = <?php echo json_encode(route('sproviders.reject')); ?>;
        this.requireModifyRoute = <?php echo json_encode(route('sproviders.requireModify')); ?>;
        this.area_id = <?php echo json_encode($area_id); ?>;
        this.voboDocRoute = <?php echo json_encode(route('voboDocs.voboDoc')); ?>;
        this.updateVoboDocRoute = <?php echo json_encode(route('voboDocs.updateVoboDoc')); ?>;
    }
    var oServerData = new GlobalData();
    var indexesProvidersTable = {
                'id_provider': 0, 
                'provider_short_name': 1,
                'provider_name': 2,
                'provider_rfc': 3,
                'provider_email': 4,
                'have_pen_doc':5,
                'num_pen_doc':6
            };
</script>
@endsection

@section('content')
<div class="card" id="sproviders">
    <div class="card-header">
        <h3>Proveedores</h3>
    </div>
    <div class="card-body">

        @include('sproviders.modal_documents_authorize_provider')

        <div class="grid-margin">
            @include('layouts.buttons', ['show' => true])
        </div>

        <div class="table-responsive">
            <table class="display expandable-table dataTable no-footer" id="table_providers" width="100%" cellspacing="0">
                <thead>
                    <th>id_provider</th>
                    <th>short_name</th>
                    <th>Proveedor</th>
                    <th>RFC</th>
                    <th>Correo</th>
                    <th># docs. pendientes</th>
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

    </script>

    @include('layouts.table_jsControll', [
                                            'table_id' => 'table_providers',
                                            'colTargets' => [0],
                                            'colTargetsSercheable' => [1],
                                            'select' => true,
                                            'show' => true,
                                            // 'edit_modal' => true,
                                            // 'delete' => true,
                                        ] )
    <script type="text/javascript" src="{{ asset('myApp/SProviders/vue_documentProviders.js') }}"></script>
    <script type="text/javascript" src="{{ asset('myApp/Utils/datatablesUtils.js') }}"></script>
    <script type="text/javascript">
        $(document).ready(function() {
            // drawTable('table_providers', oServerData.lProviders);
            drawTableJson('table_providers', oServerData.lProviders, 
                'id_provider', 
                'provider_short_name',
                'provider_name',
                'provider_rfc',
                'provider_email',
                'number_pen_doc'
            );
        })
    </script>
@endsection