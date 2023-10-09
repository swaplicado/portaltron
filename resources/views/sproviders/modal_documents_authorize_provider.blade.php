<div class="modal fade" id="modal_documents_authorize_provider" tabindex="-1" aria-labelledby="modal_documents_authorize_provider" aria-hidden="true">
    <div class="modal-dialog" style="max-width: 50rem">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modal_documents_authorize_provider">
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
                                            <template v-if="!doc.is_accept && !doc.is_reject">
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
                                            </template>
                                            <template v-else>
                                                <td colspan="2" style="text-align: center">
                                                    
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
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modal_documents_noAuthorize_provider" tabindex="-1" aria-labelledby="modal_documents_noAuthorize_provider" aria-hidden="true">
    <div class="modal-dialog" style="max-width: 50rem">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modal_documents_noAuthorize_provider">@{{ modal_title }}</h5>
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
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>