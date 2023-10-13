var app = new Vue({
    el: '#estimateRequest',
    data: {
        oData: oServerData,
        lEstimateRequest: oServerData.lEstimateRequest,
        lProviders: oServerData.lProviders,
        showProvider: false,
        lStatus: oServerData.lStatus,
        idInternal: null,
        idEstimateRequest: null,
        Year: oServerData.Year,
        lRows: null,
        modal_title: "",
        number: null,
        comments: null,
        reference: null,
        status: null,
        opened: null,
        subject: null,
        mailsTo: null,
        body: null,
        idEty: null,
        dateOpen: null,
    },
    mounted() {
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
        async showModal(data) {
            this.cleanData();

            this.idEstimateRequest = data[indexesEstimateRequestTable.idEstimateRequest],
                this.idInternal = data[indexesEstimateRequestTable.idInternal]
            this.number = data[indexesEstimateRequestTable.number]
            this.subject = data[indexesEstimateRequestTable.subject],
                this.modal_title = 'Solicitud cotización #' + data[indexesEstimateRequestTable.number],
                this.mailsTo = data[indexesEstimateRequestTable.mailsTo],
                this.body = data[indexesEstimateRequestTable.body],
                this.opened = data[indexesEstimateRequestTable.opened],
                this.dateOpen = data[indexesEstimateRequestTable.dateOpen]

            await this.getRows();

            $('#modal_purchase_order_manager').modal('show');
        },

        /**
         * Obtiene las ety de la orden de compra que se abrio
         * @returns 
         */
        getRows() {
            SGui.showWaitingUnlimit();

            let route = this.oData.getRowsRoute;
            return new Promise((resolve, reject) =>
                axios.post(route, {
                    'idInternal': this.idInternal,
                    'idExt': this.idEstimateRequest,
                    'isCompany': 1
                })
                .then(result => {
                    let data = result.data;
                    if (data.success) {
                        this.lRows = data.lRows;
                        this.drawTableRows(this.lRows);
                        Swal.close();
                        resolve('ok');
                    } else {
                        SGui.showMessage('', data.message, data.icon);
                        reject('error');
                    }
                })
                .catch(function(error) {
                    console.log(error);
                    SGui.showError(error);
                    reject('error');
                })
            );
        },

        /**
         * Metodo para abrir el calendario del datepicker al presionar el boton
         */
        openDeliveryDate() {
            inputElement = document.getElementById("myDatePicker");
            inputElement.focus();
        },

        drawTableEstimateRequests(lEstimateRequest) {
            var arrER = [];
            for (let er of lEstimateRequest) {
                arrER.push(
                    [
                        er.idYear,
                        er.idInternal,
                        er.idEstimateRequest,
                        er.number,
                        er.dateFormat,
                        er.mailsTo,
                        er.subject,
                        er.body,
                        er.is_opened,
                        (er.is_opened == 1 ? 'Sí' : 'No'),
                        er.updatedAt
                    ]
                )
            }
            drawTable('table_estimate_request', arrER);
        },

        drawTableRows(lRows) {
            let arrEty = []
            for (let ety of lRows) {
                arrEty.push(
                    [
                        ety.idEty,
                        ety.nameItem,
                        ety.idItem,
                        ety.qty,
                        ety.idUnit,
                        ety.symbol
                    ]
                )
            }

            drawTable('table_rows', arrEty);
        },

        cleanData() {
            this.idEstimateRequest = null,
                this.idInternal = null,
                this.modal_title = null,
                this.mailsTo = null,
                this.body = null,
                this.opened = null
        },

        getEstimateRequest() {
            SGui.showWaitingUnlimit();

            let route = this.oData.getEstimateRequestRoute;
            const year = this.Year;
            axios.get(route + '/' + year)
                .then(result => {
                    let data = result.data;
                    if (data.success) {
                        this.lEstimateRequest = data.lRows;
                        this.drawTableEstimateRequests(this.lEstimateRequest);
                        SGui.showOk();
                    } else {
                        SGui.showMessage('', data.message, data.icon);
                    }
                })
                .catch(function(error) {
                    console.log(error);
                    SGui.showError(error);
                });
        },

        getEstimateRequestProvider() {
            if (this.providerId == null || this.providerId == '') {
                SGui.showMessage('', 'Debe seleccionar un proveedor', 'info');
                return;
            }

            SGui.showWaitingUnlimit();

            let route = this.oData.getEstimateRequestRoute;

            axios.post(route, {
                    'providerId': this.providerId,
                    'year': this.Year,
                })
                .then(result => {
                    let data = result.data;
                    if (data.success) {
                        this.lEstimateRequest = data.lEstimateRequest;
                        this.arrDocs = Object.keys(data.lEstimateRequest).map((clave) => data.lEstimateRequest[clave]);
                        this.drawTableEstimateRequests(this.lEstimateRequest);
                        SGui.showOk();
                        this.showProvider = true;
                    } else {
                        SGui.showError('', data.message, data.icon);
                    }
                })
                .catch(function(error) {
                    console.log(error);
                    SGui.showError(error);
                })
        }
    }
});