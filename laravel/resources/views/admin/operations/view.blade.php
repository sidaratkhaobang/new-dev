@extends('admin.layouts.layout')
@section('page_title', $page_title)
@push('custom_styles')
    <style>
        .input-group-text {
            padding: 0.7rem .75rem;
            background-color: transparent;
            border-radius: 0;
            color: #6c757d;
        }

        .grey-text {
            color: #858585;
        }

        .size-text {
            font-size: 14px;
        }

        .nav-link {
            color: #343a40;
        }

        .nav-tabs-alt .nav-link.active,
        .nav-tabs-alt .nav-item.show .nav-link {
            color: #0665d0;
        }

        .fit-image{
            object-fit: contain;
        }

        .block-car .item {
    max-width: 7rem;
    height: inherit;
    align-items: flex-start;
    flex-direction: column;
    justify-content:center;
}
    </style>
@endpush
@section('content')

    <div class="block block-rounded">
        <div class="row">
            <div class="col-sm-12">
                <ul class="nav nav-tabs nav-tabs-alt" role="tablist">
                    <li class="nav-item">
                        <button class="nav-link " id="btabs-alt-static-info-tab" data-bs-toggle="tab"
                            data-bs-target="#btabs-alt-static-info" role="tab" aria-controls="btabs-alt-static-info"
                            aria-selected="true">รายละเอียดข้อมูล</button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link active" id="btabs-alt-static-form-tab" data-bs-toggle="tab"
                            data-bs-target="#btabs-alt-static-form" role="tab" aria-controls="btabs-alt-static-form"
                            aria-selected="false">Operation</button>
                    </li>
                </ul>
                <form id="save-form">
                    <div class="block-content tab-content">
                        <div class="tab-pane " id="btabs-alt-static-info" role="tabpanel"
                            aria-labelledby="btabs-alt-static-info-tab">
                            @include('admin.operations.sections.view')
                        </div>
                        <div class="tab-pane active" id="btabs-alt-static-form" role="tabpanel"
                            aria-labelledby="btabs-alt-static-form-tab">
                            @include('admin.operations.sections.form')
                        </div>
                    </div>
                    <x-forms.submit-group :optionals="['url' => 'admin.operations.index', 'view' => empty($view) ? null : $view]" />
                </form>
            </div>
        </div>
    </div>



@endsection
@include('admin.components.select2-default')
@include('admin.components.sweetalert')
@include('admin.components.list-delete')
@include('admin.components.date-input-script')
@include('admin.components.upload-image-scripts')
@include('admin.components.upload-image', [
    'id' => 'contract_file',
    'max_files' => 1,
    'accepted_files' => '.jpg,.jpeg,.png,.pdf',
    'mock_files' => $contract_file,
])

@include('admin.components.upload-image-scripts')
@include('admin.components.upload-image', [
    'id' => 'receipt_file',
    'max_files' => 1,
    'accepted_files' => '.jpg,.jpeg,.png,.pdf',
    'mock_files' => $receipt_file,
])
@include('admin.components.form-save', [
    'store_uri' => route('admin.operations.store'),
])
