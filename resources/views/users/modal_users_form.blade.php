<div class="modal fade" id="modal_users_form" tabindex="-1" aria-labelledby="modal_users_form" aria-hidden="true">
    <div class="modal-dialog" style="max-width: 50rem">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modal_users_form">@{{ modal_title }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form class="forms-sample" href="#">
                    <div class="form-group">
                        <label for="last_name1">Apellido paterno</label>
                        <input type="text" class="form-control form-control-sm" id="last_name1" placeholder="Nombre" v-model="last_name1">
                    </div>
                    <div class="form-group">
                        <label for="last_name2">Apellido materno</label>
                        <input type="email" class="form-control" id="last_name2" placeholder="Nombre comercial" v-model="last_name2">
                    </div>
                    <div class="form-group">
                        <label for="names">Nombre(s)</label>
                        <input type="text" class="form-control" id="names" placeholder="RFC" v-model="names">
                    </div>
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" class="form-control" id="email" placeholder="Email" v-model="email">
                    </div>
                    <div class="form-group">
                        <label for="roles">Rol</label>
                        <select class="select2-class-modal form-control" name="roles" id="roles" style="width: 100%;"></select>
                    </div>
                    <div class="form-group" v-if="rol_id == constants['ROL_PROVEEDOR']">
                        <label for="select_providers">Proveedor</label>
                        <select class="select2-class-modal form-control" name="select_providers" id="select_providers" ref="select_providers" style="width: 100%;"></select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" v-on:click="saveUser()">Guardar</button>
            </div>
        </div>
    </div>
</div>