var self;
var app = new Vue({
    el: '#sproviders',
    data: {
        oData: oServerData,
        lConstants: oServerData.lConstants,
        lProviders: oServerData.lProviders,
        lStatus: oServerData.lStatus,
        modal_title: null,
        provider_name: null,
        provider_short_name: null,
        provider_rfc: null,
        provider_email: null,
        id_provider: null,
        user_id: null,
    },
    mounted(){
        self = this;

        $('.select2-class').select2({})

        $('#status_filter').select2({
            data: self.lStatus,
        }).on('select2:select', function(e) {
            
        });
    },
    methods: {
        async showModal(data){
            this.id_provider = data[indexesProvidersTable.id_provider];
            this.modal_title = 'Autorización de proveedor: ' + data[indexesProvidersTable.provider_name]
            await this.getProviderData();

            $('#modal_authorize_provider').modal('show');
        },

        getProviderData(){
            SGui.showWaitingUnlimit();
            let route = oServerData.getProviderRoute;

            return new Promise((resolve, reject) => 
                axios.post(route,{
                    'provider_id': this.id_provider,
                })
                .then(result => {
                    let data = result.data;
                    
                    if(data.success){
                        this.provider_name = data.oProvider.provider_name;
                        this.provider_short_name = data.oProvider.provider_short_name;
                        this.provider_rfc = data.oProvider.provider_rfc;
                        this.provider_email = data.oProvider.provider_email;
                        this.user_id = data.oProvider.user_id;
                        Swal.close();
                        resolve('ok');
                    }else{
                        SGui.showMessage('', data.message, data.icon);
                        reject('error');
                    }

                })
                .catch(function(error){
                    console.log(error);
                    SGui.showError(error);
                    reject('error');
                })
            );
        },

        /**
         * Metodo para aprobar, rechazar o solicitar modificacion a un proveedor
         */
        setStatusProvider(status){
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
                    route = this.oData.requireModifyRoute;    
                    break;
            
                default:
                    break;
            }

            axios.post(route, {
                'id_provider': this.id_provider,
            })
            .then( result => {
                let data = result.data;
                if(data.success){
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
                }else{
                    SGui.showMessage('', data.message, data.icon);
                }
            })
            .catch( function(error){
                console.log(error)
                SGui.showError(error);
            });
        }
    }
})