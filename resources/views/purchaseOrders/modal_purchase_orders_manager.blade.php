<div class="modal fade" id="modal_purchase_order_manager" ref="modal" tabindex="-1" aria-labelledby="modal_purchase_order_manager" aria-hidden="true">
    <div class="modal-dialog" style="max-width: 50rem">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modal_purchase_order_manager"><b> @{{ modal_title }} </b></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form class="forms-sample" action="#">
                    <div class="row" id="divOrigen" ref="rowDelevery">
                        <div class="col-md-6">
                            <div class="form-group sm-form-group row">
                                <label class="col-sm-3 my-col-sm-3 col-form-label ">Fecha entrega</label>
                                <div class="col-sm-9">
                                    <div class="input-group">
                                        <input type="text" id="myDatePicker" ref="datepicker" name="datepicker" class="form-control" disabled>
                                        <div class="input-group-append">
                                            <button class="btn btn-sm btn-inverse-dark" type="button">
                                                <i class="bx bxs-calendar bx-sm"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group sm-form-group row">
                                <label class="col-sm-3 my-col-sm-3 col-form-label ">comentarios</label>
                                <div class="col-sm-9">
                                    <textarea class="form-control" id="exampleFormControlTextarea1" rows="3" v-model="comments" disabled></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group sm-form-group row">
                                <label class="col-sm-3 my-col-sm-3 col-form-label ">Referencia</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" id="folio" placeholder="Folio" v-model="reference" readonly>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group sm-form-group row">
                                <label class="col-sm-3 my-col-sm-3 col-form-label ">Estatus</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" id="authorizationStatusName" 
                                    placeholder="Estatus autorización" v-model="status" readonly>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group sm-form-group row">
                                <label class="col-sm-3 my-col-sm-3 col-form-label ">Inicio credito</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" id="folio" 
                                    placeholder="Folio" v-model="dateStartCred" readonly>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group sm-form-group row">
                                <label class="col-sm-3 my-col-sm-3 col-form-label ">Días credito</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" id="authorizationStatusName" 
                                    placeholder="Estatus autorización" v-model="daysCredit" readonly>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group sm-form-group row">
                                <label class="col-sm-3 my-col-sm-3 col-form-label ">Impuestos retenidos</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" id="folio" 
                                    placeholder="Folio" v-model="taxRetained" readonly>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group sm-form-group row">
                                <label class="col-sm-3 my-col-sm-3 col-form-label ">Impuestos cargados</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" id="authorizationStatusName" 
                                    placeholder="Estatus autorización" v-model="taxCharged" readonly>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group sm-form-group row">
                                <label class="col-sm-3 my-col-sm-3 col-form-label ">Moneda</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" id="folio" 
                                    placeholder="Folio" v-model="currency" readonly>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group sm-form-group row">
                                <label class="col-sm-3 my-col-sm-3 col-form-label ">Subtotal</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" id="authorizationStatusName" 
                                    placeholder="Estatus autorización" v-model="stot" readonly>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group sm-form-group row">
                                <label class="col-sm-3 my-col-sm-3 col-form-label ">Total</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" id="folio" 
                                    placeholder="Folio" v-model="total" readonly>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
                <div class="table-responsive">
                    <table class="display expandable-table dataTable no-footer" id="table_rows" width="100%" cellspacing="0">
                        <thead>
                            <th>idEty</th>
                            <th>conceptKey</th>
                            <th>ref</th>
                            <th>Concepto</th>
                            <th>Unidad</th>
                            <th>Precio unitario</th>
                            <th>Cantidad</th>
                            <th>Impuesto cargado</th>
                            <th>Impuesto retenido</th>
                            <th>Subtotal</th>
                            <th>Total</th>
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