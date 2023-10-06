var self;
var app = new Vue({
    el: '#sproviders',
    data: {
        oData: oServerData,
        lProviders: oServerData.lProviders,
        modal_title: null,
        provider_name: null,
        provider_short_name: null,
        provider_rfc: null,
        provider_email: null,
        id_provider: null,
        user_id: null,
        comments: null,
    },
    mounted() {
        self = this;

        $('.select2-class').select2({})

    },
    methods: {
        async showModal(data) {
            this.clean();
            this.id_provider = data[indexesProvidersTable.id_provider];
            this.modal_title = 'Autorización de proveedor: ' + data[indexesProvidersTable.provider_name]
            await this.getProviderData();

            $('#modal_authorize_provider').modal('show');
        },

        getProviderData() {
            SGui.showWaitingUnlimit();
            let route = oServerData.getProviderRoute;

            return new Promise((resolve, reject) =>
                axios.post(route, {
                    'provider_id': this.id_provider,
                })
                .then(result => {
                    let data = result.data;

                    if (data.success) {
                        this.provider_name = data.oProvider.provider_name;
                        this.provider_short_name = data.oProvider.provider_short_name;
                        this.provider_rfc = data.oProvider.provider_rfc;
                        this.provider_email = data.oProvider.provider_email;
                        this.user_id = data.oProvider.user_id;
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
         * Metodo para aprobar, rechazar o solicitar modificacion a un proveedor
         */
        setStatusProvider(status) {
            SGui.showWaitingUnlimit();
            let route = "";
            switch (status) {
                case this.lConstants.PROVIDER_APROBADO:
                    route = this.oData.approveRoute;
                    break;
                case this.lConstants.PROVIDER_RECHAZADO:
                    route = this.oData.rejectRoute;
                    break;
                case this.lConstants.PROVIDER_PENDIENTE_MODIFICAR:

                    if (this.comments == null || this.comments == "") {
                        SGui.showMessage('', 'Debes ingresara un comentario', 'info');
                        return;
                    }

                    route = this.oData.requireModifyRoute;
                    break;

                default:
                    break;
            }

            axios.post(route, {
                    'id_provider': this.id_provider,
                    'comments': this.comments,
                })
                .then(result => {
                    let data = result.data;
                    if (data.success) {
                        this.lProviders = data.lProviders;
                        drawTableJson('table_providers', this.lProviders,
                            'id_provider',
                            'status_provider_id',
                            'provider_short_name',
                            'provider_name',
                            'provider_rfc',
                            'provider_email',
                            'username',
                            'status',
                            'created',
                            'updated'
                        );
                        SGui.showOk();
                        $('#modal_authorize_provider').modal('hide');
                        $('#modal_comments_provider').modal('hide');
                    } else {
                        SGui.showMessage('', data.message, data.icon);
                    }
                })
                .catch(function(error) {
                    console.log(error)
                    SGui.showError(error);
                });
        },

        commentsProvider() {
            $('#modal_authorize_provider').modal('hide');
            $('#modal_comments_provider').modal('show');
        },

        cancelComments() {
            $('#modal_comments_provider').modal('hide');
            $('#modal_authorize_provider').modal('show');
            this.comments = null;
        },

        clean() {
            this.modal_title = null
            this.provider_name = null
            this.provider_short_name = null
            this.provider_rfc = null
            this.provider_email = null
            this.id_provider = null
            this.user_id = null
            this.comments = null
        }
    }
})