<script>
    var table = new Object();
    table["{{$table_id}}"] = '';
    $(document).ready(function() {
        table['{{$table_id}}'] = $('#{{$table_id}}').DataTable({
                    "language": {
                        "sProcessing":     "Procesando...",
                        "sLengthMenu":     "Mostrar _MENU_ registros",
                        "sZeroRecords":    "No se encontraron resultados",
                        "sEmptyTable":     "Ningún dato disponible en esta tabla",
                        "sInfo":           "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
                        "sInfoEmpty":      "Mostrando registros del 0 al 0 de un total de 0 registros",
                        "sInfoFiltered":   "(filtrado de un total de _MAX_ registros)",
                        "sInfoPostFix":    "",
                        "sSearch":         "Buscar:",
                        "sUrl":            "",
                        "sInfoThousands":  ",",
                        "sLoadingRecords": "Cargando...",
                        "oPaginate": {
                            "sFirst":    "Primero",
                            "sLast":     "Último",
                            "sNext":     "Siguiente",
                            "sPrevious": "Anterior"
                        },
                        "oAria": {
                            "sSortAscending":  ": Activar para ordenar la columna de manera ascendente",
                            "sSortDescending": ": Activar para ordenar la columna de manera descendente"
                        }
                    },
                    // "scrollX": true,
                    "responsive": false,
                    @if(isset($noInfo))
                        "info": false,
                    @endif
                    @if(isset($noSearch))
                        "searching": false,
                    @endif
                    @if(isset($noPaging))
                        "paging": false,
                    @endif
                    @if(isset($noColReorder))
                        "colReorder": false,
                    @else
                        "colReorder": true,
                    @endif
                    @if(isset($noSort))
                        "bSort": false,
                    @endif
                    @if(!isset($noDom))
                        "dom": 'Bfrtip',
                    @endif
                    @if(isset($order))
                        "order": <?php echo json_encode($order) ?>,
                    @endif
                    @if(isset($responsive))
                        "responsive": true,
                    @endif
                    @if(isset($lengthMenu))
                        "lengthMenu": <?php echo json_encode($lengthMenu) ?>,
                    @else
                        "lengthMenu": [
                            [ 10, 25, 50, 100, -1 ],
                            [ 'Mostrar 10', 'Mostrar 25', 'Mostrar 50', 'Mostrar 100', 'Mostrar todo' ]
                        ],
                    @endif
                    @if(isset($ordering))
                        "ordering": true,
                    @endif
                    "columnDefs": [
                        {
                            "targets": <?php echo json_encode($colTargets) ?>,
                            "visible": false,
                            "searchable": false,
                            "orderable": false,
                        },
                        {
                            "targets": <?php echo json_encode($colTargetsSercheable) ?>,
                            "visible": false,
                            "searchable": true,
                            "orderable": false,
                        },
                        {
                            "orderable": false,
                            "targets": "no-sort",
                        }
                    ],
                    "buttons": [
                            'pageLength',
                            {
                                extend: 'copy',
                                text: 'Copiar'
                            }, 
                            'csv', 
                            'excel', 
                            {
                                extend: 'print',
                                text: 'Imprimir'
                            }
                        ],
                    "initComplete": function(){ 
                        // $("#{{$table_id}}").show();
                        $("#{{$table_id}}").wrap("<div style='overflow:auto; width:100%;position:relative;'></div>");
                    }
                });
            
        @if(isset($select))
            $('#{{$table_id}} tbody').on('click', 'tr', function () {
                if(!$(this).hasClass('noSelectableRow')){
                    if ($(this).hasClass('selected')) {
                        $(this).removeClass('selected');
                    }
                    else {
                        table['{{$table_id}}'].$('tr.selected').removeClass('selected');
                        $(this).addClass('selected');
                    }
                }
            });
        @endif

        /**
         * Crear un registro con vue modal
         */
        @if(isset($create_modal))
            $('#btn_create').click(function () {        
                app.createModal();
            });
        @endif

        /**
         * Editar un registro con vue modal
         */
        @if(isset($edit_modal))
            $('#btn_edit').click(function () {
                if (table['{{$table_id}}'].row('.selected').data() == undefined) {
                    SGui.showError("Debe seleccionar un renglón");
                    return;
                }
        
                app.editModal(table['{{$table_id}}'].row('.selected').data());
            });
        @endif

        /**
         * Borrar un registro con vue
         */
        @if(isset($delete))
            $('#btn_delete').click(function  () {
                if (table['{{$table_id}}'].row('.selected').data() == undefined) {
                    SGui.showError("Debe seleccionar un renglón");
                    return;
                }
                app.deleteRegistry(table['{{$table_id}}'].row('.selected').data());
            });
        @endif

        /**
         * Enviar un registro con vue
         */
        @if(isset($send))
            $('#btn_send').click(function  () {
                if (table['{{$table_id}}'].row('.selected').data() == undefined) {
                    SGui.showError("Debe seleccionar un renglón");
                    return;
                }
                app.sendRegistry(table['{{$table_id}}'].row('.selected').data());
            });
        @endif

        /**
         * Abrir modal con datos de tabla 
         */
        @if(isset($show))
            $('#btn_show').click(function () {
                if(table['{{$table_id}}'].row('.selected').data() == undefined){
                    SGui.showError("Debe seleccionar un renglón");
                    return;
                }

                app.showModal(table['{{$table_id}}'].row('.selected').data());
            });
        @endif
    });
</script>