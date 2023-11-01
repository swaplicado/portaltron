<div class="modal fade" id="modal_dps_complementary" ref="modal" tabindex="-1" aria-labelledby="modal_dps_complementary" aria-hidden="true">
    <div class="modal-dialog" style="max-width: 50rem">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modal_dps_complementary"><b> Facturas </b></h5>
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
                                        <th>Referencias</th>
                                        <th>Documento</th>
                                        <th></th>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>@{{reference}}</td>
                                            <td>PDF</td>
                                            <td><a :href="pdf_url" target="_blank" class="btn btn-primary">Ver</a></td>
                                        </tr>
                                        <tr>
                                            <td>@{{reference}}</td>
                                            <td>XML</td>
                                            <td><a :href="xml_url" target="_blank" class="btn btn-primary">Ver</a></td>
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
                        v-if="check_status == 1" v-on:click="setVoboComplement(true)">
                    <b>Aprobar</b>
                    <i class="bx bxs-like"></i>
                </button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade modal-background" id="modal_dps_complementary_comments" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="modal_dps_complementary_comments" aria-hidden="true">
    <div class="modal-dialog" style="max-width: 50rem">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modal_dps_complementary_comments">@{{ modal_title }}</h5>
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
                        v-if="check_status == 1" v-on:click="setVoboComplement(false)">
                    <b>Rechazar</b>
                    <i class="bx bxs-dislike"></i>
                </button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modal_change_dps_complementary" ref="modal" tabindex="-1" aria-labelledby="modal_dps_complementary" aria-hidden="true">
    <div class="modal-dialog" style="max-width: 50rem">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modal_dps_complementary"><b> Facturas </b></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form class="forms-sample" action="#">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group sm-form-group row">
                                <label class="col-sm-3 my-col-sm-3 col-form-label ">Área destino:</label>
                                <div class="col-sm-9">
                                    <select class="select2-class form-control" style="width: 100%"
                                    name="select_area" id="select_area"></select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="table-responsive">
                                <table class="display expandable-table dataTable no-footer" id="table_provider_documents" width="100%" cellspacing="0">
                                    <thead>
                                        <th>Referencias</th>
                                        <th>Documento</th>
                                        <th></th>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>@{{reference}}</td>
                                            <td>PDF</td>
                                            <td><a :href="pdf_url" target="_blank" class="btn btn-primary">Ver</a></td>
                                        </tr>
                                        <tr>
                                            <td>@{{reference}}</td>
                                            <td>XML</td>
                                            <td><a :href="xml_url" target="_blank" class="btn btn-primary">Ver</a></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-info" v-on:click="sendChange()">
                    Reenviar
                </button>
            </div>
        </div>
    </div>
</div>