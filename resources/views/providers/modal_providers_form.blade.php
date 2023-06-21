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
                <form class="forms-sample" href="#">
                    <div class="form-group">
                        <label for="provider_name">Nombre proveedor</label>
                        <input type="text" class="form-control" id="provider_name" placeholder="Nombre" v-model="provider_name">
                    </div>
                    <div class="form-group">
                        <label for="provider_short_name">Nombre comercial</label>
                        <input type="email" class="form-control" id="provider_short_name" placeholder="Nombre comercial" v-model="provider_short_name">
                    </div>
                    <div class="form-group">
                        <label for="provider_rfc">RFC</label>
                        <input type="text" class="form-control" id="provider_rfc" placeholder="RFC" v-model="provider_rfc">
                    </div>
                    <div class="form-group">
                        <label for="provider_email">Email</label>
                        <input type="email" class="form-control" id="provider_email" placeholder="Email" v-model="provider_email">
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