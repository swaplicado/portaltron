var app = new Vue({
    el: '#validateRfc',
    data: {
        rfc: null,
        sendMail: false
    },
    mounted(){
    },
    methods: {
        save(){
            $('#btnSave').attr('disabled', true);
            $('#btnSave').html('<i class="fa fa-spinner fa-spin"></i> Validando...');

            if(!this.checkFormData()){
                $('#btnSave').attr('disabled', false);
                $('#btnSave').html('Continuar');
                return;
            }
            
            SGui.showWaitingUnlimit();

            let route = oServerData.checkProviderToRegister;

            axios.post(route, {
                rfc: this.rfc
            })
            .then( result => {
                let data = result.data;
                if(data.success){
                    // SGui.showOk();
                    this.sendMail = true
                    SGui.showOk();
                }else{
                    if (data.route != undefined && data.route != '') {
                        window.location.href = data.route;
                    } else {
                        SGui.showMessage('', data.message, data.icon);
                    }
                }
            })
            .catch( function(error){
                console.log(error);
                SGui.showError(error);
            })
            .finally(function(){
                $('#btnSave').attr('disabled', false);
                $('#btnSave').html('Continuar');
            });
        },

        checkFormData(){
            if(this.rfc == null || this.rfc == ''){
                SGui.showMessage('', 'Debe introducir su RFC');
                return false;
            }

            if(this.rfc.length < 12){
                SGui.showMessage('', 'Debe introducir un RFC valido');
                return false;
            }

            return true;
        }
    }
})