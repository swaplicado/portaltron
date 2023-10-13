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

        /**
         * Mueve el renglon de fecha de entrega a arriba o abajo dependiendo si es visible en pantalla o no
         */
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
            // this.purchase_order_id = data[indexesPurchaseOrdersTable.id_purchase_order];
            this.modal_title = "Orden de compra " + data[indexesPurchaseOrdersTable.reference];
            this.reference = data[indexesPurchaseOrdersTable.reference];
            this.status = data[indexesPurchaseOrdersTable.status];
            this.dateStartCred = data[indexesPurchaseOrdersTable.dateStartCred];
            this.daysCredit = data[indexesPurchaseOrdersTable.daysCred];
            this.idCurrency = data[indexesPurchaseOrdersTable.excRate];
            this.currency = data[indexesPurchaseOrdersTable.fCurKey];
            this.total = data[indexesPurchaseOrdersTable.total].toLocaleString('es-MX', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
            this.taxRetained = data[indexesPurchaseOrdersTable.taxRetained].toLocaleString('es-MX', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
            this.taxCharged = data[indexesPurchaseOrdersTable.taxCharged].toLocaleString('es-MX', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
            this.stot = data[indexesPurchaseOrdersTable.stot].toLocaleString('es-MX', { minimumFractionDigits: 2, maximumFractionDigits: 2 });

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
                        (self.idCurrency == 1 ? ety.priceUnit.toLocaleString('es-MX', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) : 
                                                ety.priceUCur.toLocaleString('es-MX', { minimumFractionDigits: 2, maximumFractionDigits: 2 })),
                        ety.qty,
                        (self.idCurrency == 1 ? ety.taxCharged.toLocaleString('es-MX', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) : 
                            ety.taxChargedCur.toLocaleString('es-MX', { minimumFractionDigits: 2, maximumFractionDigits: 2 })),
                        (self.idCurrency == 1 ? ety.taxRetained.toLocaleString('es-MX', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) : 
                            ety.taxRetainedCur.toLocaleString('es-MX', { minimumFractionDigits: 2, maximumFractionDigits: 2 })),
                        (self.idCurrency == 1 ? ety.sTot.toLocaleString('es-MX', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) : 
                            ety.sTotCur.toLocaleString('es-MX', { minimumFractionDigits: 2, maximumFractionDigits: 2 })),
                        (self.idCurrency == 1 ? ety.tot.toLocaleString('es-MX', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) : 
                            ety.totCur.toLocaleString('es-MX', { minimumFractionDigits: 2, maximumFractionDigits: 2 })),
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

                    var rowIndexes = table['table_purchase_orders'].rows().indexes();

                    let cellDeliveryDate = table['table_purchase_orders'].cell(rowIndexes[index], indexesPurchaseOrdersTable.delivery_date);
                    let cellstatusId = table['table_purchase_orders'].cell(rowIndexes[index], indexesPurchaseOrdersTable.id_status);
                    let cellstatus = table['table_purchase_orders'].cell(rowIndexes[index], indexesPurchaseOrdersTable.status);
                    
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
            // this.idYear = null;
            // this.purchase_order_id = null;
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