<div class="modal fade" id="modal_notaCredito" ref="modal" tabindex="-1" aria-labelledby="modal_notaCredito" aria-hidden="true">
    <div class="modal-dialog" style="max-width: 50rem">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modal_notaCredito"><b> Notas de crédito </b></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form class="forms-sample" action="#">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="table-responsive">
                                <table class="display expandable-table dataTable no-footer" id="table_provider_documents" width="100%" cellspacing="0">
                                    <thead>
                                        <th style="text-align: center">Referencia</th>
                                        <th style="text-align: center">Documento</th>
                                        <th></th>
                                        <th></th>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td style="text-align: center">@{{reference}}</td>
                                            <td style="text-align: center">PDF</td>
                                            <td style="text-align: center"><a :href="pdf_url" target="_blank" class="btn btn-primary">Ver</a></td>
                                            <td style="text-align: center"><a :href="pdf_url" :download="pdf_url" class="btn btn-info">Descargar</a></td>
                                        </tr>
                                        <tr>
                                            <td style="text-align: center">@{{reference}}</td>
                                            <td style="text-align: center">XML</td>
                                            <td style="text-align: center"><a :href="xml_url" target="_blank" class="btn btn-primary">Ver</a></td>
                                            <td style="text-align: center"><a :href="xml_url" :download="xml_url" class="btn btn-info">Descargar</a></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <br>
                    <div class="row" v-if="is_reject == 1">
                        <div class="col-md-12">
                            <div class="">
                                <label class="col-sm-12 col-form-label">Comentarios:</label>
                                <div class="col-sm-12">
                                    <textarea rows="5" v-model="comments" style="width: 100%" disabled></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-icon-text" data-dismiss="modal">
                    <b>Cerrar</b>
                    <i class="bx bx-x-circle"></i>
                </button>
                <button type="button" class="btn btn-danger btn-icon-text" id="btn_reject" 
                        v-if="check_status == 1" v-on:click="rejectDps()">
                    <b>Rechazar</b>
                    <i class="bx bxs-dislike"></i>
                </button>
                <button type="button" class="btn btn-success btn-icon-text" id="btn_approve" 
                        v-if="check_status == 1" v-on:click="setVoboNotaCredito(true)">
                    <b>Aprobar</b>
                    <i class="bx bxs-like"></i>
                </button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade modal-background" id="modal_notaCredito_comments" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="modal_notaCredito_comments" aria-hidden="true">
    <div class="modal-dialog" style="max-width: 50rem">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modal_notaCredito_comments">@{{ modal_title }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form class="forms-sample">
                    <div class="row">
                        <div class="col-md-12">
                            <label class="col-sm-12 col-form-label">Comentarios frecuentes:</label>
                            <div class="col-sm-12">
                                <select class="select2-class form-control" name="freqComments" id="freqComments" style="width: 100% !important"></select>
                            </div>
                        </div>
                    </div>
                    <br>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="">
                                <label class="col-sm-12 col-form-label">Ingrese comentario:</label>
                                <div class="col-sm-12">
                                    <textarea rows="5" v-model="comments" style="width: 100%"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-icon-text" data-dismiss="modal">
                    <b>Cancelar</b>
                    <i class="bx bx-x-circle"></i>
                </button>
                <button type="button" class="btn btn-danger btn-icon-text" id="btn_reject" 
                        v-if="check_status == 1" v-on:click="setVoboNotaCredito(false)">
                    <b>Rechazar</b>
                    <i class="bx bxs-dislike"></i>
                </button>
            </div>
        </div>
    </div>
</div>