@extends('admin.layouts.layout')

@section('page_title', $page_title)

@push('styles')
<style>
    .img-fluid {
        width: 250px;
        height: 100px;
        object-fit: cover;
    }

    .car-border {
        border: 1px solid #CBD4E1;
        width: 300px;
        border-radius: 6px;
        color: #475569;
        padding: 2rem;
        height: fit-content;
    }

    .hide {
        display: none !important;
    }

    .show {
        display: block !important;
        opacity: 1;
        animation: fade 1s;
    }

    @keyframes fade {
        0% {
            opacity: 0;
        }

        100% {
            opacity: 1;
        }
    }
</style>
@endpush

@section('content')
<form id="save-form">
    <div class="block {{ __('block.styles') }}">
        <div class="block-content">
            @include('admin.replacement-car-informs.sections.info')
            @include('admin.replacement-car-informs.sections.short-replacement-car')
            @if (isset($view))
                @include('admin.replacement-car-informs.sections.date-place')
            @endif
            <x-forms.hidden id="id" name="id"
                :value="$d->id" />
                @if ($mode == MODE_CREATE)
                <x-forms.submit-group :optionals="[
                    'url' => 'admin.replacement-car-informs.index',
                    'view' => isset($view) ? true : null,
                    'manage_permission' => Actions::Manage . '_' . Resources::ReplacementCarInform
                ]" />
                @endif
        </div>
    </div>
    @if (isset($d->main_car_id))
    @include('admin.replacement-car-informs.sections.car-detail',
    [
    'car_type' => 'main',
    'car' => $main_car,
    ])
    @include('admin.replacement-car-informs.modals.accident-history',
    [
    'car_type' => 'main'
    ])
    @include('admin.replacement-car-informs.modals.repair-history',
    [
    'car_type' => 'main'
    ])
    @include('admin.replacement-car-informs.modals.accessory',
    [
    'car_type' => 'main'
    ])
    @include('admin.replacement-car-informs.modals.condition',
    [
    'car_type' => 'main'
    ])
    @endif

    @if (isset($d->replacement_car_id))
    @include('admin.replacement-car-informs.sections.car-detail',
    [
    'car_type' => 'replace',
    'car' => $replacement_car,
    ])
    @include('admin.replacement-car-informs.modals.accident-history',
    [
    'car_type' => 'replace'
    ])
    @include('admin.replacement-car-informs.modals.repair-history',
    [
    'car_type' => 'replace'
    ])
    @include('admin.replacement-car-informs.modals.accessory',
    [
    'car_type' => 'replace'
    ])
    @include('admin.replacement-car-informs.modals.condition',
    [
    'car_type' => 'replace'
    ])
    @endif

    @if ($mode != MODE_CREATE)
    <div class="block {{ __('block.styles') }}">
        <div class="block-content">
            <x-forms.hidden id="id" name="id" :value="$d->id" />
            <x-forms.submit-group :optionals="[
                'url' => 'admin.replacement-car-informs.index',
                'view' => isset($view) ? true : null,
                'manage_permission' => Actions::Manage . '_' . Resources::ReplacementCarInform
            ]" />
        </div>
    </div>
    @endif

</form>
@endsection

@include('admin.components.select2-default')
@include('admin.components.sweetalert')
@include('admin.components.list-delete')

@include('admin.components.date-input-script')
@include('admin.components.form-save', [
'store_uri' => $route_uri,
])

@include('admin.components.select2-ajax', [
'id' => 'creditor_id_field',
'modal' => '#modal-purchase-order-dealer',
'url' => route('admin.util.select2.dealers'),
])

@include('admin.components.select2-ajax', [
'id' => 'job_id',
'parent_id' => 'job_type',
'parent_id_2' => 'replacement_type',
'url' =>
route('admin.util.select2-replacement-car.jobs'),
])


@include('admin.components.upload-image-scripts')
@include('admin.components.upload-image', [
'id' => 'documents',
'max_files' => 10,
'accepted_files' => '.jpg,.jpeg,.bmp,.png,.pdf',
'mock_files' => $replacement_car_files,
'show_url' => true,
'view_only' => isset($view) ? true : null
])

@include('admin.replacement-car-informs.scripts.replacement-script')
@include('admin.replacement-car-informs.scripts.replacement-type-script')

@push('scripts')
<script>
    $('#worksheet_no_field').prop('disabled', true);
        $('#creator').prop('disabled', true);
        $('#contract_no').prop('disabled', true);
        $('#main_license_plate').prop('disabled', true);
        $('#spec_low_reason').prop('disabled', true);
        $('#is_spec_low_0').prop('disabled', true);

        // const view = @if (isset($view)) @json($view) @else false @endif;
        const mode = @if (isset($mode)) @json($mode) @else false @endif;
        if (mode) {
            if (mode === 'MODE_UPDATE') {
                $('#replacement_type').prop('disabled', true);
                $('#job_type').prop('disabled', true);
                $('#job_id').prop('disabled', true);
            }
            if (mode === 'MODE_VIEW') {
                $('#replacement_type').prop('disabled', true);
                $('#job_type').prop('disabled', true);
                $('#job_id').prop('disabled', true);
                $('#replacement_expect_date').prop('disabled', true);
                $('#replacement_expect_place').prop('disabled', true);
                $('#customer_name').prop('disabled', true);
                $('#tel').prop('disabled', true);
                $('#remark').prop('disabled', true);
                $('#replacement_place').prop('disabled', true);
                $('#replacement_date').prop('disabled', true);
                $('input[name="is_need_driver"]').prop('disabled', true);
                $('input[name="is_need_slide"]').prop('disabled', true);

                $('#spec_low_reason').prop('disabled', true);
                $('#is_spec_low_0').prop('disabled', true);
                $('#replacement_car_id').prop('disabled', true);
            }
        }

        function openAccessoryModal(car_type) {
            $('#' + car_type + '-accessory-modal').modal('show');
        }

        function openAccidentModal(car_type) {
            $('#' + car_type + '-accident-history-modal').modal('show');
        }

        function openRepairModal(car_type) {
            $('#' + car_type + '-repair-history-modal').modal('show');
        }

        function openConditionModal(car_type) {
            $('#' + car_type + '-condition-modal').modal('show');
        }
</script>
@endpush
