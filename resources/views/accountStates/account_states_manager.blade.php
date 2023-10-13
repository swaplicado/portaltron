@extends('layouts.principal')

@section('headStyles')
    <link href="{{ asset('myApp/Utils/SDatePicker/css/datepicker.min.css') }}" rel="stylesheet" />
@endsection

@section('headJs')
<script>
    function GlobalData(){
        this.sMonth = <?php echo json_encode($sMonth) ?>;
        this.lProviders = <?php echo json_encode($lProviders) ?>;
        this.updateAccountState = <?php echo json_encode(route('accountStates.updateAccountManager')) ?>;
        this.withoutProvider = <?php echo json_encode($withoutProvider) ?>
    }
    var oServerData = new GlobalData();
    var indexesAccountStateTable = {
            'idYear': 0,
            'date': 1,
            'concept': 2,
            'import_me': 3,
            'exc_rate': 4,
            'debit': 5,
            'credit': 6,
        };

</script>
@endsection

@section('content')
  
<div class="card" id="accountStateManager">
    <div class="card-header">
        <h3>Estados de cuenta</h3>
    </div>
    <div class="card-body">

        <div class="grid-margin">
            
            <span v-show ="a" class="nobreak">
                <label for="filter_month">Fecha de corte: </label>
                <select class="select2-class form-control" name="filter_month" id="filter_month"></select>
            </span> 
            <span class="nobreak">
                <label for="filter_provider">Proveedores: </label>
                <select class="select2-class form-control" name="filter_provider" id="filter_provider"></select>
            </span>     
        </div>

        <div class="table-responsive">
            <table class="display expandable-table dataTable no-footer" id="table_account_state" width="100%" cellspacing="0">
                <thead>
                    <th>id_year</th>
                    <th>Fecha</th>
                    <th>Concepto</th>
                    <th>Importe ME</th>
                    <th>Tipo de cambio</th>
                    <th>Debe</th>
                    <th>Haber</th>
                    <th>Saldo</th>
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
        moment.locale('es');
        
    </script>
    @if ( isset($lAccountState))
        @if  ( count($lAccountState) > 1 )
            @if ( $lAccountState[1]->excRate != 1 )
                @include('layouts.table_jsControll', [
                    'table_id' => 'table_account_state',
                    'colTargets' => [0],
                    'colTargetsSercheable' => [],
                    'noSort' => true,
                    'noSearch' => true, 
                    'lengthMenu' => [
                        [ -1 , 10, 25, 50, 100],
                        [ 'Mostrar todo','Mostrar 10', 'Mostrar 25', 'Mostrar 50', 'Mostrar 100' ]
                    ],
                    'colTargetsAlignRight' =>[3,4,5,6,7],
                    'colTargetsAlignCenter' =>[1,2],
                ] )
            @else

                @include('layouts.table_jsControll', [
                    'table_id' => 'table_account_state',
                    'colTargets' => [0,3,4],
                    'colTargetsSercheable' => [],
                    'noSort'=> true,
                    'noSearch' => true,
                    'lengthMenu' => [
                        [ -1 , 10, 25, 50, 100],
                        [ 'Mostrar todo','Mostrar 10', 'Mostrar 25', 'Mostrar 50', 'Mostrar 100' ]
                    ],
                    'colTargetsAlignRight' =>[3,4,5,6,7],
                    'colTargetsAlignCenter' =>[1,2],
                ] )
            @endif

        @else
            @include('layouts.table_jsControll', [
                                                    'table_id' => 'table_account_state',
                                                    'colTargets' => [0,3,4],
                                                    'colTargetsSercheable' => [],
                                                    'noSort'=> true,
                                                    'noSearch' => true,
                                                    'lengthMenu' => [
                                                        [ -1 , 10, 25, 50, 100],
                                                        [ 'Mostrar todo','Mostrar 10', 'Mostrar 25', 'Mostrar 50', 'Mostrar 100' ]
                                                    ],
                                                    'colTargetsAlignRight' =>[3,4,5,6,7],
                                                    'colTargetsAlignCenter' =>[1,2],
                                                ] )
        @endif
    @else
        @include('layouts.table_jsControll', [
            'table_id' => 'table_account_state',
            'colTargets' => [0,3,4],
            'colTargetsSercheable' => [],
            'noSort'=> true,
            'noSearch' => true,
            'lengthMenu' => [
                [ -1 , 10, 25, 50, 100],
                [ 'Mostrar todo','Mostrar 10', 'Mostrar 25', 'Mostrar 50', 'Mostrar 100' ]
            ],
            'colTargetsAlignRight' =>[3,4,5,6,7],
            'colTargetsAlignCenter' =>[1,2],
        ] )
    @endif
    <script type="text/javascript" src="{{ asset('myApp/Utils/datatablesUtils.js') }}"></script>
    <script type="text/javascript" src="{{ asset('myApp/AccountStates/vue_accountStatesManager.js') }}"></script>
    <script src="{{ asset('myApp/Utils/SDatePicker/js/datepicker-full.min.js') }}"></script>

    <script type="text/javascript">
        function drawTableAccountStates(lAccountState){
            var arrAS = [];
            var saldo = 0;
            for (let as of lAccountState) {
                saldo = saldo - as.debit;
                saldo = saldo + as.credit; 
                arrAS.push(
                    [
                        as.idYear,
                        as.date,
                        as.concept,
                        as.importForeignCurrency.toLocaleString('es-MX', { minimumFractionDigits: 2, maximumFractionDigits: 2 })+ ' ' +as.CurrencyCode,
                        as.excRate.toLocaleString('es-MX', { minimumFractionDigits: 2, maximumFractionDigits: 2 }),
                        as.debit.toLocaleString('es-MX', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) + ' MXN',
                        as.credit.toLocaleString('es-MX', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) + ' MXN',
                        saldo.toLocaleString('es-MX', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) + ' MXN'
                    ]
                )
            }
            arrAS.push(
                [
                    "999999 ",
                    " ",
                    "SALDO FINAL",
                    " ",
                    " ",
                    " ",
                    " ",
                    saldo.toLocaleString('es-MX', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) + ' MXN'
                ]
            )
            drawTable('table_account_state', arrAS);
        };

    </script>
@endsection