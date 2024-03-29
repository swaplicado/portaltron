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
        comments: null,
        oDps: null,
        id_dps: null,

        provider_id: null,
        lProviders: oServerData.lProviders,
        showProvider: true,
        check_status: 0,

        lDpsReasons: [],
        rejection_id: null,
        comments: null,
        is_reject: 0,

        lAreas: oServerData.lAreas,
        area_id: "",

        is_omision: false,
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

        //$('#provider_filter').val('').trigger('change');

        $('#status_filter').select2({
            data: self.lStatus,
        }).on('select2:select', function(e) {
            
        });

        $('#select_area').select2({
            data: self.lAreas,
            placeholder: 'Selecciona area',
            dropdownParent: $('#modal_change_pay_complement')
        }).on('select2:select', function(e) {
            self.area_id =  e.params.data.id;
        });

        this.provider_id = $('#provider_filter').val();
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
            this.comments = data[indexesPayCompTable.comments];
            this.getPayComp()
                .then( data => {
                    $('#freqComments').select2({
                        data: self.lDpsReasons,
                        placeholder: "Comentarios frecuentes",
                        dropdownParent: $('#modal_dps_complementary_comments')

                    }).on('select2:select', function(e) {
                        self.rejection_id = e.params.data.id;
                        self.comments = e.params.data.text;
                    });

                    $('#freqComments').val('').trigger('change');

                    $('#modal_pay_complement').modal('show');
                });
        },

        getPayComp(){
            SGui.showWaitingUnlimit();

            let route = this.oData.getPayComplementManagerRoute;

            return new Promise((resolve, reject) => 
                axios.post(route, {
                    'id_dps': this.id_dps,
                })
                .then( result => {
                    let data =  result.data;
                    if(data.success){
                        this.oDps = data.oDps;
                        this.check_status = this.oDps.check_status;
                        this.comments = this.oDps.requester_comment_n;
                        this.pdf_url = this.oDps.pdf_url_n;
                        this.xml_url = this.oDps.xml_url_n;

                        this.lDpsReasons = data.lDpsReasons;
                        this.is_reject = this.oDps.is_reject;

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

        rejectPayComp(){
            $('#modal_dps_complementary_comments').modal('show');
        },

        setVoboPayComplement(authorize){
            SGui.showWaitingUnlimit();
            let is_accept = authorize;
            let is_reject = !authorize;

            if(is_reject && (this.comments == null || this.comments == '')){
                Swal.close();
                SGui.showMessage('', 'Debe ingresar un comentario', 'warning');
                return;
            }


            let route = this.oData.setVoboPayComplementRoute;

            axios.post(route, {
                'id_dps': this.id_dps,
                'is_accept': is_accept,
                'is_reject': is_reject,
                'year': this.year,
                'provider_id': this.provider_id,
                'comments': this.comments,
                'rejection_id': this.rejection_id,
            })
            .then( result => {
                let data = result.data;
                if(data.success){
                    this.lDpsPayComp = data.lDpsPayComp;
                    drawTableDpsPaycomplement(this.lDpsPayComp);
                    $('#modal_dps_complementary_comments').modal('hide');
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
            this.comments = null;
            this.lDpsReasons = [];
            this.is_reject = 0;
        },

        change(data){
            this.clean();
            this.id_dps = data[indexesPayCompTable.id_dps];
            this.name_area = data[indexesPayCompTable.area];
            this.folio = data[indexesPayCompTable.folio];
            this.comments = data[indexesPayCompTable.comments];
            this.getPayComp()
                .then( data => {
                    $('#modal_change_pay_complement').modal('show');
                });
        },

        setChange(){
            SGui.showWaitingUnlimit();

            let route = this.oData.changeAreaPayComplementRoute;

            axios.post(route, {
                'area_id': this.area_id,
                'provider_id': this.provider_id,
                'dps_id': this.id_dps,
            })
            .then( result => {
                let data = result.data;
                if(data.success){
                    this.lDpsPayComp = data.lDpsPayComp;
                    drawTableDpsPaycomplement(this.lDpsPayComp);
                    SGui.showOk();
                    $('#modal_change_pay_complement').modal('hide');
                }else{
                    SGui.showMessage('', data.message, data.icon);
                }
            })
            .catch( function(error){
                console.log(error);
                SGui.showError(error);
            });
        },

        getDpsPayComplementOmision(omision){
            SGui.showWaitingUnlimit();

            let route = this.oData.getPayComplementOmisionRoute;

            axios.post(route, {
                'omision': omision,
            })
            .then( result => {
                let data = result.data;
                if(data.success){
                    this.is_omision = omision;
                    this.lDpsPayComp = data.lDpsPayComp;
                    drawTableDpsPaycomplement(this.lDpsPayComp);
                    $('#provider_filter').val(0).trigger('change');
                    this.provider_id = $('#provider_filter').val();
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