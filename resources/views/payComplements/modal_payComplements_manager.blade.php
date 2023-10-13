<div class="modal fade" id="modal_pay_complement" ref="modal" tabindex="-1" aria-labelledby="modal_pay_complement" aria-hidden="true">
    <div class="modal-dialog" style="max-width: 50rem">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modal_pay_complement"><b> CFDI de pago </b></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form class="forms-sample" action="#">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="table-responsive">
                                <table class="display expandable-table dataTable no-footer" id="table_provider_documents" width="100%" cellspacing="0">
                                    <thead>
                                        <th>Área destino</th>
                                        <th>Documento</th>
                                        <th></th>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>@{{name_area}}</td>
                                            <td>PDF</td>
                                            <td><a :href="pdf_url" target="_blank" class="btn btn-primary">Ver</a></td>
                                        </tr>
                                        <tr>
                                            <td>@{{name_area}}</td>
                                            <td>XML</td>
                                            <td><a :href="xml_url" target="_blank" class="btn btn-primary">Ver</a></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-icon-text" data-dismiss="modal">
                    <b>Cerrar</b>
                    <i class="bx bx-x-circle"></i>
                </button>
                <button type="button" class="btn btn-danger btn-icon-text" id="btn_reject" 
                        v-if="check_status == 1" v-on:click="setVoboPayComplement(false)">
                    <b>Rechazar</b>
                    <i class="bx bxs-dislike"></i>
                </button>
                <button type="button" class="btn btn-success btn-icon-text" id="btn_approve" 
                        v-if="check_status == 1" v-on:click="setVoboPayComplement(true)">
                    <b>Autorizar</b>
                    <i class="bx bxs-like"></i>
                </button>
            </div>
        </div>
    </div>
</div>