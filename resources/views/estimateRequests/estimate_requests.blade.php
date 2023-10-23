@extends('layouts.principal')

@section('headStyles')
    <link href="{{ asset('myApp/Utils/SDatePicker/css/datepicker.min.css') }}" rel="stylesheet" />
@endsection

@section('headJs')
<script>
    function GlobalData(){
        this.lEstimateRequest = <?php echo json_encode($lEstimateRequest) ?>;
        this.lStatus = <?php echo json_encode($lStatus) ?>;
        this.getRowsRoute = <?php echo json_encode(route('estimateRequest.getRows')) ?>;
        this.getEstimateRequestRoute = <?php echo json_encode(route('estimateRequest.getEstimateRequest')) ?>;
        this.Year = <?php echo json_encode($Year) ?>;
    }
    var oServerData = new GlobalData();
    var indexesEstimateRequestTable = {
            'idYear': 0,
            'idInternal': 1,
            'idEstimateRequest': 2,
            'number': 3,
            'fecha': 4,
            'mailsTo': 5,
            'subject': 6,
            'body': 7,
            'opened': 8,
        };

    var indexesEtyEstimateRequestTable = {
            'idEty': 0,
            'item': 1,
            'idItem': 2,
            'qty': 3,
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

        <template style="overflow-y: scroll;">
            @include('estimateRequests.modal_estimate_request')
        </template>

        <div class="grid-margin">
            @include('layouts.buttons', ['show' => true])
            <span class="nobreak">
                <label for="status_filter">Filtrar estatus: </label>
                <select class="select2-class form-control" name="status_filter" id="status_filter"></select>
            </span>
        </div>
        {{--<div class="input-group" style="display: inline-flex; width: auto">
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
        <button class="btn btn-primary" v-on:click="getEstimateRequest()"><span class="bx bx-search"></span></button>
        <br>--}}
        <br>
        <div class="table-responsive">
            <table class="display expandable-table dataTable no-footer" id="table_estimate_request" width="100%" cellspacing="0">
                <thead>
                    <th>id_year</th>
                    <th>id_int</th>
                    <th>id_ext</th>
                    <th style="text-align: center">Folio</th>
                    <th style="text-align: center">Fecha</th>
                    <th style="text-align: center">Enviado a</th>
                    <th style="text-align: center">Asunto</th>
                    <th>Mensaje</th>
                    <th>abierto</th>
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
                                            'colTargets' => [0,1,2,7],
                                            'colTargetsSercheable' => [8],
                                            'colTargetsAlignRight' =>[],
                                            'colTargetsAlignCenter' =>[3,4,5,6],
                                            'select' => true,
                                            'show' => true,
                                            'order' => [[3, 'desc'], [4, 'desc']],
                                        ] )

    @include('layouts.table_jsControll', [
                                            'table_id' => 'table_rows',
                                            'colTargets' => [0,2,4],
                                            'colTargetsSercheable' => [],
                                            'colTargetsAlignCenter' =>[1,3,5],
                                        ] )

    <script type="text/javascript" src="{{ asset('myApp/Utils/datatablesUtils.js') }}"></script>
    <script type="text/javascript" src="{{ asset('myApp/EstimateRequest/vue_estimateRequest.js') }}"></script>
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
                        er.dateFormat,
                        er.mailsTo,
                        er.subject,
                        er.body,
                        er.is_opened
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