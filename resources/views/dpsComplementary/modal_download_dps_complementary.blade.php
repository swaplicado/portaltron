<div class="modal fade" id="modal_download_dps_complementary" ref="modal" tabindex="-1" aria-labelledby="modal_download_dps_complementary" aria-hidden="true">
    <div class="modal-dialog" style="max-width: 50rem">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modal_download_dps_complementary"><b>Descarga de facturas</b></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form class="forms-sample" action="#">
                    <div class="row">
                        <div class="col-md-12">
                            <div style="display: flex">
                                <div class="col-sm-4">
                                    <label>Fecha inicio</label>
                                    <input type="text" id="startDatePicker" class="form-control">
                                </div>
                                <div class="col-sm-4">
                                    <label>Fecha fin</label>
                                    <input type="text" id="endDatePicker" class="form-control">
                                </div>
                                <div class="col-sm-4">
                                    <label>Estatus</label>
                                    <br>
                                    <select style="width: 100%" class="select2-class" name="status_filter_download" id="status_filter_download"></select>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <br>
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
                </form>
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