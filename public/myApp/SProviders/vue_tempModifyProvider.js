var app = new Vue({
    el: '#registerProvider',
    data: {
        oProvider: oServerData.oProvider,
        name: null,
        shortName: null,
        rfc: null,
        email: null,
        typeInputPass: 'password',
        password: null,
        showPassword: false,
        confirmPassword: null,
        successUpdate: false,
    },
    mounted(){
        this.name = this.oProvider.provider_name;
        this.shortName = this.oProvider.provider_short_name;
        this.rfc = this.oProvider.provider_rfc;
        this.email = this.oProvider.provider_email;
    },
    methods: {
        save(){
            if(!this.checkFormData()){
                return;
            }

            SGui.showWaitingUnlimit();

            let route = oServerData.updateRoute;
            axios.post(route, {
                'name': this.name,
                'shortName': this.shortName,
                'rfc': this.rfc,
                'email': this.email,
            })
            .then( result => {
                let data = result.data;
                if(data.success){
                    this.successUpdate = data.success;
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

        checkFormData(){
            if(this.name == null || this.name == ''){
                SGui.showMessage('', 'Debe introducir su razón social');
                return false;
            }

            if(this.shortName == null || this.shortName == ''){
                SGui.showMessage('', 'Debe introducir su nombre comercial');
                return false;
            }

            if(this.rfc == null || this.rfc == ''){
                SGui.showMessage('', 'Debe introducir su RFC');
                return false;
            }

            if(this.rfc.length < 12){
                SGui.showMessage('', 'Debe introducir un RFC valido');
                return false;
            }

            if(this.email == null || this.email == ''){
                SGui.showMessage('', 'Debe introducir su Email');
                return false;
            }

            return true;
        }
    }
})