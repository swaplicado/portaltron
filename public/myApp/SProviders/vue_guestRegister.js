var app = new Vue({
    el: '#registerProvider',
    data: {
        lDocs: oServerData.lDocs,
        name: null,
        shortName: null,
        rfc: null,
        email: null,
        typeInputPass: 'password',
        password: null,
        showPassword: false,
        confirmPassword: null,
        successRegister: false,
        area_id: "",
    },
    mounted(){

    },
    methods: {
        save(){
            if(!this.checkFormData()){
                return;
            }

            const formData = new FormData();

            let inputFile = null;
            for(let doc of this.lDocs){
                inputFile = document.getElementById('doc_'+doc.id_request_type_doc);
                let file = inputFile.files[0];
                formData.append('doc_'+doc.id_request_type_doc, file);
            }

            formData.append('name', this.name);
            formData.append('shortName', this.shortName);
            formData.append('rfc', this.rfc);
            formData.append('email', this.email);
            formData.append('password', this.password);
            formData.append('confirmPassword', this.confirmPassword);
            formData.append('area_id', this.area_id);

            SGui.showWaitingUnlimit();

            let route = oServerData.registerRoute;

            axios.post(route, formData, {
                headers: {
                    'Content-Type': 'multipart/form-data'
                },
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

            if(this.area_id == null){
                SGui.showMessage('', 'Debe seleccionar un área');
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