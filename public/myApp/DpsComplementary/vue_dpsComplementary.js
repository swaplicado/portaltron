var app = new Vue({
    el: '#dpsComplementary',
    data: {
        oData: oServerData,
        lDpsComp: oServerData.lDpsComp,
        year: oServerData.year,
        lStatus: oServerData.lStatus,
        lTypes: oServerData.lTypes,
        reference: null,
        modal_title: null,
        type_name: null,
        type_id:  null,
        pdf_url: null,
        xml_url: null,
        oDps: null,
        id_dps: null,
    },
    mounted(){
        self = this;

        $('.select2-class').select2({});

        $('#status_filter').select2({
            data: self.lStatus,
        }).on('select2:select', function(e) {
            
        });

        $('#type_filter').select2({
            data: self.lTypes,
        }).on('select2:select', function(e) {
            self.type_id = e.params.data.id;
            self.type_name = e.params.data.text;
        });

        this.type_id = $('#type_filter').val();
        this.type_name = $('#type_filter').find(':selected').text();
    },
    methods: {
        showModal(data){
            this.id_dps = data[indexesDpsCompTable.id_dps];
            this.getDpsComp();
        },

        upload(){ 
            this.modal_title = "Carga de " +this.type_name;
            this.clean();
            $('#modal_dps_complementary').modal('show');
        },

        saveComplementary(){
            SGui.showWaitingUnlimit();

            let route = this.oData.saveComplementsRoute;

            const formData = new FormData();

            let inputPdf = document.getElementById('pdf');
            let filePdf = inputPdf.files[0];
            formData.append('pdf', filePdf);

            let inputXml = document.getElementById('xml');
            let fileXml = inputXml.files[0];
            formData.append('xml', fileXml);

            formData.append('reference', this.reference);
            formData.append('type_id', this.type_id);
            formData.append('year', this.year);

            axios.post(route, formData, {
                headers: {
                    'Content-Type': 'multipart/form-data'
                },
            })
            .then( result => {
                let data = result.data;
                if(data.success){
                    this.lDpsComp = data.lDpsComp;
                    drawTableDpsComplementary(this.lDpsComp);
                    SGui.showOk();
                    $('#modal_dps_complementary').modal('hide');
                }else{
                    SGui.showMessage('', data.message, data.icon);
                }
            })
            .catch( function(error){
                console.log(error);
            });
        },

        getDpsComp(){
            SGui.showWaitingUnlimit();

            let route = this.oData.GetComplementsRoute;

            axios.post(route, {
                'id_dps': this.id_dps,
            })
            .then( result => {
                let data =  result.data;
                if(data.success){
                    this.oDps = data.oDps;
                    this.reference = this.oDps.reference;
                    this.pdf_url = this.oDps.pdf_url_n;
                    this.xml_url = this.oDps.xml_url_n;
                    Swal.close();
                    $('#modal_get_dps_complementary').modal('show');
                }else{
                    SGui.showMessage('', data.message, data.icon);
                }
            })
            .catch( function(error){
                console.log(error);
                SGui.showError(error);
            });
        },

        updateComplementary(){

        },

        getlDpsCompByYear(){
            SGui.showWaitingUnlimit();

            let route = this.oData.getCompByYearRoute;

            axios.post(route, {
                'year': this.year
            })
            .then( result => {
                let data = result.data;
                if(data.success){
                    this.lDpsComp = data.lDpsComp;
                    drawTableDpsComplementary(this.lDpsComp);
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

        clean(){
            let inputPdf = document.getElementById('pdf');
            inputPdf.value = null;
            let inputPdfName = document.getElementById('pdfName');
            inputPdfName.value = null;

            let inputXml = document.getElementById('xml');
            inputXml.value = null;
            let inputXmlName = document.getElementById('xmlName');
            inputXmlName.value = null;

            this.reference = null;
        }
    }
})