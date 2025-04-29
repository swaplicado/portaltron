<div class="modal fade" id="modal_allProvidersDoduments" tabindex="-1" aria-labelledby="modal_allProvidersDoduments" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable" style="max-width: 50rem">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title" id="modal_allProvidersDoduments">
                    Descarga de documentos de proveedores
                </h3>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div v-for="doc in lTypesDocs">
                    <div style="display: flex;">
                        <div class="col-sm-1">
                            <div class="checkbox-wrapper-33">
                                <label class="checkbox">
                                    <input class="checkbox__trigger visuallyhidden checkbox_typeFile" type="checkbox"
                                        v-on:click="setDocumentsToDownload(doc.id_request_type_doc)" >
                                    <span class="checkbox__symbol">
                                        <svg aria-hidden="true" class="icon-checkbox" width="28px" height="28px" viewBox="0 0 28 28" version="1" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M4 14l8 7L24 7"></path>
                                        </svg>
                                    </span>
                                </label>
                            </div>
                        </div>
                        @{{doc.name}}
                    </div>
                </div>

                <div style="border: 1px solid gray"></div>
                <br>
                <div style="display: flex">
                    <div class="col-sm-1">
                        <div class="checkbox-wrapper-33">
                            <label class="checkbox">
                                <input class="checkbox__trigger visuallyhidden" type="checkbox" v-model="checkAllProviders">
                                <span class="checkbox__symbol">
                                    <svg aria-hidden="true" class="icon-checkbox" width="28px" height="28px" viewBox="0 0 28 28" version="1" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M4 14l8 7L24 7"></path>
                                    </svg>
                                </span>
                            </label>
                        </div>
                    </div>
                    Seleccionar todos los proveedores
                </div>
                <table class="display expandable-table dataTable no-footer" id="table_allProviders" width="100%" cellspacing="0">
                    <thead>
                        <th>id_provider</th>
                        <th>Proveedor</th>
                        <th></th>
                    </thead>
                    <tbody>

                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <div class="col-md-3">
                    <button type="button" class="btn btn-success btn-icon-text form-control" id="btn_approve"
                        v-on:click="downloadDocuments()">
                        <b>Descargar</b>
                        <i class="bx bxs-like"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>