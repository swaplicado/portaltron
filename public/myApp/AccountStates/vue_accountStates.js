var app = new Vue({
    el: '#accountState',
    data: {
        oData: oServerData,
        lAccountState: oServerData.lAccountState,
        sMonth: oServerData.sMonth,
        filter_month_id: 0,
        idProvider: oServerData.idProvider,
    },
    mounted() {
        self = this;
        let dataSelect = [];
        for (let i = 0; i < this.sMonth.length; i++) {
            dataSelect.push({ id: this.sMonth[i].index_calendar, text: this.sMonth[i].name_calendar });
        }

        $('#filter_month').select2({
            data: dataSelect,
        }).on('select2:select', function(e) {
            self.filter_month_id = e.params.data.id;
            self.changeMonth();
        });

        $('#filter_month').val('0').trigger('change');

    },
    methods: {
        changeMonth() {
            SGui.showWaitingUnlimit();
            let route = this.oData.updateAccountState;
            axios.post(route, {
                    'sMonths': this.sMonth,
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
    }
});