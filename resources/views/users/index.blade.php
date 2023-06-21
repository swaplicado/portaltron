@extends('layouts.principal')

@section('headStyles')
    <link href={{asset('select2js/css/select2.min.css')}} rel="stylesheet" />
@endsection

@section('headJs')
<script>
    function GlobalData(){
        this.lUsers = <?php echo json_encode($lUsers); ?>;
        this.lProviders = <?php echo json_encode($lProviders); ?>;
        this.lRoles = <?php echo json_encode($lRoles); ?>;
        this.createRoute = <?php echo json_encode(route('users_create')); ?>;
        this.updateRoute = <?php echo json_encode(route('users_update')); ?>;
        this.deleteRoute = <?php echo json_encode(route('users_delete')); ?>;
        this.constants = <?php echo json_encode($constants); ?>;
    }
    var oServerData = new GlobalData();
    var indexesUsersTable = {
                'id_user': 0,
                'rol_id': 1,
                'provider_id': 2,
                'names': 3,
                'last_name1': 4,
                'last_name2': 5,
                'username': 6,
                'email': 7,
                'full_name': 8,
                'rol': 9,
                'provider_name': 10,
                'created_by': 11,
                'updated_by': 12,
                'created': 13,
                'updated': 14,
            };
</script>
@endsection

@section('content')
<div class="card" id="users">
    <div class="card-header">
        <h3>Usuarios</h3>
    </div>
    <div class="card-body">

        @include('users.modal_users_form')

        <div class="grid-margin">
            @include('layouts.buttons', ['create' => true, 'edit' => true, 'delete' => true])
        </div>

        <div class="table-responsive">
            <table class="display expandable-table dataTable no-footer" id="table_users" width="100%" cellspacing="0">
                <thead>
                    <th>id_user</th>
                    <th>rol_id</th>
                    <th>provider_id</th>
                    <th>names</th>
                    <th>last_name1</th>
                    <th>last_name2</th>
                    <th>Usuario</th>
                    <th>Email</th>
                    <th>Nombre</th>
                    <th>Rol</th>
                    <th>Proveedor</th>
                    <th>Creado por</th>
                    <th>Actualizado por</th>
                    <th>Fecha creación</th>
                    <th>Fecha actualización</th>
                </thead>
                <tbody>
                    
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@section('scripts')
    <script>
        var self;
    </script>
    @include('layouts.table_jsControll', [
                                            'table_id' => 'table_users',
                                            'colTargets' => [0,1,2,3,4,5],
                                            'colTargetsSercheable' => [],
                                            'select' => true,
                                            'create_modal' => true,
                                            'edit_modal' => true,
                                            'delete' => true,
                                        ] )

    <script src="{{asset('varios/select2/select2.min.js')}}"></script>
    <script type="text/javascript" src="{{ asset('myApp/Users/vue_users.js') }}"></script>
    <script type="text/javascript" src="{{ asset('myApp/Utils/datatablesUtils.js') }}"></script>
    <script type="text/javascript">
        $(document).ready(function() {
            drawTable('table_users', oServerData.lUsers);
        })
    </script>

@endsection