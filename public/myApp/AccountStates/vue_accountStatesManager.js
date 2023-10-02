var app = new Vue({
    el: '#accountStateManager',
    data: {
        oData: oServerData,
        lAccountState: 0,
        sMonth: oServerData.sMonth,
        filter_month_id: 0,
        filter_provider_id: 0,
        lProviders: oServerData.lProviders,
        withoutProvider: oServerData.withoutProvider,
        a: false,
    },
    mounted() {
        self = this;
        let dataSelect = [];
        let dataProviders = [];
        for (let i = 0; i < this.sMonth.length; i++) {
            dataSelect.push({ id: this.sMonth[i].index_calendar, text: this.sMonth[i].name_calendar });
        }
        for (let i = 0; i < this.lProviders.length; i++) {
            dataProviders.push({ id: this.lProviders[i].ext_id, text: this.lProviders[i].provider_name });
        }

        $('#filter_month').select2({
            data: dataSelect,
        }).on('select2:select', function(e) {
            self.filter_month_id = e.params.data.id;
            self.changeMonth();
        });

        $('#filter_month').val('0').trigger('change');

        $('#filter_provider').select2({
            placeholder: 'selecciona proveedor',
            data: dataProviders,
        }).on('select2:select', function(e) {
            self.filter_provider_id = e.params.data.id;
            self.changeProvider();
        });
        $('#filter_provider').val('').trigger('change');

    },
    methods: {
        changeMonth() {
            SGui.showWaitingUnlimit();
            let route = this.oData.updateAccountState;
            axios.post(route, {
                    'sMonths': this.sMonth,
                    'filter_provider_id': this.filter_provider_id,
                    'filter_month_id': this.filter_month_id,
                })
                .then(result => {
                    let data = result.data;
                    if (data.success) {
                        drawTableAccountStates(data.lAccountState);
                        SGui.showOk();
                    } else {
                        SGui.showMessage('', data.message, data.icon);
                    }
                })
                .catch(function(error) {
                    console.log(error);
                    SGui.showError(error);
                });
        },

        changeProvider() {
            SGui.showWaitingUnlimit();
            let route = this.oData.updateAccountState;
            axios.post(route, {
                    'sMonths': this.sMonth,
                    'lProviders': this.lProviders,
                    'filter_provider_id': this.filter_provider_id,
                    'filter_month_id': this.filter_month_id,
                })
                .then(result => {
                    let data = result.data;
                    this.a = true;
                    if (data.success) {
                        drawTableAccountStates(data.lAccountState);
                        SGui.showOk();
                    } else {
                        SGui.showMessage('', data.message, data.icon);
                    }
                })
                .catch(function(error) {
                    console.log(error);
                    SGui.showError(error);
                });
        }
    }
});