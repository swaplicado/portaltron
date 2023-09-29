var app = new Vue({
    el: '#registerProvider',
    data: {
        name: null,
        shortName: null,
        rfc: null,
        email: null,
        typeInputPass: 'password',
        password: null,
        showPassword: false,
        confirmPassword: null,
        successRegister: false,
    },
    mounted(){

    },
    methods: {
        save(){
            if(!this.checkFormData()){
                return;
            }

            SGui.showWaitingUnlimit();

            let route = oServerData.registerRoute;
            axios.post(route, {
                'name': this.name,
                'shortName': this.shortName,
                'rfc': this.rfc,
                'email': this.email,
                'password': this.password,
                'confirmPassword': this.confirmPassword,
            })
            .then( result => {
                let data = result.data;
                if(data.success){
                    this.successRegister = data.success;
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

            if(this.password == null || this.password == ''){
                SGui.showMessage('', 'Debe introducir una contraseña de al menos 8 caracteres');
                return false;
            }

            if(this.password.length < 8){
                SGui.showMessage('', 'La contraseña debe contener al menos 8 caracteres');
                return false;
            }

            if(this.confirmPassword == null || this.confirmPassword == ''){
                SGui.showMessage('', 'Debe introducir la confirmación de la contraseña');
                return false;
            }

            if(this.password != this.confirmPassword){
                SGui.showMessage('', 'La contraseña y la confirmación de la contraseña deben ser iguales');
                return false;
            }

            return true;
        },

        showPass(){
            this.showPassword = this.showPassword ? false : true;
            this.typeInputPass = this.showPassword ? "text" : "password";
        },
    }
})