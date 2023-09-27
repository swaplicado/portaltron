@extends('layouts.principal')

@section('headStyles')
    <link href="{{ asset('myApp/Utils/SDatePicker/css/datepicker.min.css') }}" rel="stylesheet" />
@endsection

@section('headJs')
<script>
    function GlobalData(){
        this.lPurchaseOrders = <?php echo json_encode($lPurchaseOrders) ?>;
        this.lStatus = <?php echo json_encode($lStatus) ?>;
        this.getRowsRoute = <?php echo json_encode(route('purchaseOrders.getRows')) ?>;
        this.updateRoute = <?php echo json_encode(route('purchaseOrders.update')) ?>;
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
            'bpb': 8,
            'reference': 9,
            'status': 10,
            'dateStartCred': 11,
            'daysCred': 12,
            'fCurKey': 13,
            'total': 14,
            'delivery_date': 15,
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

        <template style="overflow-y: scroll;">
            @include('purchaseOrders.modal_purchase_orders')
        </template>

        <div class="grid-margin">
            @include('layouts.buttons', ['show' => true])
            <span class="nobreak">
                <label for="status_filter">Filtrar estatus: </label>
                <select class="select2-class form-control" name="status_filter" id="status_filter"></select>
            </span>
        </div>

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
                    <th>Sucursal</th>
                    <th>Referencia</th>
                    <th>Estatus</th>
                    <th>Inicio credito</th>
                    <th>Días credito</th>
                    <th>Moneda</th>
                    <th>Total</th>
                    <th>Fecha entrega</th>
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
                                            'colTargets' => [0,1,2,3,4,5,6],
                                            'colTargetsSercheable' => [7],
                                            'select' => true,
                                            'show' => true,
                                        ] )

    @include('layouts.table_jsControll', [
                                            'table_id' => 'table_rows',
                                            'colTargets' => [0,1,2],
                                            'colTargetsSercheable' => [],
                                        ] )

    <script type="text/javascript" src="{{ asset('myApp/Utils/datatablesUtils.js') }}"></script>
    <script type="text/javascript" src="{{ asset('myApp/PurchaseOrders/vue_purchaseOrders.js') }}"></script>
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
                        oc.bpb,
                        oc.numRef,
                        oc.status,
                        oc.dateStartCred,
                        oc.daysCred,
                        oc.fCurKey,
                        (oc.excRate == 1 ? oc.tot : oc.totCur),
                        oc.delivery_date
                    ]
                )
            }
            drawTable('table_purchase_orders', arrOC);
        };

        $(document).ready(function() {
            drawTablePurchaseOrders(oServerData.lPurchaseOrders);
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