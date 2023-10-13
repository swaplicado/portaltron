<div class="modal fade" id="modal_purchase_order" ref="modal" tabindex="-1" aria-labelledby="modal_estimate_request_form" aria-hidden="true">
    <div class="modal-dialog" style="max-width: 50rem">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modal_estimate_request_form"><b> @{{ modal_title }} </b></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group sm-form-group row">
                            <label class="col-sm-2 my-col-sm-3 col-form-label ">Folio:</label>
                            <div class="col-sm-4">
                                <input type="text" class="form-control" id="number" placeholder="Folio" v-model="number" readonly>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group sm-form-group row">
                            <label class="col-sm-2 my-col-sm-3 col-form-label ">Asunto:</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" id="subject" 
                                placeholder="Asunto" v-model="subject" readonly>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group sm-form-group row">
                            <label class="col-sm-2 my-col-sm-3 col-form-label ">Enviado a:</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" id="mailsTo" 
                                placeholder="Enviado a" v-model="mailsTo" readonly>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group sm-form-group row">
                            <label class="col-sm-2 my-col-sm-3 col-form-label ">Cuerpo:</label>
                            <div class="col-sm-10">
                                <textarea type="text" class="form-control" id="body" 
                                placeholder="Cuerpo" v-model="body" rows="5" readonly></textarea>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="display expandable-table dataTable no-footer" id="table_rows" width="100%" cellspacing="0">
                        <thead>
                            <th>idEty</th>
                            <th style="text-align: center">Articulo</th>
                            <th>idItem</th>
                            <th style="text-align: center">Cantidad</th>
                            <th>idUnit</th>
                            <th style="text-align: center">Unidad</th>
                        </thead>
                        <tbody>
                            
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal"><b>Cerrar</b></button>
            </div>
        </div>
    </div>
</div>