var app = new Vue({
    el: '#notaCredito',
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

        provider_id: '',
        lProviders: oServerData.lProviders,
        showProvider: true,
        check_status: 0,

        lDpsReasons: [],
        rejection_id: null,
        is_reject: 0,

        name_area: '',

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

        // $('#provider_filter').val('').trigger('change');

        $('#status_filter').select2({
            data: self.lStatus,
        }).on('select2:select', function(e) {
            
        });

        $('#select_area').select2({
            data: self.lAreas,
            placeholder: 'Selecciona area',
            dropdownParent: $('#modal_change_notaCredito')
        }).on('select2:select', function(e) {
            self.area_id =  e.params.data.id;
        });

        this.provider_id = $('#provider_filter').val();
    },
    methods: {
        getNotasCreditoProvider(){
            SGui.showWaitingUnlimit();

            let route = this.oData.getNotasCreditoProviderRoute;

            axios.post(route, {
                'provider_id': this.provider_id,
            })
            .then( result => {
                let data = result.data;
                if(data.success){
                    this.lNotaCredito = data.lNotaCredito;
                    drawTableNotaCredito(this.lNotaCredito);
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
            this.id_dps = data[indexesNCTable.id_dps];
            this.getNotaCredito()
                .then(data => {
                    $('#freqComments').select2({
                        data: self.lDpsReasons,
                        placeholder: "Comentarios frecuentes",
                        dropdownParent: $('#modal_notaCredito_comments')

                    }).on('select2:select', function(e) {
                        self.rejection_id = e.params.data.id;
                        self.comments = e.params.data.text;
                    });

                    $('#freqComments').val('').trigger('change');

                    $('#modal_notaCredito').modal('show');
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
                        this.check_status = this.oDps.check_status;
                        this.comments = this.oDps.requester_comment_n;
                        this.reference = this.oDps.reference_string;
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
            $('#modal_notaCredito_comments').modal('show');
        },

        /**
         * Metodo para aprobar o rechazar un dps
         * @param {*} authorize 
         */
        setVoboNotaCredito(authorize){
            SGui.showWaitingUnlimit();
            let is_accept = authorize;
            let is_reject = !authorize;

            if(is_reject && (this.comments == null || this.comments == '')){
                Swal.close();
                SGui.showMessage('', 'Debe ingresar un comentario', 'warning');
                return;
            }

            let route = this.oData.setVoboNotaCreditoRoute;

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
                    this.lNotaCredito = data.lNotaCredito;
                    drawTableNotaCredito(this.lNotaCredito);
                    $('#modal_notaCredito_comments').modal('hide');
                    $('#modal_notaCredito').modal('hide');
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
            this.area_id = '';
            this.serie = '';
            this.folio = '';
            this.reference = '';
            this.modal_title = '';
            this.pdf_url = '';
            this.xml_url = '';
            this.comments = '';
            this.serieFolio = '';

            this.provider_id = $('#provider_filter').val();
            this.showProvider = true;
            this.check_status = 0;

            this.lDpsReasons = [];
            this.rejection_id = null;
            this.is_reject = 0;
        },

        change(data){
            this.clean();
            this.id_dps = data[indexesNCTable.id_dps];
            this.name_area = data[indexesNCTable.area];

            this.getNotaCredito()
                .then(data => {
                    $('#select_area').val(this.area_id).trigger('change');
                    $('#modal_change_notaCredito').modal('show');
                });
        },

        sendChange(){
            SGui.showWaitingUnlimit();

            let route = this.oData.changeAreaDpsRoute;

            axios.post(route, {
                'area_id': this.area_id,
                'provider_id': this.provider_id,
                'dps_id': this.id_dps,
            })
            .then( result => {
                let data = result.data;
                if(data.success){
                    this.lNotaCredito = data.lNotaCredito;
                    drawTableNotaCredito(this.lNotaCredito);
                    SGui.showOk();
                    $('#modal_change_notaCredito').modal('hide');
                }else{
                    SGui.showMessage('', data.message, data.icon);
                }
            })
            .catch( function(error){
                console.log(error);
                SGui.showError(error);
            });
        },

        getNotasCreditoOmision(omision){
            SGui.showWaitingUnlimit();

            let route = this.oData.getNotasCreditoOmisionRoute;

            axios.post(route, {
                'omision': omision,
            })
            .then( result => {
                let data = result.data;
                if(data.success){
                    this.is_omision = omision;
                    this.lNotaCredito = data.lNotaCredito;
                    drawTableNotaCredito(this.lNotaCredito);
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
});