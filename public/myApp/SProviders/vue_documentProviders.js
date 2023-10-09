var self;
var app = new Vue({
    el: '#sproviders',
    data: {
        oData: oServerData,
        lProviders: oServerData.lProviders,
        lConstants: oServerData.lConstants,
        area_id: oServerData.area_id,
        modal_title: null,
        provider_name: null,
        provider_short_name: null,
        provider_rfc: null,
        provider_email: null,
        id_provider: null,
        user_id: null,
        comments: null,
        lDocuments: [],
        lDocuments: [],
        showWaitinIcon: false,
        canAuthorize: false,
    },
    watch: {
        lDocuments:function(val){
            if(this.lDocuments.length > 0){
                for(let doc of this.lDocuments){
                    if(doc.is_accept || doc.is_reject){
                        this.canAuthorize = false;
                    }else{
                        this.canAuthorize = true;
                        break;
                    }
                }
            }else{
                this.enableAuthorize = false;
            }
        }
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

            if(this.canAuthorize){
                $('#modal_documents_authorize_provider').modal('show');
            }else{
                $('#modal_documents_noAuthorize_provider').modal('show');
            }
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
                        this.lDocuments = data.lDocuments;
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

        clean() {
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
            let route = this.oData.updateVoboDocRoute;
            axios.post(route, {
                'id_vobo': id_vobo,
                'is_accept': true,
                'is_reject': false,
                'id_provider': this.id_provider,
                'id_area': this.area_id,
            })
            .then( result =>  {
                let data = result.data;
                if(data.success){
                    this.lDocuments = data.lDocuments;
                    this.lProviders = data.lProviders;
                    drawTableJson('table_providers', this.lProviders, 
                                    'id_provider', 
                                    'provider_short_name',
                                    'provider_name',
                                    'provider_rfc',
                                    'provider_email',
                                    'number_pen_doc'
                                );
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
            let route = this.oData.updateVoboDocRoute;
            axios.post(route, {
                'id_vobo': id_vobo,
                'is_accept': false,
                'is_reject': true,
                'id_provider': this.id_provider,
                'id_area': this.area_id,
            })
            .then( result =>  {
                let data = result.data;
                if(data.success){
                    this.lDocuments = data.lDocuments;
                    this.lProviders = data.lProviders;
                    drawTableJson('table_providers', this.lProviders, 
                                    'id_provider', 
                                    'provider_short_name',
                                    'provider_name',
                                    'provider_rfc',
                                    'provider_email',
                                    'number_pen_doc'
                                );
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