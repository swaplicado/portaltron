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
        showProvider: true,
        check_status: 0,

        lDpsReasons: [],
        rejection_id: null,
        comments: null,
        is_reject: 0,

        lAreas: oServerData.lAreas,
        area_id: "",
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

        // $('#provider_filter').val('').trigger('change');

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
            dropdownParent: $('#modal_change_dps_complementary')
        }).on('select2:select', function(e) {
            self.area_id =  e.params.data.id;
        });

        this.provider_id = $('#provider_filter').val();
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

        getlDpsCompByYear(){
            SGui.showWaitingUnlimit();

            let route = this.oData.getcomplementsManagerRoute;

            axios.post(route, {
                'provider_id': this.provider_id,
                'year': this.year,
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
            this.getDpsComp()
                .then(data => {
                    $('#freqComments').select2({
                        data: self.lDpsReasons,
                        placeholder: "Comentarios frecuentes",
                        dropdownParent: $('#modal_dps_complementary_comments')

                    }).on('select2:select', function(e) {
                        self.rejection_id = e.params.data.id;
                        self.comments = e.params.data.text;
                    });

                    $('#freqComments').val('').trigger('change');

                    $('#modal_dps_complementary').modal('show');
                });
        },

        getDpsComp(){
            SGui.showWaitingUnlimit();

            let route = this.oData.getDpsComplementManagerRoute;

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
                        this.reference = this.oDps.reference;
                        this.pdf_url = this.oDps.pdf_url_n;
                        this.xml_url = this.oDps.xml_url_n;
                        this.area_id = this.oDps.area_id;

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
                    reject(data.message);
                })
            );
        },

        rejectDps(){
            $('#modal_dps_complementary_comments').modal('show');
        },

        /**
         * Metodo para aprobar o rechazar un dps
         * @param {*} authorize 
         */
        setVoboComplement(authorize){
            SGui.showWaitingUnlimit();
            let is_accept = authorize;
            let is_reject = !authorize;

            if(is_reject && (this.comments == null || this.comments == '')){
                Swal.close();
                SGui.showMessage('', 'Debe ingresar un comentario', 'warning');
                return;
            }

            let route = this.oData.setVoboComplementRoute;

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
                    this.lDpsComp = data.lDpsComp;
                    drawTableDpsComplementary(this.lDpsComp);
                    $('#modal_dps_complementary_comments').modal('hide');
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
            this.comments = null;
            this.lDpsReasons = [];
            this.is_reject = 0;
            this.area_id = "";
            this.provider_id = $('#provider_filter').val();
            this.id_dps = null;
            this.type_id = $('#type_filter').val();
        },

        change(data){
            this.clean();
            this.id_dps = data[indexesDpsCompTable.id_dps];
            this.name_area = data[indexesDpsCompTable.area];

            this.getDpsComp()
                .then(data => {
                    $('#select_area').val(this.area_id).trigger('change');
                    $('#modal_change_dps_complementary').modal('show');
                });
        },

        sendChange(){
            SGui.showWaitingUnlimit();

            let route = this.oData.changeAreaDpsRoute;

            axios.post(route, {
                'area_id': this.area_id,
                'provider_id': this.provider_id,
                'dps_id': this.id_dps,
                'type_id': this.type_id,
            })
            .then( result => {
                let data = result.data;
                if(data.success){
                    this.lDpsComp = data.lDpsComp;
                    drawTableDpsComplementary(this.lDpsComp);
                    SGui.showOk();
                    $('#modal_change_dps_complementary').modal('hide');
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