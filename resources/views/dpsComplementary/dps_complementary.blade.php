@extends('layouts.principal')

@section('headStyles')

@endsection

@section('headJs')
<script>
    function GlobalData(){
        this.lDpsComp = <?php echo json_encode($lDpsComp) ?>;
        this.lStatus = <?php echo json_encode($lStatus) ?>;
        this.year = <?php echo json_encode($year) ?>;
        this.lStatus = <?php echo json_encode($lStatus) ?>;
        this.lTypes = <?php echo json_encode($lTypes) ?>;
        this.saveComplementsRoute = <?php echo json_encode(route('dpsComplementary.SaveComplements')) ?>;
        this.GetComplementsRoute = <?php echo json_encode(route('dpsComplementary.GetComplements')) ?>;
        this.getCompByYearRoute = <?php echo json_encode(route('dpsComplementary.getCompByYear')) ?>;
    }
    var oServerData = new GlobalData();
    var indexesDpsCompTable = {
            'id_dps': 0,
            'ext_id_year': 1,
            'ext_id_doc': 2,
            'type_doc_id': 3,
            'status_id': 4,
            'is_opened': 5,
            'reference_doc_n': 6,
            'type': 7,
            'folio': 8,
            'status': 9,
            'purchase_order': 10,
            'have_pdf': 11,
            'have_xml': 12,
        };
</script>
@endsection

@section('content')
  
<div class="card" id="dpsComplementary">
    <div class="card-header">
        <h3>Ordenes de compra</h3>
    </div>
    <div class="card-body">

        <template style="overflow-y: scroll;">
            @include('dpsComplementary.modal_dps_complementary')
        </template>

        <div class="grid-margin">
            @include('layouts.buttons', ['show' => true, 'upload' => true])
            <span class="nobreak">
                <label for="status_filter">Filtrar Tipo: </label>
                <select class="select2-class form-control" name="type_filter" id="type_filter"></select>
            </span>
            <span class="nobreak">
                <label for="status_filter">Filtrar estatus: </label>
                <select class="select2-class form-control" name="status_filter" id="status_filter"></select>
            </span>
        </div>
        <div class="input-group" style="display: inline-flex; width: auto">
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
        <button class="btn btn-primary" v-on:click="getlDpsCompByYear()"><span class="bx bx-search"></span></button>
        <br>
        <br>
        <div class="table-responsive">
            <table class="display expandable-table dataTable no-footer" id="table_dps_complementary" width="100%" cellspacing="0">
                <thead>
                    <th>id_dps</th>
                    <th>ext_id_year</th>
                    <th>ext_id_doc</th>
                    <th>type_doc_id</th>
                    <th>status_id</th>
                    <th>is_opened</th>
                    <th>reference_doc_n</th>
                    <th>Tipo</th>
                    <th>Folio</th>
                    <th>Estatus</th>
                    <th>Orden compra</th>
                    <th>PDF</th>
                    <th>XML</th>
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
                    let col_type = null;

                    col_status = parseInt( data[indexesDpsCompTable.status_id] );
                    col_type = parseInt( data[indexesDpsCompTable.type_doc_id] );

                    if(settings.nTable.id == 'table_dps_complementary'){
                        let iType = parseInt( $('#type_filter').val(), 10 );
                        let iStatus = parseInt( $('#status_filter').val(), 10 );
                        if(col_type == iType || iType == 0){
                            return iStatus == col_status || iStatus == 0;
                        }
                    }

                    return false;
                }
            );
            
            $('#type_filter').change( function() {
                table['table_dps_complementary'].draw();
            });

            $('#status_filter').change( function() {
                table['table_dps_complementary'].draw();
            });

        });
    </script>

    @include('layouts.table_jsControll', [
                                            'table_id' => 'table_dps_complementary',
                                            'colTargets' => [0,1,2,5,6],
                                            'colTargetsSercheable' => [3,4],
                                            'select' => true,
                                            'show' => true,
                                            'upload' => true,
                                            'order' => [[0, 'desc']],
                                        ] )

    <script type="text/javascript" src="{{ asset('myApp/Utils/datatablesUtils.js') }}"></script>
    <script type="text/javascript" src="{{ asset('myApp/DpsComplementary/vue_dpsComplementary.js') }}"></script>
    <script type="text/javascript">
        function drawTableDpsComplementary(lDpsComp){
            var arrDpcComp = [];
            for (let dps of lDpsComp) {
                arrDpcComp.push(
                    [
                        dps.id_dps,
                        dps.ext_id_year,
                        dps.ext_id_doc,
                        dps.type_doc_id,
                        dps.status_id,
                        dps.is_opened,
                        dps.reference_doc_n,
                        dps.type,
                        dps.folio_n,
                        dps.status,
                        dps.reference_folio,
                        ((dps.pdf_url_n != null && dps.pdf_url_n != "") ? 'Cargado' : 'Sin cargar'),
                        ((dps.xml_url_n != null && dps.xml_url_n != "") ? 'Cargado' : 'Sin cargar'),
                    ]
                )
            }
            drawTable('table_dps_complementary', arrDpcComp);
        };

        $(document).ready(function() {
            drawTableDpsComplementary(oServerData.lDpsComp);
        })
    </script>
@endsection