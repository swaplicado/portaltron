@extends('layouts.principal')

@section('headStyles')

@endsection

@section('headJs')
    <script>
        function GlobalData() {
            this.lDpsComp = <?php echo json_encode($lDpsComp); ?>;
            this.year = <?php echo json_encode($year); ?>;
            this.lStatus = <?php echo json_encode($lStatus); ?>;
            this.lTypes = <?php echo json_encode($lTypes); ?>;
            this.lConstants = <?php echo json_encode($lConstants); ?>;
            this.lProviders = <?php echo json_encode($lProviders); ?>;
            this.getcomplementsManagerRoute = <?php echo json_encode(route('dpsComplementary.getComplementsManager')); ?>;
            this.GetComplementsRoute = <?php echo json_encode(route('dpsComplementary.GetComplements')); ?>;
            this.getDpsComplementManagerRoute = <?php echo json_encode(route('dpsComplementary.getDpsComplementManager')); ?>;
            this.setVoboComplementRoute = <?php echo json_encode(route('dpsComplementary.setVoboComplement')); ?>;
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
            'provider_name': 7,
            'dateFormat': 8,
            'type': 9,
            'area': 10,
            'folio': 11,
            'status': 12,
            'purchase_order': 13,
            'comments': 14,
            'have_pdf': 15,
            'have_xml': 16,
        };
    </script>
@endsection

@section('content')
    <div class="card" id="dpsComplementaryManager">
        <div class="card-header">
            <h3>Facturas y notas de crédito</h3>
        </div>
        <div class="card-body">
            <div class="grid-margin">
                <span class="nobreak">
                    <label for="provider_filter">Seleccione proveedor: </label>
                    <select class="select2-class form-control" style="min-width: 20rem; !important" name="provider_filter"
                        id="provider_filter"></select>
                </span>
                <button type="button" class="btn btn-primary" v-on:click="getComplementsProvider()">
                    Consultar
                </button>
            </div>

            <div v-show="showProvider">
                <template style="overflow-y: scroll;">
                    @include('dpsComplementary.modal_dps_complementary_manager')
                </template>

                <div class="grid-margin">
                    @include('layouts.buttons', ['show' => true])
                    <span class="nobreak">
                        <label for="status_filter">Filtrar Tipo: </label>
                        <select class="select2-class form-control" name="type_filter" id="type_filter"></select>
                    </span>
                    <span class="nobreak">
                        <label for="status_filter">Filtrar estatus: </label>
                        <select class="select2-class form-control" name="status_filter" id="status_filter"></select>
                    </span>
                </div>
                {{-- <div class="input-group" style="display: inline-flex; width: auto">
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
        <br> --}}
                <br>
                <div class="table-responsive">
                    <table class="display expandable-table dataTable no-footer" id="table_dps_complementary" width="100%"
                        cellspacing="0">
                        <thead>
                            <th>id_dps</th>
                            <th>ext_id_year</th>
                            <th>ext_id_doc</th>
                            <th>type_doc_id</th>
                            <th>status_id</th>
                            <th>is_opened</th>
                            <th>reference_doc_n</th>
                            <th style="text-align: center">Proveedor</th>
                            <th style="text-align: center">F. Creación</th>
                            <th style="text-align: center">Tipo</th>
                            <th style="text-align: center">Área destino</th>
                            <th style="text-align: center">Folio</th>
                            <th style="text-align: center">Estatus</th>
                            <th style="text-align: center">Orden compra</th>
                            <th style="text-align: center">Comentario</th>
                            <th style="text-align: center">PDF</th>
                            <th style="text-align: center">XML</th>
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
        $(document).ready(function() {
            //filtros para datatables
            $.fn.dataTable.ext.search.push(
                function(settings, data, dataIndex) {
                    let col_status = null;
                    let col_type = null;

                    col_status = parseInt(data[indexesDpsCompTable.status_id]);
                    col_type = parseInt(data[indexesDpsCompTable.type_doc_id]);

                    if (settings.nTable.id == 'table_dps_complementary') {
                        let iType = parseInt($('#type_filter').val(), 10);
                        let iStatus = parseInt($('#status_filter').val(), 10);
                        if (col_type == iType || iType == 0) {
                            return iStatus == col_status || iStatus == 0;
                        }
                    }

                    return false;
                }
            );

            $('#type_filter').change(function() {
                table['table_dps_complementary'].draw();
            });

            $('#status_filter').change(function() {
                table['table_dps_complementary'].draw();
            });

        });
    </script>

    @include('layouts.table_jsControll', [
        'table_id' => 'table_dps_complementary',
        'colTargets' => [0, 1, 2, 5, 6, 11],
        'colTargetsSercheable' => [3, 4],
        'colTargetsNoOrder' => [7, 8, 12, 13, 14, 15, 16],
        'select' => true,
        'show' => true,
        'upload' => true,
        'order' => [[0, 'desc']],
        'colTargetsAlignCenter' => [6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16],
    ])

    <script type="text/javascript" src="{{ asset('myApp/Utils/datatablesUtils.js') }}"></script>
    <script type="text/javascript" src="{{ asset('myApp/DpsComplementary/vue_dpsComplementaryManager.js') }}"></script>
    <script type="text/javascript">
        function drawTableDpsComplementary(lDpsComp) {
            var arrDpsComp = [];
            for (let dps of lDpsComp) {
                arrDpsComp.push(
                    [
                        dps.id_dps,
                        dps.ext_id_year,
                        dps.ext_id_doc,
                        dps.type_doc_id,

                        ((dps.type_doc_id == oServerData.lConstants.FACTURA) ?
                            (dps.check_status == 2 && dps.status_id == oServerData.lConstants.FAC_STATUS_NUEVO ?
                                (dps.is_accept == 1 ? oServerData.lConstants.FAC_STATUS_PENDIENTE : dps.status_id) :
                                dps.status_id) :
                            (dps.check_status == 2 && dps.status_id == oServerData.lConstants.NC_STATUS_NUEVO ?
                                (dps.is_accept == 1 ? oServerData.lConstants.NC_STATUS_PENDIENTE : dps.status_id) :
                                dps.status_id)),

                        dps.is_opened,
                        dps.reference_doc_n,
                        dps.provider_name,
                        dps.dateFormat,
                        dps.type,
                        (dps.name_area != null ? dps.name_area : 'Sin area'),
                        dps.folio_n,

                        ((dps.type_doc_id == oServerData.lConstants.FACTURA) ?
                            (dps.check_status == 2 && dps.status_id == oServerData.lConstants.FAC_STATUS_NUEVO ?
                                (dps.is_accept == 1 ? 'Pendiente' : dps.status) :
                                dps.status) :
                            (dps.check_status == 2 && dps.status_id == oServerData.lConstants.NC_STATUS_NUEVO ?
                                (dps.is_accept == 1 ? 'Pendiente' : dps.status) :
                                dps.status)),

                        dps.reference_folio,
                        dps.requester_comment_n,
                        ((dps.pdf_url_n != null && dps.pdf_url_n != "") ? 'Cargado' : 'Sin cargar'),
                        ((dps.xml_url_n != null && dps.xml_url_n != "") ? 'Cargado' : 'Sin cargar'),
                    ]
                )
            }
            drawTable('table_dps_complementary', arrDpsComp);
        };
    </script>
    <script>
        $(document).ready(function() {
            drawTableDpsComplementary(oServerData.lDpsComp);
        })
    </script>
@endsection
