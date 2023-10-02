var app = new Vue({
    el: '#purchaseOrders',
    data: {
        oData: oServerData,
        lProviders: oServerData.lProviders,
        showProvider: false,
        lPurchaseOrders: [],
        lStatus: oServerData.lStatus,
        idDoc: null,
        year: oServerData.year,
        idYear: oServerData.year,
        lRows: null,
        modal_title: "",
        rowDeliveryIsVisible: 0,
        deliveryDate: null,
        comments: null,
        reference: null,
        status: null,
        idStatus: null,
        dateStartCred: null,
        daysCredit: null,
        idCurrency: null,
        currency: null,
        total: null,
        taxRetained: null,
        taxCharged: null,
        stot: null,
        providerId: 0,
    },
    mounted(){
        self = this;

        $('.select2-class').select2({})

        $('#provider_filter').select2({
            placeholder: 'Selecciona proveedor',
            data: self.oData.lProviders,
        }).on('select2:select', function(e) {
            self.providerId = e.params.data.id;
        });

        $('#provider_filter').val('').trigger('change');

        $('#status_filter').select2({
            data: self.oData.lStatus,
        }).on('select2:select', function(e) {
            
        });
    }, 
    methods: {
        /**
         * Metodo para abrir el modal de la orden de compra,
         * se requiere que sea asincrono para usar el metodo await
         * y esperar a que axios realice la consulta de los ety
         * @param {*} data 
         */
        async showModal(data){
            this.cleanData();

            this.idDoc = data[indexesPurchaseOrdersTable.idDoc];
            this.idYear = data[indexesPurchaseOrdersTable.idYear];
            this.purchase_order_id = data[indexesPurchaseOrdersTable.id_purchase_order];
            this.modal_title = "Orden de compra " + data[indexesPurchaseOrdersTable.folio];
            this.reference = data[indexesPurchaseOrdersTable.reference];
            this.status = data[indexesPurchaseOrdersTable.status];
            this.dateStartCred = data[indexesPurchaseOrdersTable.dateStartCred];
            this.daysCredit = data[indexesPurchaseOrdersTable.daysCred];
            this.idCurrency = data[indexesPurchaseOrdersTable.excRate];
            this.currency = data[indexesPurchaseOrdersTable.fCurKey];
            this.total = data[indexesPurchaseOrdersTable.total];
            this.taxRetained = data[indexesPurchaseOrdersTable.taxRetained];
            this.taxCharged = data[indexesPurchaseOrdersTable.taxCharged];
            this.stot = data[indexesPurchaseOrdersTable.stot];

            await this.getRows();

            $('#modal_purchase_order_manager').modal('show');

            if(this.deliveryDate == null || this.deliveryDate == ''){
                SGui.showMessage('', 'Sin fecha de entrega', 'info');
            }
        },

        /**
         * Obtiene las ety de la orden de compra que se abrio
         * @returns 
         */
        getRows(){
            SGui.showWaitingUnlimit();

            let route = this.oData.getRowsRoute;
            return new Promise((resolve, reject) => 
                axios.post(route, {
                    'idDoc': this.idDoc,
                    'idYear': this.idYear
                })
                .then( result => {
                    let data = result.data;
                    if(data.success){
                        this.lRows = data.lRows;
                        this.comments = data.providerComment;
                        this.deliveryDate = data.deliveryDate;
                        inputDeliveryDate = document.getElementById("myDatePicker");
                        inputDeliveryDate.value = this.deliveryDate;
                        this.drawTableRows(this.lRows);
                        Swal.close();
                        resolve('ok');
                    }else{
                        SGui.showMessage('', data.message, data.icon);
                        reject('error');
                    }
                })
                .catch( function(error){
                    console.log(error);
                    SGui.showError(error);
                    reject('error');
                })
            );
        },

        drawTableRows(lRows){
            let arrEty = []
            for(let ety of lRows){
                arrEty.push(
                    [
                        ety.idEty,
                        ety.conceptKey,
                        ety.ref,
                        ety.concept,
                        ety.unit,
                        (self.idCurrency == 1 ? ety.priceUnit : ety.priceUCur),
                        ety.qty,
                        (self.idCurrency == 1 ? ety.taxCharged : ety.taxChargedCur),
                        (self.idCurrency == 1 ? ety.taxRetained : ety.taxRetainedCur),
                        (self.idCurrency == 1 ? ety.sTot : ety.sTotCur),
                        (self.idCurrency == 1 ? ety.tot : ety.totCur),
                    ]
                )
            }

            drawTable('table_rows', arrEty);
        },

        cleanData(){
            this.idDoc = null;
            this.idYear = null;
            this.purchase_order_id = null;
            this.modal_title = null;
            this.reference = null;
            this.status = null;
            this.dateStartCred = null;
            this.daysCredit = null;
            this.idCurrency = null;
            this.currency = null;
            this.total = null;
            this.taxRetained = null;
            this.taxCharged = null;
            this.stot = null;
            this.deliveryDate = null;
            this.comments = null;
        },

        getPurcharseOrders(){
            SGui.showWaitingUnlimit();

            let route = this.oData.getPurchaseOrdersRoute;
            const year = this.year;
            axios.get(route  + '/' + year)
            .then( result => {
                let data = result.data;
                if(data.success){
                    this.lPurchaseOrders = data.lRows;
                    drawTablePurchaseOrders(this.lPurchaseOrders);
                    SGui.showOk();
                }else{
                    SGui.showMessage('', data.message, data.icon);
                }
            })
            .catch( function(error){
                console.log(error);
                SGui.showError(error);
            });
        },

        getPurchaseOrdersProvider(){
            if(this.providerId == null || this.providerId == ''){
                SGui.showMessage('', 'Debe seleccionar un proveedor', 'info');
                return;
            }

            SGui.showWaitingUnlimit();

            let route = this.oData.getPurchaseOrdersRoute;

            axios.post(route, {
                'providerId': this.providerId,
                'year': this.year,
            })
            .then( result => {
                let data = result.data;
                if(data.success){
                    this.lPurchaseOrders = data.lPurchaseOrders;
                    drawTablePurchaseOrders(this.lPurchaseOrders);
                    SGui.showOk();
                    this.showProvider = true;
                }else{
                    SGui.showError('', data.message, data.icon);
                }
            })
            .catch( function(error){
                console.log(error);
                SGui.showError(error);
            })
        }
    }
});