<div class="modal fade" id="modal_dps_complementary" ref="modal" tabindex="-1" aria-labelledby="modal_dps_complementary" aria-hidden="true">
    <div class="modal-dialog" style="max-width: 50rem">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modal_dps_complementary"><b> Facturas y notas de crédito </b></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form class="forms-sample" action="#">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group sm-form-group row">
                                <label class="col-sm-3 my-col-sm-3 col-form-label ">Área destino</label>
                                <div class="col-sm-9">
                                    <select class="select2-class form-control" style="width: 100%"
                                    name="select_area" id="select_area"></select>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group sm-form-group row">
                                <label class="col-sm-3 my-col-sm-3 col-form-label ">Referencia</label>
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
                <button type="button" class="btn btn-success" v-on:click="saveComplementary()"><b>Guardar</b></button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modal_get_dps_complementary" ref="modal" tabindex="-1" aria-labelledby="modal_get_dps_complementary" aria-hidden="true">
    <div class="modal-dialog" style="max-width: 50rem">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modal_get_dps_complementary"><b> @{{ modal_title }} </b></h5>
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
                                        <th style="text-align: center">Orden compra</th>
                                        <th style="text-align: center">Área destino</th>
                                        <th style="text-align: center">Documento</th>
                                        <th></th>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td style="text-align: center">@{{reference}}</td>
                                            <td style="text-align: center">@{{name_area}}</td>
                                            <td style="text-align: center">PDF</td>
                                            <td style="text-align: center"><a :href="pdf_url" target="_blank" class="btn btn-primary">Ver</a></td>
                                        </tr>
                                        <tr>
                                            <td style="text-align: center">@{{reference}}</td>
                                            <td style="text-align: center">@{{name_area}}</td>
                                            <td style="text-align: center">XML</td>
                                            <td style="text-align: center"><a :href="xml_url" target="_blank" class="btn btn-primary">Ver</a></td>
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