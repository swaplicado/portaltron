@extends('layouts.principal')

@section('headStyles')
    <link href="{{ asset('myApp/Utils/SDatePicker/css/datepicker.min.css') }}" rel="stylesheet" />
@endsection

@section('headJs')
<script>
    function GlobalData(){
        this.lProviders = <?php echo json_encode($lProviders) ?>;
        this.lEstimateRequest = <?php echo json_encode($lEstimateRequest) ?>;
        this.lStatus = <?php echo json_encode($lStatus) ?>;
        this.getRowsRoute = <?php echo json_encode(route('estimateRequest.getRows')) ?>;
        this.getEstimateRequestRoute = <?php echo json_encode(route('estimateRequest.getEstimateRequestManager')) ?>;
        this.Year = <?php echo json_encode($Year) ?>;
    }
    var oServerData = new GlobalData();
    var indexesEstimateRequestTable = {
            'idYear': 0,
            'idInternal': 1,
            'idEstimateRequest': 2,
            'number': 3,
            'mailsTo': 4,
            'subject': 5,
            'body': 6,
            'opened': 7,
            'See':8,
            'dateOpen': 9
        };

    var indexesEtyEstimateRequestTable = {
            'idEty': 0,
            'qty': 1,
            'idItem': 2,
            'item': 3,
            'idUnit': 4,
            'unit': 5,
            'idEstimateRequest': 6,
        };

</script>
@endsection

@section('content')
  
<div class="card" id="estimateRequest">
    <div class="card-header">
        <h3>Solicitudes de cotización</h3>
    </div>
    <div class="card-body">
        <div class="grid-margin">
            <span class="nobreak">
                <label for="provider_filter">Seleccione proveedor: </label>
                <select class="select2-class form-control" name="provider_filter" id="provider_filter"></select>
            </span>
            <button type="button" class="btn btn-primary" v-on:click="getEstimateRequestProvider()">
                Consultar
            </button>
        </div>
        <div v-show="showProvider">
            <template style="overflow-y: scroll;">
                @include('estimateRequests.modal_estimate_request_manager')
            </template>

            <div class="grid-margin">
                @include('layouts.buttons', ['show' => true])
                <span class="nobreak">
                    <label for="status_filter">Filtrar estatus: </label>
                    <select class="select2-class form-control" name="status_filter" id="status_filter"></select>
                </span>
            </div>
            <div class="input-group" style="display: inline-flex; width: auto">
                <div class="input-group-prepend">
                    <button type="button" class="btn btn-secondary" v-on:click="Year--">
                        <span class="bx bx-minus"></span>
                    </button>
                </div>
                <input type="number" class="form-control" style="max-width: 7rem;" readonly v-model="Year">
                <div class="input-group-append">
                    <button type="button" class="btn btn-secondary" v-on:click="Year++">
                        <span class="bx bx-plus"></span>
                    </button>
                </div>
            </div>
            <button class="btn btn-primary" v-on:click="getEstimateRequestProvider()"><span class="bx bx-search"></span></button>
            <br>
            <br>
            <div class="table-responsive">
                <table class="display expandable-table dataTable no-footer" id="table_estimate_request" width="100%" cellspacing="0">
                    <thead>
                        <th>id_year</th>
                        <th>id_int</th>
                        <th>id_ext</th>
                        <th>Folio</th>
                        <th>Mail</th>
                        <th>Asunto</th>
                        <th>Mensaje</th>
                        <th>abierto</th>
                        <th>Visto</th>
                        <th>Fecha visto</th>
                    </thead>
                    <tbody>

                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
    <script>
        var self;
        moment.locale('es');
        $(document).ready(function () {
            //filtros para datatables
            $.fn.dataTable.ext.search.push(
                function( settings, data, dataIndex ) {
                    let col_status = null;
                    col_status = parseInt( data[indexesEstimateRequestTable.opened] );

                    if(settings.nTable.id == 'table_estimate_request'){
                        let iStatus = parseInt( $('#status_filter').val(), 10 );
                        return iStatus == col_status || iStatus == 2;
                    }

                    return true;
                }
            );
            
            $('#status_filter').change( function() {
                table['table_estimate_request'].draw();
            });
        });
    </script>

    @include('layouts.table_jsControll', [
                                            'table_id' => 'table_estimate_request',
                                            'colTargets' => [0,1,2,6],
                                            'colTargetsSercheable' => [7],
                                            'select' => true,
                                            'show' => true,
                                        ] )

    @include('layouts.table_jsControll', [
                                            'table_id' => 'table_rows',
                                            'colTargets' => [0,2,4],
                                            'colTargetsSercheable' => [],
                                        ] )

    <script type="text/javascript" src="{{ asset('myApp/Utils/datatablesUtils.js') }}"></script>
    <script type="text/javascript" src="{{ asset('myApp/EstimateRequest/vue_estimateRequestManager.js') }}"></script>
    <script src="{{ asset('myApp/Utils/SDatePicker/js/datepicker-full.min.js') }}"></script>

    <script type="text/javascript">
        function drawTableEstimateRequest(lEstimateRequest){
            var arrER = [];
            for (let er of lEstimateRequest) {
                arrER.push(
                    [
                        er.idYear,
                        er.idInternal,
                        er.idEstimateRequest,
                        er.number,
                        er.mailsTo,
                        er.subject,
                        er.body,
                        er.is_opened,
                        (er.is_opened == 1 ? 'Sí' : 'No'),
                        er.updatedAt
                    ]
                )
            }
            drawTable('table_estimate_request', arrER);
        };

        $(document).ready(function() {
            drawTableEstimateRequest(oServerData.lEstimateRequest);
        })
    </script>
@endsection