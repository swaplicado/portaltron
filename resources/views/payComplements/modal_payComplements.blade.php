<div class="modal fade" id="modal_pay_complements" ref="modal" tabindex="-1" aria-labelledby="modal_pay_complements" aria-hidden="true">
    <div class="modal-dialog" style="max-width: 50rem">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modal_pay_complements"><b> @{{ modal_title }} </b></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form class="forms-sample" action="#">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group sm-form-group row">
                                <label class="col-sm-3 my-col-sm-3 col-form-label ">Area</label>
                                <div class="col-sm-9">
                                    <select class="select2-class form-control" style="width: 100%"
                                    name="select_area" id="select_area"></select>
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
                <button type="button" class="btn btn-success" v-on:click="savePayComplement()"><b>Guardar</b></button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modal_get_pay_complement" ref="modal" tabindex="-1" aria-labelledby="modal_get_pay_complement" aria-hidden="true">
    <div class="modal-dialog" style="max-width: 50rem">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modal_get_pay_complement"><b> @{{ modal_title }} </b></h5>
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
                                        <th>Area</th>
                                        <th>Documento</th>
                                        <th></th>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>@{{name_area}}</td>
                                            <td>PDF</td>
                                            <td><a :href="pdf_url" target="_blank" class="btn btn-primary">Ver</a></td>
                                        </tr>
                                        <tr>
                                            <td>@{{name_area}}</td>
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
                <button type="button" class="btn btn-secondary" data-dismiss="modal"><b>Cerrar</b></button>
                {{-- <button type="button" class="btn btn-success" v-on:click="updateComplementary()"><b>Actualizar referencia</b></button> --}}
            </div>
        </div>
    </div>
</div>