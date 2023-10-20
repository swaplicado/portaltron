var app = new Vue({
    el: '#dpsComplementary',
    data: {
        oData: oServerData,
        lDpsComp: oServerData.lDpsComp,
        year: oServerData.year,
        lStatus: oServerData.lStatus,
        lTypes: oServerData.lTypes,
        lAreas: oServerData.lAreas,
        default_area_id: oServerData.default_area_id,
        area_id: '',
        name_area: '',
        reference: null,
        modal_title: null,
        type_name: null,
        type_id:  null,
        pdf_url: null,
        xml_url: null,
        oDps: null,
        id_dps: null,
        folio: null,
        comments: null,
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
        
        $('#select_area').select2({
            data: self.lAreas,
            placeholder: 'Selecciona area',
        }).on('select2:select', function(e) {
            self.area_id =  e.params.data.id;
        });

        $('#select_area').val(self.default_area_id).trigger('change');

        this.type_id = $('#type_filter').val();
        this.type_name = $('#type_filter').find(':selected').text();
    },
    methods: {
        showModal(data){
            this.id_dps = data[indexesDpsCompTable.id_dps];
            this.name_area = data[indexesDpsCompTable.area];
            let folio = data[indexesDpsCompTable.folio] != null ? data[indexesDpsCompTable.folio] : ''
            this.modal_title = data[indexesDpsCompTable.type] + ' ' + folio;
            this.getDpsComp();
        },

        upload(){ 
            this.modal_title = "Carga de " +this.type_name;
            this.clean();
            $('#select_area').val(self.default_area_id).trigger('change');
            this.area_id = self.default_area_id;
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
                    this.comments = this.oDps.requester_comment_n;
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
            this.area_id = null;
            this.name_area = null;
            this.folio = null;
            this.comments = null;
        }
    }
})