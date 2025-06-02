<div class="modal fade" id="modal_new_area_provider" tabindex="-1" aria-labelledby="modal_new_area_provider" aria-hidden="true">
    <div class="modal-dialog" style="max-width: 30rem">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modal_new_area_provider">Asignar nueva área de proveedor</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form class="forms-sample">
                    <div class="col-md-12">
                        <div class="form-group sm-form-group row">
                            <label class="col-sm-3 my-col-sm-3 col-form-label ">Área actual</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" id="area" placeholder="Área" v-model="provider_area" readonly>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group sm-form-group row">
                            <label class="col-sm-3 my-col-sm-3 col-form-label ">Nueva área destino</label>
                            <div class="col-sm-9">
                                <select class="select2-class form-control" style="width: 100%" name="select_area" id="select_area"></select>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-success" v-on:click="updateAreaProvider()">Guardar</button>
            </div>
        </div>
    </div>
</div>