var app = new Vue({
    el: '#dpsComplementaryManager',
    data: {
        oData: oServerData,
        lDpsComp: oServerData.lDpsComp,
        year: oServerData.year,
        lStatus: oServerData.lStatus,
        lTypes: oServerData.lTypes,
        lAreas: oServerData.lAreas,
        lUserAreas: oServerData.lUserAreas,
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
        selected_area: 0,

        provider_id: null,
        lProviders: oServerData.lProviders,
        showProvider: true,
        check_status: 0,

        lDpsReasons: [],
        rejection_id: null,
        comments: null,
        is_reject: 0,

        is_omision: false,

        checkAllProviders: false,
        startDate: null,
        endDate: oServerData.now,
        lProvidersToDownloadDocuments: [],
        statusToDownload: 3,
        now: oServerData.now
    },
    watch: {
        checkAllProviders: function(val){
            let elements = document.querySelectorAll('.checkbox_provider');
            for(let element of elements){
                element.checked = val;
                if (!val) {
                    self.lProvidersToDownloadDocuments = [];
                } else {
                    self.lProvidersToDownloadDocuments = [];
                    for (let provider of self.lProviders) {
                        if (provider.id == 0) {
                            continue;
                        }
                        self.lProvidersToDownloadDocuments.push(provider.id);
                    }
                }
            }
        }
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

        let arrStatusFilterDownload = [];
        for(let status of self.lStatus){
            if(status.id != 0){
                arrStatusFilterDownload.push(status);
            }
        }

        let userAreasFormatted = self.lUserAreas.map(area => ({
            id: area.id_area,
            text: area.name_area
        }));
        
        let commonAreas = self.lAreas.filter(area =>
            userAreasFormatted.some(userArea => userArea.id === area.id)
        );

        // Agregar opción "Todos" si hay 2 o más
        if (commonAreas.length >= 2) {
            commonAreas.unshift({
                id: 0,
                text: 'Todos'
            });
        }
        
        $('#area_filter').select2({
            data: commonAreas,
        }).on('select2:select', function(e) {
            const selectedId = e.params.data.id;
            self.area_id = selectedId;
            self.selected_area = selectedId;
        
            // Si seleccionan "Todos" (id = 0), manda null para traer todo
            self.getComplementsByArea(selectedId === 0 ? null : selectedId);
        });
            
        let arrAreaFilterDownload = [];
        for(let userArea of commonAreas){
            arrAreaFilterDownload.push(userArea);
        }
        
        $('#status_filter_download').select2({
            data: arrStatusFilterDownload,
        }).on('select2:select', function(e) {
            self.statusToDownload = e.params.data.id;
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

        $('#btn_download').click(function () {
            self.showModalAllProviders()
        });

        var elemDatePicker = document.getElementById("startDatePicker");
        elemDatePicker.addEventListener('changeDate', function (e, details) {
            self.startDate = this.value;
        });

        var elemDatePicker = document.getElementById("endDatePicker");
        elemDatePicker.addEventListener('changeDate', function (e, details) { 
            self.endDate = this.value;
        });
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
                'area_id': this.selected_area,
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
            this.selected_area = $('#area_filter').val();
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
        },

        getDpsComplementOmision(omision){
            SGui.showWaitingUnlimit();

            let route = this.oData.getDpsComplementOmisionRoute;

            axios.post(route, {
                'omision': omision,
            })
            .then( result => {
                let data = result.data;
                if(data.success){
                    this.is_omision = omision;
                    this.lDpsComp = data.lDpsComp;
                    drawTableDpsComplementary(this.lDpsComp);
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
        },

        showModalAllProviders(){
            this.cleanDownloadDocuments();
            let arrlProviders = [];
            let elements = [];
            for(let provider of this.lProviders){
                if (provider.id == 0) {
                    continue;
                }

                arrlProviders.push([
                    provider.id,
                    provider.text,
                    ''
                ]);

                elements.push(
                    '<div class="checkbox-wrapper-33">' +
                        '<label class="checkbox" style="width: 20%">' +
                            '<input id="prov_' + provider.id + '" class="checkbox__trigger visuallyhidden checkbox_provider" type="checkbox" ' +
                            'onclick="app.setSelectedProvidersToDownloadDocuments(' + provider.id + ')"' +
                            ' >' +
                            '<span class="checkbox__symbol">' +
                                '<svg aria-hidden="true" class="icon-checkbox" width="28px" height="28px" viewBox="0 0 28 28" version="1" xmlns="http://www.w3.org/2000/svg">' +
                                    '<path d="M4 14l8 7L24 7"></path>' +
                                '</svg>' +
                            '</span>' +
                        '</label>' +
                    '</div>'
                );
            }

            drawTable('table_allProviders', arrlProviders);
            renderInTable('table_allProviders', 1, elements);

            $('#modal_download_dps_complementary').modal('show');
        },

        setSelectedProvidersToDownloadDocuments(provider_id){
            //buscar si existe el valor
            let index = this.lProvidersToDownloadDocuments.indexOf(provider_id);

            //si existe eliminarlo del array, si no, agregarlo
            if(index == -1){
                this.lProvidersToDownloadDocuments.push(provider_id);
            }else{
                this.lProvidersToDownloadDocuments.splice(index, 1);
            }
        },

        downloadDocuments(){
            SGui.showWaiting();
            let route = this.oData.downloadDpsComplementRoute;

            axios.post(route, {
                'lProviders': this.lProvidersToDownloadDocuments,
                'startDate': this.startDate,
                'endDate': this.endDate,
                'statusToDownload': this.statusToDownload
            }, {
                responseType: 'blob'
            })
            .then((response) => {
                const blob = new Blob([response.data], { type: 'application/zip' });
                const link = document.createElement('a');
                link.href = window.URL.createObjectURL(blob);
                link.download = 'documentos.zip';
                link.click();
                SGui.showOk();
            })
            .catch(async (error) => {
                console.error(error);
            
                if (error.response && error.response.data) {
                    try {
                        // Convertir el blob de error a texto
                        const errorText = await error.response.data.text();
                        const errorJson = JSON.parse(errorText);
            
                        // Mostrar mensaje personalizado
                        if (errorJson.message) {
                            SGui.showError(errorJson.message);
                        } else {
                            SGui.showError('Ocurrió un error desconocido.');
                        }
                    } catch (e) {
                        console.error('Error al procesar la respuesta del error:', e);
                        SGui.showError('Error de conexión o respuesta inválida.');
                    }
                } else {
                    SGui.showError('No se pudo conectar al servidor.');
                }
            });
        },

        cleanDownloadDocuments(){
            this.lProvidersToDownloadDocuments = [];
            // this.startDate = null;
            // this.endDate = this.now;
            let lCheckProvider = document.getElementsByClassName('checkbox__trigger');
            for(let check of lCheckProvider){
                check.checked = false;
            }

            // elemStartDatePicker.value = null;
            // elemEndDatePicker.value = null;

            startDatepicker.setDate(null);
            triggerStartDatepickerChange();
            // endDatepicker.setDate(null);
            endDatepicker.setDate(app.now);
            triggerEndDatepickerChange();
            // elemEndDatePicker.value = app.now;
        },

        getComplementsByArea(area_id) {
            //SGui.showWaitingUnlimit();
        
            let route = this.oData.getcomplementsManagerRoute;
        
            axios.post(route, {
                provider_id: this.provider_id,
                area_id: area_id,
                year: this.year
            })
            .then(result => {
                let data = result.data;
                if (data.success) {
                    this.lDpsComp = data.lDpsComp;
                    drawTableDpsComplementary(this.lDpsComp);
                    //SGui.showOk();
                } else {
                    SGui.showMessage('', data.message, data.icon);
                }
            })
            .catch(error => {
                console.log(error);
                SGui.showError(error);
            });
        }
    }
})