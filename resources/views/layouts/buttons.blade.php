@if (isset($create))
    @if (isset($create_type))
        @switch($create_type)
            @case('principal')
                <button type="button" class="btn btn-success btn-rounded btn-icon" id="btn_create">
                    <i class="bx bx-plus"></i>
                </button>
                @break

            @case('inverse')
                <button type="button" class="btn btn-inverse-success btn-rounded btn-icon" id="btn_create_inverse">
                    <i class="bx bx-plus"></i>
                </button>
                @break
                
            @case('outline')
                <button type="button" class="btn btn-outline-success btn-rounded btn-icon" id="btn_create_outline">
                    <i class="bx bx-plus"></i>
                </button>
                @break
                
            @default
                <button type="button" class="btn btn-success btn-rounded btn-icon" id="btn_create">
                    <i class="bx bx-plus"></i>
                </button>
                @break

        @endswitch
    @else
        <button type="button" class="btn btn-success btn-rounded btn-icon" id="btn_create">
            <i class="bx bx-plus"></i>
        </button>
    @endif
@endif

@if (isset($edit))
    @if (isset($edit_type))
        @switch($edit_type)
            @case('principal')
                <button type="button" class="btn btn-warning btn-rounded btn-icon" id="btn_edit">
                    <i class="bx bxs-edit-alt"></i>
                </button>
                @break

            @case('inverse')
                <button type="button" class="btn btn-inverse-warning btn-rounded btn-icon" id="btn_edit_inverse">
                    <i class="bx bxs-edit-alt"></i>
                </button>
                @break
                
            @case('outline')
                <button type="button" class="btn btn-outline-warning btn-rounded btn-icon" id="btn_edit_outline">
                    <i class="bx bxs-edit-alt"></i>
                </button>
                @break
                
            @default
                <button type="button" class="btn btn-warning btn-rounded btn-icon" id="btn_edit">
                    <i class="bx bxs-edit-alt"></i>
                </button>
                @break

        @endswitch
    @else
        <button type="button" class="btn btn-warning btn-rounded btn-icon" id="btn_edit">
            <i class="bx bxs-edit-alt"></i>
        </button>
    @endif
@endif

@if (isset($delete))
    @if (isset($delete_type))
        @switch($delete_type)
            @case('principal')
                <button type="button" class="btn btn-danger btn-rounded btn-icon" id="btn_delete">
                    <i class="bx bx-trash"></i>
                </button>
                @break

            @case('inverse')
                <button type="button" class="btn btn-inverse-danger btn-rounded btn-icon" id="btn_delete_inverse">
                    <i class="bx bx-trash"></i>
                </button>
                @break
                
            @case('outline')
                <button type="button" class="btn btn-outline-danger btn-rounded btn-icon" id="btn_delete_outline">
                    <i class="bx bx-trash"></i>
                </button>
                @break
                
            @default
                <button type="button" class="btn btn-danger btn-rounded btn-icon" id="btn_delete">
                    <i class="bx bx-trash"></i>
                </button>
                @break

        @endswitch
    @else
        <button type="button" class="btn btn-danger btn-rounded btn-icon" id="btn_delete">
            <i class="bx bx-trash"></i>
        </button>
    @endif
@endif