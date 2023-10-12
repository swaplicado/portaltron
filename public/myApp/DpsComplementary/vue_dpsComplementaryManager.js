var app = new Vue({
    el: '#dpsComplementaryManager',
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

        provider_id: null,
        lProviders: oServerData.lProviders,
        showProvider: false,
        check_status: 0,
    },
    mounted(){
        self = this;

        $('.select2-class').select2({});

        $('#provider_filter').select2({
            placeholder: 'Selecciona proveedor',
            data: self.lProviders,
        }).on('select2:select', function(e) {
            self.provider_id = e.params.data.id;
        });

        $('#provider_filter').val('').trigger('change');

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
        getComplementsProvider(){
            SGui.showWaitingUnlimit();

            let route = this.oData.getcomplementsManagerRoute;

            axios.post(route, {
                'provider_id': this.provider_id,
            })
            .then( result => {
                let data = result.data;
                if(data.success){
                    this.lDpsComp = data.lDpsComp;
                    drawTableDpsComplementary(this.lDpsComp);
                    this.showProvider = true;
                    SGui.showOk();
                }else{
                    SGui.showMessage('', data.message, data.icon);
                }
            })
            .catch( function(error){
                console.log(error);
                SGui.showError(error);
            });
        },

        showModal(data){
            this.clean();
            this.id_dps = data[indexesDpsCompTable.id_dps];
            this.name_area = data[indexesDpsCompTable.area];
            this.getDpsComp();
        },

        getDpsComp(){
            SGui.showWaitingUnlimit();

            let route = this.oData.getDpsComplementManagerRoute;

            axios.post(route, {
                'id_dps': this.id_dps,
            })
            .then( result => {
                let data =  result.data;
                if(data.success){
                    this.oDps = data.oDps;
                    this.check_status = this.oDps.check_status;
                    this.reference = this.oDps.reference;
                    this.pdf_url = this.oDps.pdf_url_n;
                    this.xml_url = this.oDps.xml_url_n;
                    Swal.close();
                    $('#modal_dps_complementary').modal('show');
                }else{
                    SGui.showMessage('', data.message, data.icon);
                }
            })
            .catch( function(error){
                console.log(error);
                SGui.showError(error);
            });
        },

        setVoboComplement(authorize){
            SGui.showWaitingUnlimit();
            let is_accept = authorize;
            let is_reject = !authorize;

            let route = this.oData.setVoboComplementRoute;

            axios.post(route, {
                'id_dps': this.id_dps,
                'is_accept': is_accept,
                'is_reject': is_reject,
                'year': this.year,
                'provider_id': this.provider_id,
            })
            .then( result => {
                let data = result.data;
                if(data.success){
                    this.lDpsComp = data.lDpsComp;
                    drawTableDpsComplementary(this.lDpsComp);
                    $('#modal_dps_complementary').modal('hide');
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
            this.id_dps = null;
            this.name_area = null;
            this.oDps = null;
            this.check_status = 0;
            this.reference = null;
            this.pdf_url = null;
            this.xml_url = null;
        }
    }
})