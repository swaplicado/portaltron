var app = new Vue({
    el: '#quotationsapp',
    data:{
        oData: oServerData,
        lQuotations: oServerData.lQuotations,
        folioUser: null,
        description: null,
        modal_title: null,
        pdf_original_name: null,
        is_edit: false,
        id_quotation: null,
    },
    mounted(){

    },
    methods: {
        createModal(){
            this.modal_title = 'Crear cotización';
            this.cleanData();

            $('#modal_quotations').modal('show');
        },

        editModal(data){
            this.modal_title = 'Editar cotización';
            this.folioUser = data[indexes.folio_user];
            this.description = data[indexes.description];
            this.pdf_original_name = !!data[indexes.pdf_original_name] ? data[indexes.pdf_original_name] : 'No hay archivo';
            this.is_edit = true;
            this.id_quotation = data[indexes.id_quotation];

            document.getElementById('pdf').value = '';
            $('#modal_quotations').modal('show');
        },

        save(){
            const file = this.$refs.pdf.files[0];
            const formData = new FormData();
            formData.append('pdf', file);
            formData.append('folio', this.folioUser);
            formData.append('description', this.description);
            formData.append('id_quotation', this.id_quotation);

            let route;
            if(!this.is_edit){
                route = oServerData.uploadQuotationRoute;
            }else{
                route = oServerData.updateQuotationRoute;
            }

            axios.post(route, formData, {
                headers: {
                    'Content-Type': 'multipart/form-data'
                },
            }).then(response => {
                let data = response.data;
                if(data.success){
                    drawTable('table_quotations', data.lQuotations);
                    $('#modal_quotations').modal('hide');
                    SGui.showOk();
                }else{
                    SGui.showMessage('', data.message, data.icon);
                }
            }).catch(function(error){
                console.log(error);
                SGui.showError(error);
            });
        },

        showQuotation(){
            let data = table['table_quotations'].row('.selected').data();
            if(data == undefined){
                SGui.showError("Debe seleccionar un renglón");
                return;
            }

            let route = oServerData.showQuotationRoute;
            window.open(route + '/' + data[indexes.id_quotation], '_blank');
        },

        cleanData(){
            this.is_edit = false;
            this.folioUser = null;
            this.description = null;
            document.getElementById('pdf').value = '';
        },

        deleteRegistry(data){
            Swal.fire({
                title: '¿Desea eliminar la cotización ' + data[indexes.folio_user] + '?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Aceptar'
            }).then((result) => {
                if (result.isConfirmed) {
                    this.deleteQuotation(data[indexes.id_quotation]);
                }
            })
        },

        deleteQuotation(id_quotation){
            SGui.showWaiting(15000);

            var route = this.oData.deleteRoute;

            axios.post(route, {
                'id_quotation': id_quotation,
            })
            .then( result => {
                let data = result.data;
                if(data.success){
                    drawTable('table_quotations', data.lQuotations);
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
    }
})