var app = new Vue({
    el: '#payComplements',
    data: {
        oData: oServerData,
        lDpsPayComp: oServerData.lDpsPayComp,
        lStatus: oServerData.lStatus,
        year: oServerData.year,
        lAreas: oServerData.lAreas,
        default_area_id: oServerData.default_area_id,
        modal_title: null,
        name_area: null,
        pdf_url: null,
        xml_url: null,
        folio: null,
    },
    mounted(){
        self = this;

        $('.select2-class').select2({});

        $('#status_filter').select2({
            data: self.lStatus,
        }).on('select2:select', function(e) {
            
        });
        
        $('#select_area').select2({
            data: self.lAreas,
            placeholder: 'Selecciona area',
        }).on('select2:select', function(e) {
            self.area_id =  e.params.data.id;
        });

        $('#select_area').val(self.default_area_id).trigger('change');
    },
    methods:{
        showModal(data){
            this.id_dps = data[indexesPayCompTable.id_dps];
            this.name_area = data[indexesPayCompTable.area];
            this.folio = data[indexesPayCompTable.folio];
            this.modal_title = "CFDI de pago " + this.folio;
            this.getPayComp();
        },

        upload(){ 
            this.modal_title = "Carga de complemento de pago";
            this.clean();
            $('#select_area').val(self.default_area_id).trigger('change');
            this.area_id = self.default_area_id;
            $('#modal_pay_complements').modal('show');
        },

        savePayComplement(){
            SGui.showWaitingUnlimit();

            let route = this.oData.savePayComplementRoute;

            const formData = new FormData();

            let inputPdf = document.getElementById('pdf');
            let filePdf = inputPdf.files[0];
            formData.append('pdf', filePdf);

            let inputXml = document.getElementById('xml');
            let fileXml = inputXml.files[0];
            formData.append('xml', fileXml);
            formData.append('year', this.year);
            formData.append('area_id', this.area_id);
            formData.append('folio', this.folio);

            axios.post(route, formData, {
                headers: {
                    'Content-Type': 'multipart/form-data'
                },
            })
            .then( result => {
                let data = result.data;
                if(data.success){
                    this.lDpsPayComp = data.lDpsPayComp;
                    drawTableDpsPaycomplement(this.lDpsPayComp);
                    SGui.showOk();
                    $('#modal_pay_complements').modal('hide');
                }else{
                    SGui.showMessage('', data.message, data.icon);
                }
            })
            .catch( function(error){
                console.log(error);
            });
        },

        getPayComp(){
            SGui.showWaitingUnlimit();

            let route = this.oData.getPayComplementRoute;

            axios.post(route, {
                'id_dps': this.id_dps,
            })
            .then( result => {
                let data =  result.data;
                if(data.success){
                    this.oDps = data.oDps;
                    this.pdf_url = this.oDps.pdf_url_n;
                    this.xml_url = this.oDps.xml_url_n;
                    this.folio = this.oDps.folio_n;
                    Swal.close();
                    $('#modal_get_pay_complement').modal('show');
                }else{
                    SGui.showMessage('', data.message, data.icon);
                }
            })
            .catch( function(error){
                console.log(error);
                SGui.showError(error);
            });
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

            this.id_dps = null;
            this.area_id = null;
            this.name_area = null;
            this.folio = null;
        },

        getlPayCompByYear(){
            SGui.showWaitingUnlimit();

            let route = this.oData.getlPayCompByYearRoute;

            axios.post(route, {
                'year': this.year
            })
            .then( result => {
                let data = result.data;
                if(data.success){
                    this.lDpsPayComp = data.lDpsPayComp;
                    drawTableDpsPaycomplement(this.lDpsPayComp);
                    SGui.showOk();
                }else{
                    SGui.showMessage('', data.message, data.icon);
                }
            })
            .catch( function(error){
                console.log(error);
                SGui.showError(error);
            })
        },
    }
});