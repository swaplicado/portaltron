var app = new Vue({
    el: '#payComplementsManager',
    data: {
        oData: oServerData,
        lDpsPayComp: oServerData.lDpsPayComp,
        year: oServerData.year,
        lStatus: oServerData.lStatus,
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
        folio: null,
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
    },
    methods: {
        getPayCompProvider(){
            SGui.showWaitingUnlimit();

            let route = this.oData.getPayComplementsProviderRoute;

            axios.post(route, {
                'provider_id': this.provider_id,
            })
            .then( result => {
                let data = result.data;
                if(data.success){
                    this.lDpsPayComp = data.lDpsPayComp;
                    drawTableDpsPaycomplement(this.lDpsPayComp);
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

        getlDpsCompByYear(){
            SGui.showWaitingUnlimit();

            let route = this.oData.getPayComplementsProviderRoute;

            axios.post(route, {
                'provider_id': this.provider_id,
                'year': this.year,
            })
            .then( result => {
                let data = result.data;
                if(data.success){
                    this.lDpsPayComp = data.lDpsPayComp;
                    drawTableDpsPaycomplement(this.lDpsPayComp);
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
            this.id_dps = data[indexesPayCompTable.id_dps];
            this.name_area = data[indexesPayCompTable.area];
            this.folio = data[indexesPayCompTable.folio];
            this.getPayComp();
        },

        getPayComp(){
            SGui.showWaitingUnlimit();

            let route = this.oData.getPayComplementManagerRoute;

            axios.post(route, {
                'id_dps': this.id_dps,
            })
            .then( result => {
                let data =  result.data;
                if(data.success){
                    this.oDps = data.oDps;
                    this.check_status = this.oDps.check_status;
                    this.pdf_url = this.oDps.pdf_url_n;
                    this.xml_url = this.oDps.xml_url_n;
                    Swal.close();
                    $('#modal_pay_complement').modal('show');
                }else{
                    SGui.showMessage('', data.message, data.icon);
                }
            })
            .catch( function(error){
                console.log(error);
                SGui.showError(error);
            });
        },

        setVoboPayComplement(authorize){
            SGui.showWaitingUnlimit();
            let is_accept = authorize;
            let is_reject = !authorize;

            let route = this.oData.setVoboPayComplementRoute;

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
                    this.lDpsPayComp = data.lDpsPayComp;
                    drawTableDpsPaycomplement(this.lDpsPayComp);
                    $('#modal_pay_complement').modal('hide');
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