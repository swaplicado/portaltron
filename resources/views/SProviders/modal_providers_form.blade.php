<div class="modal fade" id="modal_providers_form" tabindex="-1" aria-labelledby="modal_providers_form" aria-hidden="true">
    <div class="modal-dialog" style="max-width: 50rem">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modal_providers_form">@{{ modal_title }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form class="forms-sample">
                    <template v-if="!!oUser">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group row">
                                    <label class="col-sm-3 col-form-label">Usuario</label>
                                    <div class="col-sm-9">
                                        <input v-model="oUser.username" type="text" class="form-control"
                                         id="inlineFormInputName2" placeholder="Usuario" disabled>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group row">
                                    <label class="col-sm-3 col-form-label">Correo</label>
                                    <div class="col-sm-9">
                                        <input v-model="oUser.email" type="email" class="form-control"
                                         id="inlineFormInputName2" placeholder="Email" disabled>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group row">
                                    <label class="col-sm-3 col-form-label">Nombre</label>
                                    <div class="col-sm-9">
                                        <input v-model="oUser.full_name" type="text" class="form-control" 
                                            id="inlineFormInputName2" placeholder="Nombre" style="text-transform: uppercase" disabled>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </template>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label">Nombre proveedor</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" id="provider_name" placeholder="Nombre" v-model="provider_name">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label">Nombre comercial</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" id="provider_short_name" placeholder="Nombre comercial" v-model="provider_short_name">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label">RFC</label>
                                <div class="col-sm-9">
                                    <input type="text" minlength="12" maxlength="13"
                                        class="form-control" id="provider_rfc" placeholder="RFC" v-model="provider_rfc">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label">Correo</label>
                                <div class="col-sm-9">
                                    <input type="email" class="form-control" id="provider_email" placeholder="Email" v-model="provider_email">
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" v-on:click="saveProvider()">Guardar</button>
            </div>
        </div>
    </div>
</div>