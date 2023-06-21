var app = new Vue({
    el: '#providers',
    data: {
        oData: oServerData,
        lProviders: oServerData.lProviders,
        modal_title: null,
        provider_name: null,
        provider_short_name: null,
        provider_rfc: null,
        provider_email: null,
        id_provider: null,
        is_edit: false,
    },
    mounted(){
        
    },
    methods: {
        createModal(){
            this.is_edit = false;
            this.modal_title = 'Nuevo proveedor';
            this.provider_name = null;
            this.provider_short_name = null;
            this.provider_rfc = null;
            this.provider_email = null;
            $('#modal_providers_form').modal('show');
        },

        editModal(data){
            this.is_edit = true;
            this.modal_title = 'Proveedor: ' + data[indexesProvidersTable.provider_short_name];
            this.id_provider = data[indexesProvidersTable.id_provider];
            this.provider_name = data[indexesProvidersTable.provider_name];
            this.provider_short_name = data[indexesProvidersTable.provider_short_name];
            this.provider_rfc = data[indexesProvidersTable.provider_rfc];
            this.provider_email = data[indexesProvidersTable.provider_email];
            $('#modal_providers_form').modal('show');
        },

        saveProvider(){
            SGui.showWaiting(15000);

            var route = null;
            if(this.is_edit){
                route = this.oData.updateRoute;
            }else{
                route = this.oData.createRoute;
            }

            axios.post(route, {
                'id_provider': this.id_provider,
                'provider_name': this.provider_name,
                'provider_short_name': this.provider_short_name,
                'provider_rfc': this.provider_rfc,
                'provider_email': this.provider_email,
            })
            .then( result => {
                let data = result.data;
                if(data.success){
                    drawTable('table_providers', data.lProviders);
                    $('#modal_providers_form').modal('hide');
                    SGui.showOk();
                }else{
                    SGui.showMessage('', data.message, data.icon);
                }
            })
            .catch( function(error){
                console.log(error);
                SGui.showMessage('', error, 'error');
            });
        },

        deleteRegistry(data){
            Swal.fire({
                title: '¿Desea eliminar al proveedor ' + data[indexesProvidersTable.provider_name] + '?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Aceptar'
            }).then((result) => {
                if (result.isConfirmed) {
                    this.deleteProvider(data[indexesProvidersTable.id_provider]);
                }
            })
        },

        deleteProvider(id_provider){
            SGui.showWaiting(15000);

            var route = this.oData.deleteRoute;

            axios.post(route, {
                'id_provider': id_provider,
            })
            .then( result => {
                let data = result.data;
                if(data.success){
                    drawTable('table_providers', data.lProviders);
                    SGui.showOk();
                }else{
                    SGui.showMessage('', data.message, data.icon);
                }
            })
            .catch( function(error){
                console.log(error);
                SGui.showMessage('', error, 'error');
            });
        }
    }
})