@extends('layouts.principal')

@section('headStyles')

@endsection

@section('headJs')
<script>
    function GlobalData(){
        this.lNotaCredito = <?php echo json_encode($lNotaCredito) ?>;
        this.lStatus = <?php echo json_encode($lStatus) ?>;
        this.year = <?php echo json_encode($year) ?>;
        this.lAreas = <?php echo json_encode($lAreas) ?>;
        this.default_area_id = <?php echo json_encode($default_area_id) ?>;
        this.saveNotaCreditoRoute = <?php echo json_encode(route('notaCredito.saveNotaCredito')) ?>;
        this.getNotaCreditoRoute = <?php echo json_encode(route('notaCredito.getNotaCredito')) ?>;
    }
    var oServerData = new GlobalData();
    var indexesNCTable = {
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
            'serie': 10,
            'folio': 11,
            'status': 12,
            'comments': 13,
            'reference': 14,
            'have_pdf': 15,
            'have_xml': 16,
        };
</script>
@endsection

@section('content')
  
<div class="card" id="notaCredito">
    <div class="card-header">
        <h3>Notas de crédito</h3>
    </div>
    <div class="card-body">

        <template style="overflow-y: scroll;">
            @include('notaCredito.modal_notaCredito')
        </template>

        <div class="grid-margin">
            @include('layouts.buttons', ['show' => true, 'upload' => true])
            <span class="nobreak">
                <label for="status_filter">Filtrar estatus: </label>
                <select class="select2-class form-control" name="status_filter" id="status_filter"></select>
            </span>
        </div>
        <br>
        <div class="table-responsive">
            <table class="display expandable-table dataTable no-footer" id="table_nota_credito" width="100%" cellspacing="0">
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
                    <th style="text-align: center">Serie</th>
                    <th style="text-align: center">Folio</th>
                    <th style="text-align: center">Estatus</th>
                    <th style="text-align: center">Comentario</th>
                    <th style="text-align: center">Referencia</th>
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

                    col_status = parseInt( data[indexesNCTable.status_id] );

                    if(settings.nTable.id == 'table_nota_credito'){
                        let iStatus = parseInt( $('#status_filter').val(), 10 );
                        return iStatus == col_status || iStatus == 0;
                    }

                    return false;
                }
            );
            
            $('#status_filter').change( function() {
                table['table_nota_credito'].draw();
            });

        });
    </script>

    @include('layouts.table_jsControll', [
                                            'table_id' => 'table_nota_credito',
                                            'colTargets' => [0,1,2,5,6,9,10],
                                            'colTargetsSercheable' => [3,4],
                                            'colTargetsNoOrder' => [7,8,13,14,15],
                                            'select' => true,
                                            'show' => true,
                                            'upload' => true,
                                            'order' => [[0, 'desc']],
                                            'colTargetsAlignCenter' =>[6,7,8,9,10,11,12,13,14,15,16],
                                        ] )

    <script type="text/javascript" src="{{ asset('myApp/Utils/datatablesUtils.js') }}"></script>
    <script type="text/javascript" src="{{ asset('myApp/notaCredito/vue_notaCredito.js') }}"></script>
    <script type="text/javascript">
        function drawTableNotaCredito(lNotaCredito){
            var arrNC = [];
            for (let dps of lNotaCredito) {
                arrNC.push(
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
                        dps.serie_n,
                        dps.folio_n,
                        dps.status,
                        dps.requester_comment_n,
                        dps.reference_string,
                        ((dps.pdf_url_n != null && dps.pdf_url_n != "") ? 'Cargado' : 'Sin cargar'),
                        ((dps.xml_url_n != null && dps.xml_url_n != "") ? 'Cargado' : 'Sin cargar'),
                    ]
                )
            }
            drawTable('table_nota_credito', arrNC);
        };

        $(document).ready(function() {
            drawTableNotaCredito(oServerData.lNotaCredito);
        })
    </script>
@endsection