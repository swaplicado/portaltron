var app = new Vue({
    el: '#registerProvider',
    data: {
        oProvider: oServerData.oProvider,
        lDocs: oServerData.lDocs,
        name: null,
        shortName: null,
        rfc: null,
        email: null,
        typeInputPass: 'password',
        password: null,
        showPassword: false,
        confirmPassword: null,
        successUpdate: false,
        comments: null,
        area_id: null,
        arrDocs: [],
    },
    mounted(){
        this.arrDocs = Object.keys(this.lDocs).map((clave) => this.lDocs[clave]);
        this.name = this.oProvider.provider_name;
        this.shortName = this.oProvider.provider_short_name;
        this.rfc = this.oProvider.provider_rfc;
        this.email = this.oProvider.provider_email;
        this.comments = this.oProvider.comments_n;
        this.area_id = this.oProvider.area_id;
    },
    methods: {
        save(){
            if(!this.checkFormData()){
                return;
            }

            const formData = new FormData();

            let inputFile = null;
            for(let doc of this.arrDocs){
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

            let route = oServerData.updateRoute;

            axios.post(route, formData, {
                headers: {
                    'Content-Type': 'multipart/form-data'
                },
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

            if(this.area_id == null || this.area_id == ""){
                SGui.showMessage('', 'Debe seleccionar un área');
                return false;
            }

            return true;
        }
    }
})