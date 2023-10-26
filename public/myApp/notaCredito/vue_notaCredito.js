var app = new Vue({
    el: "#notaCredito",
    data: {
        oData: oServerData,
        lNotaCredito: oServerData.lNotaCredito,
        lStatus: oServerData.lStatus,
        lAreas: oServerData.lAreas,
        area_id: '',
        serie: '',
        folio: '',
        serieFolio: '',
        reference: '',
        modal_title: '',
        pdf_url: '',
        xml_url: '',
        comments: '',
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
        showModal(data){
            this.id_dps = data[indexesNCTable.id_dps];
            this.name_area = data[indexesNCTable.area];
            let folio = data[indexesNCTable.folio] != null ? data[indexesNCTable.folio] : '';
            this.modal_title = data[indexesNCTable.type] + ' ' + folio;
            this.getNotaCredito()
                .then(data => {
                    $('#modal_get_notaCredito').modal('show');
                });
        },

        upload(){
            this.modal_title = "Carga de nota de crédito";
            this.clean();
            $('#modal_up_notaCredito').modal('show');
        },

        saveNotaCredito(){
            SGui.showWaitingUnlimit();

            let route = this.oData.saveNotaCreditoRoute;

            const formData = new FormData();

            let inputPdf = document.getElementById('pdf');
            let filePdf = inputPdf.files[0];
            formData.append('pdf', filePdf);

            let inputXml = document.getElementById('xml');
            let fileXml = inputXml.files[0];
            formData.append('xml', fileXml);

            formData.append('reference', this.reference);
            formData.append('area_id', this.area_id);
            formData.append('serie', this.serie);
            formData.append('folio', this.folio);
            formData.append('serieFolio', this.serieFolio);

            axios.post(route, formData, {
                headers: {
                    'Content-Type': 'multipart/form-data'
                },
            })
            .then( result => {
                let data = result.data;
                if(data.success){
                    this.lNotaCredito = data.lNotaCredito;
                    drawTableNotaCredito(this.lNotaCredito);
                    SGui.showOk();
                    $('#modal_up_notaCredito').modal('hide');
                }else{
                    SGui.showMessage('', data.message, data.icon);
                }
            })
            .catch( function(error){
                console.log(error);
            });
        },

        getNotaCredito(){
            SGui.showWaitingUnlimit();

            let route = this.oData.getNotaCreditoRoute;

            return new Promise((resolve, reject) => 
                axios.post(route, {
                    'id_dps': this.id_dps,
                })
                .then( result => {
                    let data =  result.data;
                    if(data.success){
                        this.oDps = data.oDps;
                        this.reference = this.oDps.reference_string;
                        this.comments = this.oDps.requester_comment_n;
                        this.pdf_url = this.oDps.pdf_url_n;
                        this.xml_url = this.oDps.xml_url_n;
                        Swal.close();
                        resolve(true);
                    }else{
                        SGui.showMessage('', data.message, data.icon);
                        reject(data.message);
                    }
                })
                .catch( function(error){
                    console.log(error);
                    SGui.showError(error);
                    reject(error);
                })
            );
        },

        clean(){
            let inputPdf = document.getElementById('pdf');
            inputPdf.value = null;
            let inputPdfName = document.getElementById('pdfName');
            inputPdfName.value = null;

            let inputXml = document.getElementById('xml');
            inputXml.value = null;
            let inputXmlName = document.getElementById('xmlName');
            inputXmlName.value = null;

            this.area_id = '';
            this.serie = '';
            this.folio = '';
            this.reference = '';
            this.modal_title = '';
            this.pdf_url = '';
            this.xml_url = '';
            this.comments = '';
            this.serieFolio = '';
        }
    }
})