<div class="modal fade" id="modal_authorize_provider" tabindex="-1" aria-labelledby="modal_authorize_provider" aria-hidden="true">
    <div class="modal-dialog" style="max-width: 50rem">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modal_authorize_provider">
                    <div class="myTooltip">
                        <span class="bx bx-info-circle" style="color: aqua"></span>
                        <div class="bottom-right">
                            <div class="text-content">
                                <ul>
                                    <li>
                                        El botón "Sol. modif." Permanecerá inhabilitado hasta 
                                        que todos los tengan estatus autorizado o rechazado.
                                    </li>
                                    <li>
                                        El botón "Autorizar" solo se habilitará hasta que todos los documentos tengan estatus aprobado.
                                    </li>
                                </ul>
                            </div>
                            <i></i>
                        </div>
                    </div>
                    @{{ modal_title }}
                </h5>
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
                    <div class="row">
                        <div class="col-md-12">
                            <div class="table-responsive">
                                <table class="display expandable-table dataTable no-footer" id="table_provider_documents" width="100%" cellspacing="0">
                                    <thead>
                                        <th>Documento</th>
                                        <th></th>
                                        <th>Rechazar</th>
                                        <th>Aprobar</th>
                                        <th>Estatus</th>
                                    </thead>
                                    <tbody>
                                        <tr v-for="doc in lDocuments" v-bind:class="[ (doc.is_accept == true ? 'row-success' : (doc.is_reject == true ? 'row-danger' : '')) ]">
                                            <td>@{{doc.name}}</td>
                                            <td>
                                                <a :href="doc.url" target="_blank" class="btn btn-primary">
                                                    Ver
                                                </a>
                                            </td>
                                            <template v-if="showWaitinIcon == false">
                                                <td>
                                                    <button type="button" class="btn btn-outline-secondary btn-rounded btn-icon"
                                                    :id="'btn_approve_doc_'+doc.id_vobo" v-on:click="rejectDoc(doc.id_vobo)">
                                                    <i class="bx bx-dislike text-danger"></i>
                                                    </button>
                                                </td>
                                                <td>
                                                    <button type="button" class="btn btn-outline-secondary btn-rounded btn-icon"
                                                    :id="'btn_approve_doc_'+doc.id_vobo" v-on:click="approveDoc(doc.id_vobo)">
                                                        <i class="bx bx-like text-success"></i>
                                                    </button>
                                                </td>
                                            </template>
                                            <template v-else>
                                                <td colspan="2" style="text-align: center">
                                                    <div class="lds-dual-ring"></div>
                                                </td>
                                            </template>
                                            <td>@{{doc.status}}</td>
                                        </tr>
                                    </tbody>
                                </table>
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
                            v-on:click="commentsProvider()" :disabled="!enableModify">
                                <b>Sol. modif.</b>
                                <i class="bx bxs-message-square-edit"></i>
                            </button>
                        </div>
                        <div class="col-md-3">
                            <label class="col-form-label "></label>
                            <button type="button" class="btn btn-success btn-icon-text form-control" id="btn_approve"
                            v-on:click="setStatusProvider(lConstants.PROVIDER_APROBADO)" :disabled="!enableAuthorize">
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

<div class="modal fade" id="modal_comments_provider" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="modal_comments_provider" aria-hidden="true">
    <div class="modal-dialog" style="max-width: 50rem">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modal_comments_provider">@{{ modal_title }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form class="forms-sample">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="">
                                <label class="col-sm-12 col-form-label">Ingrese comentario</label>
                                <div class="col-sm-12">
                                    <textarea rows="3" v-model="comments" style="width: 100%"></textarea>
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
                                <button type="button" class="btn btn-secondary btn-icon-text form-control" v-on:click="cancelComments()">
                                    <b>Cancelar</b>
                                    <i class="bx bx-x-circle"></i>
                                </button>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <label class="col-form-label "></label>
                            <button type="button" class="btn btn-warning btn-icon-text form-control" id="btn_modif"
                                    v-on:click="setStatusProvider(lConstants.PROVIDER_PENDIENTE_MODIFICAR)">
                                <b>Sol. modif.</b>
                                <i class="bx bxs-message-square-edit"></i>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modal_noAuthorize_provider" tabindex="-1" aria-labelledby="modal_noAuthorize_provider" aria-hidden="true">
    <div class="modal-dialog" style="max-width: 50rem">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modal_noAuthorize_provider">@{{ modal_title }}</h5>
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
                    <div class="row">
                        <div class="col-md-12">
                            <div class="table-responsive">
                                <table class="display expandable-table dataTable no-footer" id="table_provider_documents" width="100%" cellspacing="0">
                                    <thead>
                                        <th>Documento</th>
                                        <th></th>
                                    </thead>
                                    <tbody>
                                        <tr v-for="doc in lDocuments">
                                            <td>@{{doc.name}}</td>
                                            <td>
                                                <a :href="doc.url" target="_blank" class="btn btn-primary">
                                                    Ver
                                                </a>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
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
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>