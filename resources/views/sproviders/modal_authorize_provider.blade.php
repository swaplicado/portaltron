<div class="modal fade" id="modal_authorize_provider" tabindex="-1" aria-labelledby="modal_authorize_provider" aria-hidden="true">
    <div class="modal-dialog" style="max-width: 50rem">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modal_authorize_provider">@{{ modal_title }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form class="forms-sample">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group sm-form-group row">
                                <label class="col-sm-3 my-col-sm-3 col-form-label ">Proveedor</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" id="proveedor" placeholder="Proveedor" v-model="provider_name" readonly>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group sm-form-group row">
                                <label class="col-sm-3 my-col-sm-3 col-form-label ">Nombre comercial</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" id="provider_short_name" placeholder="Nombre comercial" v-model="provider_short_name" readonly>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group sm-form-group row">
                                <label class="col-sm-3 my-col-sm-3 col-form-label ">RFC</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" id="rfc" placeholder="RFC" v-model="provider_rfc" readonly>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group sm-form-group row">
                                <label class="col-sm-3 my-col-sm-3 col-form-label ">Email</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" id="email" placeholder="Email" v-model="provider_email" readonly>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer" style="display: block">
                <form class="forms-sample">
                    <div class="row">
                        <div class="col-md-3">
                            <label class="col-form-label "></label>
                            <div>
                                <button type="button" class="btn btn-secondary btn-icon-text form-control" data-dismiss="modal">
                                    <b>Cancelar</b>
                                    <i class="bx bx-x-circle"></i>
                                </button>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <label class="col-form-label "></label>
                            <button type="button" class="btn btn-danger btn-icon-text form-control" id="btn_reject"
                            v-on:click="setStatusProvider(lConstants.PROVIDER_RECHAZADO)">
                                <b>Rechazar</b>
                                <i class="bx bxs-dislike"></i>
                            </button>
                        </div>
                        <div class="col-md-3">
                            <label class="col-form-label "></label>
                            <button type="button" class="btn btn-warning btn-icon-text form-control" id="btn_modif"
                            v-on:click="setStatusProvider(lConstants.PROVIDER_PENDIENTE_MODIFICAR)">
                                <b>Sol. modif.</b>
                                <i class="bx bxs-message-square-edit"></i>
                            </button>
                        </div>
                        <div class="col-md-3">
                            <label class="col-form-label "></label>
                            <button type="button" class="btn btn-success btn-icon-text form-control" id="btn_approve"
                            v-on:click="setStatusProvider(lConstants.PROVIDER_APROBADO)">
                                <b>Autorizar</b>
                                <i class="bx bxs-like"></i>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>