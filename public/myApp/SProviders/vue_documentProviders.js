var self;
var app = new Vue({
    el: '#sproviders',
    data: {
        oData: oServerData,
        lProviders: oServerData.lProviders,
        lConstants: oServerData.lConstants,
        lAreas: oServerData.lAreas,
        lSysDocs: oServerData.lDocs,
        area_id: oServerData.area_id,
        modal_title: null,
        provider_name: null,
        provider_short_name: null,
        provider_rfc: null,
        provider_email: null,
        provider_fiscal_regime: null,
        provider_area: null,
        id_provider: null,
        user_id: null,
        comments: null,
        lDocuments: [],
        showWaitinIcon: false,
        canAuthorize: false,
        new_area_id: null,
    },
    watch: {
        lDocuments:function(val){
            if(this.lDocuments.length > 0){
                for(let doc of this.lDocuments){
                    if(doc.is_accept || doc.is_reject || doc.url == null){
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

        $('#select_area').select2({
            data: self.lAreas,
            placeholder: 'Selecciona área',
        }).on('select2:select', function(e) {
            self.new_area_id =  e.params.data.id;
        });

        $('#select_area').val('').trigger('change');

    },
    methods: {
        async showModal(data) {
            this.clean();
            this.id_provider = data[indexesProvidersTable.id_provider];
            this.modal_title = 'Proveedor: ' + data[indexesProvidersTable.provider_name]
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
                        this.provider_fiscal_regime = data.oProvider.fiscal_regime_name;
                        this.provider_area = data.oProvider.area;
                        this.user_id = data.oProvider.user_id;
                        this.lDocuments = data.lDocuments;

                        for(let doc of this.lSysDocs){
                            let found = false;
                            for(let doc2 of this.lDocuments){
                                if(doc2.id_request_type_doc == doc.id_request_type_doc){
                                    found = true;
                                    break;
                                }
                            }
                            if(!found){
                                this.lDocuments.push({
                                    'id_request_type_doc': doc.id_request_type_doc,
                                    'name': doc.name,
                                    'url': null
                                });
                            }
                        }

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
            this.new_area_id = null;
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
        },

        async editModal(data) {
            $('#select_area').val('').trigger('change');
            this.clean();
            this.id_provider = data[indexesProvidersTable.id_provider];
            await this.getProviderData();
            $('#modal_new_area_provider').modal('show');
        },

        updateAreaProvider() {
            if(this.new_area_id == null){
                SGui.showMessage('', 'Debes seleccionar una nueva área destino', 'error');
                return;
            }
            SGui.showWaitingUnlimit();
            let route = this.oData.updateAreaRoute;
            axios.post(route, {
                'id_provider': this.id_provider,
                'id_area': this.new_area_id,
            })
            .then( result =>  {
                let data = result.data;
                if(data.success){
                    this.lProviders = data.lProviders;
                    drawTableJson('table_providers', this.lProviders, 
                        'id_provider', 
                        'provider_name',
                        'provider_short_name',
                        'provider_rfc',
                        'fiscal_regime',
                        'area',
                        'provider_email',
                        'number_pen_doc'
                    );
                    $('#modal_new_area_provider').modal('hide');
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
})