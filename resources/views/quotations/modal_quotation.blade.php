<div class="modal fade" id="modal_quotations" tabindex="-1" aria-labelledby="modal_quotations" aria-hidden="true">
    <div class="modal-dialog modal-xl" style="max-width: 50rem">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modal_quotations" v-html="modal_title"></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form class="forms-sample">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label">Descripción</label>
                                <div class="col-sm-9">
                                    <textarea v-model="description" class="form-control"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label">Folio</label>
                                <div class="col-sm-9">
                                    <input v-model="folioUser" type="text" class="form-control" placeholder="Folio">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row" v-if="is_edit">
                        <div class="col-md-12">
                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label">Archivo actual</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" placeholder="Folio" v-model="pdf_original_name" disabled>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <label for="pdf" class="drop-container" id="dropcontainer">
                                <span class="drop-title">Arrastre el archivo aquí (máximo 20MB)</span>
                                    o
                                <input type="file" ref='pdf' id="pdf" accept=".pdf" required style="width: 90%">
                            </label>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button class="btn btn-light" data-dismiss="modal">Cancelar</button>
                <button class="btn btn-primary mr-2" v-on:click="save();">Guardar</button>
            </div>
        </div>
    </div>
</div>