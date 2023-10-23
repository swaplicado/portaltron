@extends('layouts.principal')

@section('headStyles')
    <link href="{{ asset('myApp/Utils/SDatePicker/css/datepicker.min.css') }}" rel="stylesheet" />
@endsection

@section('headJs')
<script>
    function GlobalData(){
        this.lProviders = <?php echo json_encode($lProviders) ?>;
        this.lPurchaseOrders = <?php echo json_encode($lPurchaseOrders) ?>;
        this.getPurchaseOrdersRoute = <?php echo json_encode(route('purchaseOrders.getPurchaseOrdersManager')) ?>;
        this.lStatus = <?php echo json_encode($lStatus) ?>;
        this.year = <?php echo json_encode($year) ?>;
        this.getRowsRoute = <?php echo json_encode(route('purchaseOrders.getRows')) ?>;
    }
    var oServerData = new GlobalData();
    var indexesPurchaseOrdersTable = {
            'idYear': 0,
            'idDoc': 1,
            'date': 2,
            'excRate': 3,
            'taxCharged': 4,
            'taxRetained': 5,
            'stot': 6,
            'id_status': 7,
            'Proveedor': 8,
            'dateFormat': 9,
            'reference': 10,
            'bpb': 11,
            'status': 12,
            'dateStartCred': 13,
            'daysCred': 14,
            'total': 15,
            'delivery_date': 16,
        };

    var indexesEtyPurchaseOrderTable = {
            'idEty': 0,
            'conceptKey': 1,
            'ref': 2,
            'concept': 3,
            'unit': 4,
            'priceUnit': 5,
            'qty': 6,
            'taxCharged': 7,
            'taxRetained': 8,
            'sTot': 9,
            'tot': 10,
        };

</script>
@endsection

@section('content')
  
<div class="card" id="purchaseOrders">
    <div class="card-header">
        <h3>Ordenes de compra</h3>
    </div>
    <div class="card-body">
        <div class="grid-margin">
            <span class="nobreak">
                <label for="provider_filter">Seleccione proveedor: </label>
                <select class="select2-class form-control" name="provider_filter" id="provider_filter"></select>
            </span>
            <button type="button" class="btn btn-primary" v-on:click="getPurchaseOrdersProvider()">
                Consultar
            </button>
        </div>

        <div v-show="showProvider">

            <template style="overflow-y: scroll;">
                @include('purchaseOrders.modal_purchase_orders_manager')
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
                    <button type="button" class="btn btn-secondary" v-on:click="year--">
                        <span class="bx bx-minus"></span>
                    </button>
                </div>
                <input type="number" class="form-control" style="max-width: 7rem;" readonly v-model="year">
                <div class="input-group-append">
                    <button type="button" class="btn btn-secondary" v-on:click="year++">
                        <span class="bx bx-plus"></span>
                    </button>
                </div>
            </div>
            <button class="btn btn-primary" v-on:click="getPurchaseOrdersProvider()"><span class="bx bx-search"></span></button>
            <br>--}}
            <br>
            <div class="table-responsive">
                <table class="display expandable-table dataTable no-footer" id="table_purchase_orders" width="100%" cellspacing="0">
                    <thead>
                        <th>id_year</th>
                        <th>id_dps</th>
                        <th>date</th>
                        <th>extRate</th>
                        <th>taxCharged</th>
                        <th>taxRetained</th>
                        <th>stot</th>
                        <th>id_status</th>
                        <th style="text-align: center">Proveedor</th>
                        <th style="text-align: center">F. Creación</th>
                        <th style="text-align: center">Folio</th>
                        <th style="text-align: center">Sucursal</th>
                        <th style="text-align: center">Estatus</th>
                        <th style="text-align: center">Inicio credito</th>
                        <th style="text-align: center">Días credito</th>
                        <th style="text-align: center">Total</th>
                        <th style="text-align: center">Fecha entrega</th>
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
                    col_status = parseInt( data[indexesPurchaseOrdersTable.id_status] );

                    if(settings.nTable.id == 'table_purchase_orders'){
                        let iStatus = parseInt( $('#status_filter').val(), 10 );
                        return iStatus == col_status || iStatus == 0;
                    }

                    return true;
                }
            );
            
            $('#status_filter').change( function() {
                table['table_purchase_orders'].draw();
            });
        });
    </script>

    @include('layouts.table_jsControll', [
                                            'table_id' => 'table_purchase_orders',
                                            'colTargets' => [0,1,2,3,4,5,6,13],
                                            'colTargetsSercheable' => [7],
                                            'colTargetsNoOrder' => [8,9,11,13,14,15],
                                            'select' => true,
                                            'show' => true,
                                            'order' => [[2, 'desc'], [10, 'desc']],
                                            'colTargetsAlignRight' =>[14],
                                            'colTargetsAlignCenter' =>[8,9,10,11,12,13,15],
                                        ] )

    @include('layouts.table_jsControll', [
                                            'table_id' => 'table_rows',
                                            'colTargets' => [0,1,2],
                                            'colTargetsSercheable' => [],
                                            'colTargetsAlignRight' =>[5,7,8,9,10],
                                            'colTargetsAlignCenter' =>[1,2,3,4,6],
                                        ] )

    <script type="text/javascript" src="{{ asset('myApp/Utils/datatablesUtils.js') }}"></script>
    <script type="text/javascript" src="{{ asset('myApp/PurchaseOrders/vue_purchaseOrdersManager.js') }}"></script>
    <script src="{{ asset('myApp/Utils/SDatePicker/js/datepicker-full.min.js') }}"></script>

    <script type="text/javascript">
        function drawTablePurchaseOrders(lPurchaseOrders){
            var arrOC = [];
            for (let oc of lPurchaseOrders) {
                arrOC.push(
                    [
                        oc.idYear,
                        oc.idDoc,
                        oc.date,
                        oc.excRate,
                        (oc.excRate == 1 ? oc.taxCharged : oc.taxChargedCur),
                        (oc.excRate == 1 ? oc.taxRetained : oc.taxRetainedCur),
                        (oc.excRate == 1 ? oc.stot : oc.stotCur),
                        oc.id_status,
                        oc.provider_name,
                        oc.dateFormat,
                        oc.numRef,
                        oc.bpb,
                        oc.status,
                        oc.dateStartCred,
                        oc.daysCred,
                        (oc.excRate == 1 ? oc.tot.toLocaleString('es-MX', { minimumFractionDigits: 2, maximumFractionDigits: 2 })+ ' ' +oc.fCurKey: 
                                            oc.totCur.toLocaleString('es-MX', { minimumFractionDigits: 2, maximumFractionDigits: 2 })+ ' '+oc.fCurKey),
                        (oc.delivery_date != null ? oc.delivery_date : 'Sin fecha de entrega')
                    ]
                )
            }
            drawTable('table_purchase_orders', arrOC);
        };

        $(document).ready(function() {
            drawTablePurchaseOrders(oServerData.lDpsComp);
        })
    </script>
    <script>
        var elemDatePicker = document.getElementById('myDatePicker');
        var datepicker = new Datepicker(elemDatePicker, {
            language: 'es',
            format: 'dd-mm-yyyy',
            // minDate: null,
        });
    </script>
@endsection