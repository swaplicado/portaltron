var app = new Vue({
    el: '#providerAccountActivation',
    data: {
        oData: oServerData,
        rfc: oServerData.rfc,
        successRegister: false,
        name: oServerData.name,
        shortName: oServerData.shortName,
        email: oServerData.email,
        typeInputPass: 'password',
        password: null,
        showPassword: false,
        confirmPassword: null,
        fiscal_id: oServerData.fiscal_id,
        key: oServerData.key
    },
    mounted(){
    },
    methods: {
        save(){
            $('#btnSave').attr('disabled', true);
            $('#btnSave').html('<i class="fa fa-spinner fa-spin"></i> Guardando...');

            if(!this.checkFormData()){
                $('#btnSave').attr('disabled', false);
                $('#btnSave').html('Guardar');
                return;
            }
            
            SGui.showWaitingUnlimit();

            let route = oServerData.registryRoute;
            
            axios.post(route, {
                rfc: this.rfc,
                name: this.name,
                shortName: this.shortName,
                email: this.email,
                password: this.password,
                confirmPassword: this.confirmPassword,
                fiscal_id: this.fiscal_id,
                key: this.key
            })
            .then( result => {
                let data = result.data;
                if(data.success){
                    this.successRegister = data.success;
                    SGui.showOk();
                    if (data.redirect) {
                        window.location.href = data.redirect;
                    } else {
                        SGui.showMessage('', 'Cuenta creada con éxito. Por favor, inicie sesión.', 'success');
                        setTimeout(function(){
                            window.location.href = oServerData.loginRoute;
                        }, 2000);
                    }        
                }else{
                    SGui.showMessage('', data.message, data.icon);
                }
            })
            .catch( function(error){
                console.log(error);
                SGui.showError(error);
            })
            .finally(function(){
                $('#btnSave').attr('disabled', false);
                $('#btnSave').html('Guardar');
            });
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