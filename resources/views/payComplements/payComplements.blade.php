@extends('layouts.principal')

@section('headStyles')

@endsection

@section('headJs')
<script>
    function GlobalData(){
        this.lDpsPayComp = <?php echo json_encode($lDpsPayComp) ?>;
        this.lStatus = <?php echo json_encode($lStatus) ?>;
        this.year = <?php echo json_encode($year) ?>;
        this.lAreas = <?php echo json_encode($lAreas) ?>;
        this.default_area_id = <?php echo json_encode($default_area_id) ?>;
        this.savePayComplementRoute = <?php echo json_encode(route('payComplement.savePayComplement')) ?>;
        this.getPayComplementRoute = <?php echo json_encode(route('payComplement.getPayComplement')) ?>;
        this.getlPayCompByYearRoute = <?php echo json_encode(route('payComplement.getlPayCompByYear')) ?>;
    }
    var oServerData = new GlobalData();
    var indexesPayCompTable = {
            'id_dps': 0,
            'ext_id_year': 1,
            'ext_id_doc': 2,
            'type_doc_id': 3,
            'status_id': 4,
            'is_opened': 5,
            'reference_doc_n': 6,
            'dateFormat': 7,
            'type': 8,
            'area': 9,
            'folio': 10,
            'comments': 11,
            'status': 12,
            'comments': 13,
            'purchase_order': 14,
            'have_pdf': 15,
            'have_xml': 16,
        };
</script>
@endsection

@section('content')
  
<div class="card" id="payComplements">
    <div class="card-header">
        <h3>CFDI de pago</h3>
    </div>
    <div class="card-body">

        <template style="overflow-y: scroll;">
            @include('payComplements.modal_payComplements')
        </template>

        <div class="grid-margin">
            @include('layouts.buttons', ['show' => true, 'upload' => true])
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
        <button class="btn btn-primary" v-on:click="getlPayCompByYear()"><span class="bx bx-search"></span></button>
        <br>--}}
        <br>
        <div class="table-responsive">
            <table class="display expandable-table dataTable no-footer" id="table_pay_complements" width="100%" cellspacing="0">
                <thead>
                    <th>id_dps</th>
                    <th>ext_id_year</th>
                    <th>ext_id_doc</th>
                    <th>type_doc_id</th>
                    <th>status_id</th>
                    <th>is_opened</th>
                    <th>reference_doc_n</th>
                    <th style="text-align: center">F. Creación</th>
                    <th style="text-align: center">Tipo</th>
                    <th style="text-align: center">Área destino</th>
                    <th style="text-align: center">Folio</th>
                    <th style="text-align: center">Ref. Factura</th>
                    <th style="text-align: center">Estatus</th>
                    <th style="text-align: center">Comentario</th>
                    <th style="text-align: center">Orden compra</th>
                    <th style="text-align: center">PDF</th>
                    <th style="text-align: center">XML</th>
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

                    col_status = parseInt( data[indexesPayCompTable.status_id] );

                    if(settings.nTable.id == 'table_pay_complements'){
                        let iStatus = parseInt( $('#status_filter').val(), 10 );
                        return iStatus == col_status || iStatus == 0;
                    }

                    return false;
                }
            );
            
            $('#status_filter').change( function() {
                table['table_pay_complements'].draw();
            });

        });
    </script>

    @include('layouts.table_jsControll', [
                                            'table_id' => 'table_pay_complements',
                                            'colTargets' => [0,1,2,3,5,6,14],
                                            'colTargetsSercheable' => [4],
                                            'colTargetsNoOrder' => [7,8,11,13,14,15,16],
                                            'select' => true,
                                            'show' => true,
                                            'upload' => true,
                                            'order' => [[0, 'desc']],
                                            'colTargetsAlignCenter' =>[7,8,9,10,11,12,13,14,15,16],
                                        ] )

    <script type="text/javascript" src="{{ asset('myApp/Utils/datatablesUtils.js') }}"></script>
    <script type="text/javascript" src="{{ asset('myApp/payComplements/vue_payComplements.js') }}"></script>
    <script type="text/javascript">
        function drawTableDpsPaycomplement(lDpsPayComp){
            var arrDpsPayComp = [];
            for (let dps of lDpsPayComp) {
                arrDpsPayComp.push(
                    [
                        dps.id_dps,
                        dps.ext_id_year,
                        dps.ext_id_doc,
                        dps.type_doc_id,
                        dps.status_id,
                        dps.is_opened,
                        dps.reference_doc_n,
                        dps.dateFormat,
                        dps.type,
                        (dps.name_area != null ? dps.name_area : 'Sin area'),
                        dps.folio_n,
                        dps.provider_comment_n,
                        dps.status,
                        dps.requester_comment_n,
                        dps.reference_folio,
                        ((dps.pdf_url_n != null && dps.pdf_url_n != "") ? 'Cargado' : 'Sin cargar'),
                        ((dps.xml_url_n != null && dps.xml_url_n != "") ? 'Cargado' : 'Sin cargar'),
                    ]
                )
            }
            drawTable('table_pay_complements', arrDpsPayComp);
        };

        $(document).ready(function() {
            drawTableDpsPaycomplement(oServerData.lDpsPayComp);
        })
    </script>
@endsection