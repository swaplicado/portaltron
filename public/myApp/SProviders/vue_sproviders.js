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
        oArea: oServerData.oArea,
        user_id: null,
        comments: null,
        lDocuments: [],
        rowClass: null,
        showWaitinIcon: false,
        enableAuthorize: false,
        canAuthorize: false,
        enableModify: false,
    },
    watch: {
        lDocuments:function(val){
            if(this.lDocuments.length > 0){
                for(let doc of this.lDocuments){
                    if(doc.check_status == this.lConstants.VOBO_REVISION){
                        this.canAuthorize = true;
                        break;
                    }else{
                        this.canAuthorize = false;
                    }
                }

                for(let doc of this.lDocuments){
                    if(doc.is_accept || doc.is_reject){
                        this.enableModify = true;
                    }else{
                        this.enableModify = false;
                        break;
                    }
                }

                for(let doc of this.lDocuments){
                    if(doc.is_accept){
                        this.enableAuthorize = true;
                    }else{
                        this.enableAuthorize = false;
                        break;
                    }
                }
            }else{
                this.enableAuthorize = false;
            }
        }
    },
    mounted(){
        self = this;

        $('.select2-class').select2({});

        $('#status_filter').select2({
            data: self.lStatus,
        }).on('select2:select', function(e) {
            
        });
    },
    methods: {
        async showModal(data){
            this.clean();
            this.id_provider = data[indexesProvidersTable.id_provider];
            this.modal_title = 'Autorización de proveedor: ' + data[indexesProvidersTable.provider_name]
            await this.getProviderData();

            if(this.canAuthorize){
                $('#modal_authorize_provider').modal('show');
            }else{
                $('#modal_noAuthorize_provider').modal('show');
            }
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
                        this.lDocuments = data.lDocuments;
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

                    if(this.comments == null || this.comments == ""){
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
                    $('#modal_comments_provider').modal('hide');
                }else{
                    SGui.showMessage('', data.message, data.icon);
                }
            })
            .catch( function(error){
                console.log(error)
                SGui.showError(error);
            });
        },

        commentsProvider(){
            $('#modal_authorize_provider').modal('hide');
            $('#modal_comments_provider').modal('show');
        },

        cancelComments(){
            $('#modal_comments_provider').modal('hide');
            $('#modal_authorize_provider').modal('show');
            this.comments = null;
        },

        clean(){
            this.modal_title = null;
            this.provider_name = null;
            this.provider_short_name = null;
            this.provider_rfc = null;
            this.provider_email = null;
            this.id_provider = null;
            this.user_id = null;
            this.comments = null;
            this.showWaitinIcon = false;
        },

        approveDoc(id_vobo){
            this.showWaitinIcon = true;
            let route = this.oData.voboDocRoute;
            axios.post(route, {
                'id_vobo': id_vobo,
                'is_accept': true,
                'is_reject': false,
                'id_provider': this.id_provider,
                'id_area': this.oArea.id_area,
            })
            .then( result =>  {
                let data = result.data;
                if(data.success){
                    this.lDocuments = data.lDocuments;
                    this.showWaitinIcon = false;
                }else{
                    SGui.showMessage('', data.message, data.icon);
                    this.showWaitinIcon = false;
                }
            })
            .catch( function(error){
                this.showWaitinIcon = false;
                console.log(error);
                SGui.showError(error);
            });
        },

        rejectDoc(id_vobo){
            this.showWaitinIcon = true;
            let route = this.oData.voboDocRoute;
            axios.post(route, {
                'id_vobo': id_vobo,
                'is_accept': false,
                'is_reject': true,
                'id_provider': this.id_provider,
                'id_area': this.oArea.id_area,
            })
            .then( result =>  {
                let data = result.data;
                if(data.success){
                    this.lDocuments = data.lDocuments;
                    this.showWaitinIcon = false;
                }else{
                    SGui.showMessage('', data.message, data.icon);
                    this.showWaitinIcon = false;
                }
            })
            .catch( function(error){
                this.showWaitinIcon = false;
                console.log(error);
                SGui.showError(error);
            });
        }
    }
})