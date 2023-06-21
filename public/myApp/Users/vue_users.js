var app = new Vue({
    el: '#users',
    data: {
        oData: oServerData,
        constants: oServerData.constants,
        lUsers: oServerData.lUsers,
        lProviders: oServerData.lProviders,
        lRoles: oServerData.lRoles,
        modal_title: null,
        last_name1: null,
        last_name2: null,
        names: null,
        email: null,
        id_user: null,
        provider_id: null,
        rol_id: null,
        is_edit: false
    },
    updated(){
        this.$nextTick(function () {
            if (this.$el.querySelector('#select_providers')) {
                $('#select_providers').select2({
                    placeholder: 'Selecciona proveedor',
                    data: self.lProviders,
                }).on('select2:select', function(e) {
                    self.provider_id = e.params.data.id;
                });

                $('#select_providers').val('').trigger('change');

            }else{
                self.provider_id = null;
            }
        })
    },
    mounted(){
        self = this;

        $('.select2-class-modal').select2({
            dropdownParent: $('#modal_users_form')
        });

        $('#roles').select2({
            placeholder: 'Selecciona rol',
            data: self.lRoles,
        }).on('select2:select', function(e) {
            self.rol_id = e.params.data.id;
        });
    },
    methods: {
        createModal(){
            if ($('#select_providers') != 'undefined') {
                $('#select_providers').val('').trigger('change');
            }
            $('#roles').val('').trigger('change');
            this.is_edit = false;
            this.modal_title = 'Nuevo usuario',
            this.id_user = null;
            this.rol_id = null;
            this.provider_id = null;
            this.last_name1 = null;
            this.last_name2 = null;
            this.names = null;
            this.email = null;
            this.id_user = null;
            this.names = null;
            this.email = null;
            $('#modal_users_form').modal('show');
        },

        editModal(data){
            this.is_edit = true;
            this.modal_title = 'Usuario: ' + data[indexesUsersTable.full_name];
            this.last_name1 = data[indexesUsersTable.last_name1];
            this.last_name2 = data[indexesUsersTable.last_name2];
            this.names = data[indexesUsersTable.names];
            this.email = data[indexesUsersTable.email];
            this.id_user = data[indexesUsersTable.id_user];
            $('#modal_users_form').modal('show');
        },

        saveUser(){
            SGui.showWaiting(15000);

            var route = null;
            if(this.is_edit){
                route = this.oData.updateRoute;
            }else{
                route = this.oData.createRoute;
            }

            axios.post(route, {
                'id_user': this.id_user,
                'rol_id': this.rol_id,
                'provider_id': this.provider_id,
                'last_name1': this.last_name1,
                'last_name2': this.last_name2,
                'names': this.names,
                'email': this.email,
            })
            .then( result => {
                let data = result.data;
                if(data.success){
                    drawTable('table_users', data.lUsers);
                    $('#modal_users_form').modal('hide');
                    SGui.showOk();
                }else{
                    SGui.showMessage('', data.message, data.icon);
                }
            })
            .catch( function(error){
                console.log(error);
                SGui.showMessage('', error, 'error');
            });
        },

        deleteRegistry(data){
            Swal.fire({
                title: '¿Desea eliminar al usuario ' + data[indexesUsersTable.full_name] + '?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Aceptar'
            }).then((result) => {
                if (result.isConfirmed) {
                    this.deleteUser(data[indexesUsersTable.id_user]);
                }
            })
        },

        deleteUser(id_user){
            SGui.showWaiting(15000);

            var route = this.oData.deleteRoute;

            axios.post(route, {
                'is_user': id_user,
            })
            .then( result => {
                let data = result.data;
                if(data.success){
                    drawTable('table_users', data.lUsers);
                    SGui.showOk();
                }else{
                    SGui.showMessage('', data.message, data.icon);
                }
            })
            .catch( function(error){
                console.log(error);
                SGui.showMessage('', error, 'error');
            });
        }
    }
})