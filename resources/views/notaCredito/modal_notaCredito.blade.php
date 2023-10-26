<div class="modal fade" id="modal_up_notaCredito" ref="modal" tabindex="-1" aria-labelledby="modal_up_notaCredito" aria-hidden="true">
    <div class="modal-dialog" style="max-width: 50rem">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modal_up_notaCredito"><b> Notas de crédito </b></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form class="forms-sample" action="#">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group sm-form-group row">
                                <label class="col-sm-3 my-col-sm-3 col-form-label ">Serie y folio:*</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" id="serieFolio" placeholder="serie y folio" v-model="serieFolio">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group sm-form-group row">
                                <label class="col-sm-3 my-col-sm-3 col-form-label ">Referencia:*</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" id="reference" placeholder="reference" v-model="reference">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group sm-form-group row">
                                <label class="col-sm-3 my-col-sm-3 col-form-label ">
                                    PDF
                                </label>
                                <div class="col-sm-9">
                                    <input type="file" id="pdf" name="pdf"
                                        class="file-upload-default" accept=".pdf">
                                    <div class="input-group col-xs-12">
                                        <input type="text" id="pdfName"
                                            class="form-control file-upload-info" disabled
                                            placeholder="Cargar archivo">
                                        <span class="input-group-append">
                                            <button
                                                class="file-upload-browse btn btn-info"
                                                type="button">Cargar</button>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group sm-form-group row">
                                <label class="col-sm-3 my-col-sm-3 col-form-label ">
                                    XML
                                </label>
                                <div class="col-sm-9">
                                    <input type="file" id="xml" name="xml"
                                        class="file-upload-default" accept=".xml">
                                    <div class="input-group col-xs-12">
                                        <input type="text" id="xmlName"
                                            class="form-control file-upload-info" disabled
                                            placeholder="Cargar archivo">
                                        <span class="input-group-append">
                                            <button
                                                class="file-upload-browse btn btn-info"
                                                type="button">Cargar</button>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal"><b>Cerrar</b></button>
                <button type="button" class="btn btn-success" v-on:click="saveNotaCredito()"><b>Guardar</b></button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modal_get_notaCredito" ref="modal" tabindex="-1" aria-labelledby="modal_get_notaCredito" aria-hidden="true">
    <div class="modal-dialog" style="max-width: 50rem">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modal_get_notaCredito"><b> @{{ modal_title }} </b></h5>
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
                    <div class="row" v-if="comments != null">
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
                <button type="button" class="btn btn-secondary" data-dismiss="modal"><b>Cerrar</b></button>
            </div>
        </div>
    </div>
</div>