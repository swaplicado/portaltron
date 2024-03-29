@extends('layouts.principal')

@section('headStyles')
    <link href="{{ asset('myApp/Utils/SDatePicker/css/datepicker.min.css') }}" rel="stylesheet" />
@endsection

@section('headJs')
<script>
    function GlobalData(){
        this.lAccountState = <?php echo json_encode($lAccountState) ?>;
        this.sMonth = <?php echo json_encode($sMonth) ?>;
        this.idProvider = <?php echo json_encode($idProvider) ?>;
        this.updateAccountState = <?php echo json_encode(route('accountStates.updateAccount')) ?>;
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
  
<div class="card" id="accountState">
    <div class="card-header">
        <h3>Estados de cuenta</h3>
    </div>
    <div class="card-body">

        <div class="grid-margin">
            <span class="nobreak">
                <label for="filter_month">Fecha de corte: </label>
                <select class="select2-class form-control" name="filter_month" id="filter_month"></select>
            </span>    
        </div>

        <div class="table-responsive">
            <table class="display expandable-table dataTable no-footer" id="table_account_state" width="100%" cellspacing="0">
                <thead>
                    <th>id_year</th>
                    <th style="text-align: center">Fecha</th>
                    <th style="text-align: center">Concepto</th>
                    <th style="text-align: center">Importe ME</th>
                    <th style="text-align: center">Tipo de cambio</th>
                    <th style="text-align: center">Debe</th>
                    <th style="text-align: center">Haber</th>
                    <th style="text-align: center">Saldo</th>
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
                                                'lengthMenu' => [
                                                    [ -1 , 10, 25, 50, 100],
                                                    [ 'Mostrar todo','Mostrar 10', 'Mostrar 25', 'Mostrar 50', 'Mostrar 100' ]
                                                ],
                                                'colTargetsAlignRight' =>[3,4,5,6,7],
                                                'colTargetsAlignCenter' =>[1,2],
                                            ] )
    @endif

    <script type="text/javascript" src="{{ asset('myApp/Utils/datatablesUtils.js') }}"></script>
    <script type="text/javascript" src="{{ asset('myApp/AccountStates/vue_accountStates.js') }}"></script>
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
                        as.dateFormat,
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

        $(document).ready(function() {
            drawTableAccountStates(oServerData.lAccountState);
        })
    </script>
@endsection