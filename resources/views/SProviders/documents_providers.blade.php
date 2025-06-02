@extends('layouts.principal')

@section('headJs')
<script>
    function GlobalData(){
        this.lProviders = <?php echo json_encode($lProviders); ?>;
        this.lConstants = <?php echo json_encode($lConstants); ?>;
        this.lDocs = <?php echo json_encode($lDocs); ?>;
        this.lAreas = <?php echo json_encode($lAreas); ?>;
        this.getProviderRoute = <?php echo json_encode(route('sproviders.getProviderToDocuments')); ?>;
        this.approveRoute = <?php echo json_encode(route('sproviders.approve')); ?>;
        this.rejectRoute = <?php echo json_encode(route('sproviders.reject')); ?>;
        this.requireModifyRoute = <?php echo json_encode(route('sproviders.requireModify')); ?>;
        this.updateAreaRoute = <?php echo json_encode(route('sproviders.updateArea')); ?>;
        this.area_id = <?php echo json_encode($area_id); ?>;
        this.voboDocRoute = <?php echo json_encode(route('voboDocs.voboDoc')); ?>;
        this.updateVoboDocRoute = <?php echo json_encode(route('voboDocs.updateVoboDoc')); ?>;
    }
    var oServerData = new GlobalData();
    var indexesProvidersTable = {
                'id_provider': 0, 
                'provider_name': 1,
                'provider_short_name': 2,
                'provider_rfc': 3,
                'provider_fiscal_regime': 4,
                'provider_area': 5,
                'provider_email': 6,
                'have_pen_doc':7,
                'num_pen_doc':8
            };
</script>
@endsection

@section('content')
<div class="card" id="sproviders">
    <div class="card-header">
        @if ($isFatherArea)
            <h3>Todos los Proveedores</h3>
        @else
            <h3>Mis proveedores</h3>
        @endif
    </div>
    <div class="card-body">

        @include('SProviders.modal_documents_authorize_provider')
        @include('SProviders.modal_update_area_provider')

        <div class="grid-margin">
            @include('layouts.buttons', ['show' => true])
            @if ($isFatherArea)
                <button type="button" class="btn btn-info btn-rounded btn-icon" id="btn_edit" title="Asignar nueva área">
                    <i class='bx bx-transfer-alt'></i>
                </button>
            @endif
        </div>

        <div class="table-responsive">
            <table class="display expandable-table dataTable no-footer" id="table_providers" width="100%" cellspacing="0">
                <thead>
                    <th>id_provider</th>
                    <th>Razón social</th>
                    <th>Nombre comercial</th>
                    <th>RFC</th>
                    <th>Régimen fiscal</th>
                    <th>Área</th>
                    <th>Correo</th>
                    <th># docs. cargados</th>
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
                                            'colTargetsSercheable' => [$isFatherArea ? null : 5],
                                            'select' => true,
                                            'show' => true,
                                            'edit_modal' => true
                                        ] )

    <script type="text/javascript" src="{{ asset('myApp/SProviders/vue_documentProviders.js') }}"></script>
    <script type="text/javascript" src="{{ asset('myApp/Utils/datatablesUtils.js') }}"></script>
    <script type="text/javascript">
        $(document).ready(function() {
            // drawTable('table_providers', oServerData.lProviders);
            drawTableJson('table_providers', oServerData.lProviders, 
                'id_provider', 
                'provider_name',
                'provider_short_name',
                'provider_rfc',
                'fiscal_regime',
                'area',
                'provider_email',
                'number_pen_doc'
            );
        })
    </script>
@endsection