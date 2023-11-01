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
                    @if($showAreaDps)
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group sm-form-group row">
                                    @if($requireAreaDps)
                                        <label class="col-sm-3 my-col-sm-3 col-form-label ">Área destino*:</label>
                                        <div class="col-sm-9">    
                                            <select class="select2-class form-control" style="width: 100%"
                                            name="select_area" id="select_area" required></select>
                                        </div>
                                    @else
                                        <label class="col-sm-3 my-col-sm-3 col-form-label ">Área destino:</label>
                                        <div class="col-sm-9">    
                                            <select class="select2-class form-control" style="width: 100%"
                                            name="select_area" id="select_area"></select>
                                        </div>
                                    @endif
                                    
                                </div>
                            </div>
                        </div>
                    @endif
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group sm-form-group row">
                                <label class="col-sm-3 my-col-sm-3 col-form-label ">Referencia:*
                                    <div class="myTooltip">
                                        <span class="bx bx-info-circle" style="color: rgb(39, 63, 243)"></span>
                                        <div class="bottom-right">
                                            <div class="text-content">
                                                <ul>
                                                    <li>
                                                        Si se ingresa más de un folio, deben separarse por comas.
                                                    </li>
                                                    <li>
                                                        Si se tiene serie debe ir separada del folio con un guión.
                                                    </li>
                                                </ul>
                                            </div>
                                            <i></i>
                                        </div>
                                    </div>
                                </label>
                                
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" id="serieoc" placeholder="oc" v-model="serieoc">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group sm-form-group row">
                                <label class="col-sm-3 my-col-sm-3 col-form-label ">Serie y folio factura:
                                    <div class="myTooltip">
                                        <span class="bx bx-info-circle" style="color: rgb(39, 63, 243)"></span>
                                        <div class="bottom-right">
                                            <div class="text-content">
                                                <ul>
                                                    <li>
                                                        Si se tiene serie debe ir separada del folio con un guión.
                                                    </li>
                                                </ul>
                                            </div>
                                            <i></i>
                                        </div>
                                    </div>
                                </label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" id="folio" placeholder="Folio" v-model="folio">
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
                                        <th style="text-align: center">Referencias</th>
                                        <th style="text-align: center">Documento</th>
                                        <th></th>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td style="text-align: center">@{{reference}}</td>
                                            <td style="text-align: center">PDF</td>
                                            <td style="text-align: center"><a :href="pdf_url" target="_blank" class="btn btn-primary">Ver</a></td>
                                        </tr>
                                        <tr>
                                            <td style="text-align: center">@{{reference}}</td>
                                            <td style="text-align: center">XML</td>
                                            <td style="text-align: center"><a :href="xml_url" target="_blank" class="btn btn-primary">Ver</a></td>
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
                {{-- <button type="button" class="btn btn-success" v-on:click="updateComplementary()"><b>Actualizar referencia</b></button> --}}
            </div>
        </div>
    </div>
</div>