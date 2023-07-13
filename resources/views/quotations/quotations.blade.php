@extends('layouts.principal')

@section('headJs')
<script>
    function GlobalData(){
        this.lQuotations = @json($lQuotations);
        this.uploadQuotationRoute = @json(route('quotations.uploadQuotation'));
        this.showQuotationRoute = @json(route('quotations.showQuotation'));
        this.updateQuotationRoute = @json(route('quotations.updateQuotation'));
        this.deleteRoute = @json(route('quotations.delete'));
    }
    var oServerData = new GlobalData();
    var indexes = {
                'id_quotation': 0,
                'provider_id': 1,
                'pdf_original_name': 2,
                'folio_system': 3,
                'folio_user': 4,
                'description': 5,
            };
</script>
@endsection

@section('content')
<div class="card" id="quotationsapp">
    <div class="card-header">
        <h3>Cotizaciones</h3>
    </div>
    <div class="card-body">

        @include('quotations.modal_quotation')

        <div class="grid-margin">
            @include('layouts.buttons', ['create' => true, 'edit' => true, 'delete' => true])
            <button type="button" class="btn btn-secondary btn-rounded btn-icon" id="btn_pdf" v-on:click="showQuotation();">
                <i class="bx bxs-file-pdf"></i>
            </button>
        </div>

        <div class="table-responsive">
            <table class="display expandable-table dataTable no-footer" id="table_quotations" width="100%" cellspacing="0">
                <thead>
                    <th>id_quotation</th>
                    <th>provider_id</th>
                    <th>pdf_original_name</th>
                    <th>Cotizacón</th>
                    <th>Folio</th>
                    <th>Descripción</th>
                </thead>
                <tbody>
                    
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@section('scripts')
    @include('layouts.table_jsControll', [
                                            'table_id' => 'table_quotations',
                                            'colTargets' => [0, 1, 2],
                                            'colTargetsSercheable' => [],
                                            'select' => true,
                                            'create_modal' => true,
                                            'edit_modal' => true,
                                            'delete' => true,
                                        ] )
    <script type="text/javascript" src="{{ asset('myApp/Quotations/vue_quotations.js') }}"></script>
    <script type="text/javascript" src="{{ asset('myApp/Utils/datatablesUtils.js') }}"></script>
    <script type="text/javascript">
        $(document).ready(function() {
            drawTable('table_quotations', oServerData.lQuotations);
        })
    </script>
    <script>
        const dropContainer = document.getElementById("dropcontainer")
        const fileInput = document.getElementById("pdf")

        dropContainer.addEventListener("dragover", (e) => {
            // prevent default to allow drop
            e.preventDefault()
        }, false)

        dropContainer.addEventListener("dragenter", () => {
            dropContainer.classList.add("drag-active")
        })

        dropContainer.addEventListener("dragleave", () => {
            dropContainer.classList.remove("drag-active")
        })

        dropContainer.addEventListener("drop", (e) => {
            e.preventDefault()
            dropContainer.classList.remove("drag-active")
            fileInput.files = e.dataTransfer.files
        })
    </script>
@endsection