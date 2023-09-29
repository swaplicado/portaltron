var app = new Vue({
    el: '#purchaseOrders',
    data: {
        oData: oServerData,
        lPurchaseOrders: oServerData.lPurchaseOrders,
        lStatus: oServerData.lStatus,
        idDoc: null,
        idYear: oServerData.idYear,
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
    },
    mounted(){
        self = this;

        $('.select2-class').select2({})

        $('#status_filter').select2({
            data: self.oData.lStatus,
        }).on('select2:select', function(e) {
            
        });

        //obtener el elemento a observar
        const rowDelevery = this.$refs.rowDelevery;
        // Crea una nueva instancia de IntersectionObserver
        const observer = new IntersectionObserver(entries => {
            entries.forEach(entry => {
            if (entry.isIntersecting) {
                // El elemento está visible en pantalla
                self.rowDeliveryIsVisible = 1;
            } else {
                // El elemento no está visible en pantalla
                self.rowDeliveryIsVisible = 0;
            }
            });
        });
        // Observa el elemento
        observer.observe(rowDelevery);

        var elemDatePicker = document.getElementById("myDatePicker");
        elemDatePicker.addEventListener('changeDate', function (e, details) { 
            self.deliveryDate = this.value;
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

            $('#modal_purchase_order').modal('show');

            if(this.deliveryDate == null || this.deliveryDate == ''){
                SGui.showMessage('', 'Recuerda que debes ingresar una fecha de entrega obligatoriamente y un comentario opcional', 'info');
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

        /**
         * Metodo para abrir el calendario del datepicker al presionar el boton
         */
        openDeliveryDate(){
            inputElement = document.getElementById("myDatePicker");
            inputElement.focus();
        },

        drawTablePurchaseOrders(lPurchaseOrders){
            var arrOC = [];
            for (let oc of lPurchaseOrders) {
                arrOC.push(
                    [
                        oc.idYear,
                        oc.idDoc,
                        oc.date,
                        oc.excRate,
                        (oc.excRate == 1 ? oc.taxCharged : oc.taxChargedCur),
                        (oc.excRate == 1 ? oc.taxRetained : oc.taxRetainedCur),
                        (oc.excRate == 1 ? oc.stot : oc.stotCur),
                        1,
                        oc.bpb,
                        oc.numRef,
                        'Nuevo',
                        oc.dateStartCred,
                        oc.daysCred,
                        oc.fCurKey,
                        (oc.excRate == 1 ? oc.tot : oc.totCur),
                        ''
                    ]
                )
            }
            drawTable('table_purchase_orders', arrOC);
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

        saveDeliveryDate(){
            if(this.deliveryDate == null || this.deliveryDate == ''){
                SGui.showMessage('', 'Debe introducir una fecha de entrega', 'warning');
                return;
            }
            SGui.showWaitingUnlimit();
            let route = this.oData.updateRoute;
            axios.post(route, {
                'idYear': this.idYear,
                'idDoc': this.idDoc,
                'deliveryDate': this.deliveryDate,
                'comments': this.comments,
            })
            .then( result => {
                let data = result.data;
                if(data.success){
                    let index = searchIndex('table_purchase_orders', [indexesPurchaseOrdersTable.idYear, indexesPurchaseOrdersTable.idDoc], [this.idYear, this.idDoc]);
                    
                    let cellDeliveryDate = table['table_purchase_orders'].cell(index, indexesPurchaseOrdersTable.delivery_date);
                    let cellstatusId = table['table_purchase_orders'].cell(index, indexesPurchaseOrdersTable.id_status);
                    let cellstatus = table['table_purchase_orders'].cell(index, indexesPurchaseOrdersTable.status);
                    
                    this.deliveryDate = data.deliveryDate;
                    this.comments = data.comments;

                    cellstatusId.data(data.idStatus);
                    cellstatus.data(data.status);
                    cellDeliveryDate.data(self.deliveryDate);
                    
                    table['table_purchase_orders'].draw();
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
            const year = this.idYear;
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
        }
    }
});