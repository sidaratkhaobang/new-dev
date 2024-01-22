@extends('admin.layouts.layout')

@section('page_title', $page_title)
@section('history')
    @include('admin.components.btns.history')
@endsection
@push('styles')
    <style>
        .img-fluid {
            width: 250px;
            height: 100px;
            object-fit: cover;
            /* display: block; */
            /* margin: auto; */
        }

        .car-border {
            border: 1px solid #CBD4E1;
            border-radius: 6px;
            color: #475569;
            padding: 2rem;
            height: fit-content;
            width: 300px;
        }

        .car-section {
            text-align: center;
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

        /* .form-progress-bar {
                                        color: #888888;
                                        padding: 30px;
                                    } */

        .form-progress-bar .form-progress-bar-header {
            text-align: left;

        }

        .form-progress-bar .form-progress-bar-steps {
            margin: 30px 0 10px 0;
            /* display: flex;
                                justify-content: center;
                                align-items: center; */
        }

        div.check-status {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100%;
            flex-direction: column;
        }

        .form-progress-bar .form-progress-bar-steps li,
        .form-progress-bar .form-progress-bar-labels li {
            width: 16.6%;
            float: left;
            position: relative;
        }

        .form-progress-bar-line {
            background-color: #f3f3f3;
            content: "";
            height: 2px;
            left: 0;
            /* position: absolute; */
            right: 0;
            top: 50%;
            transform: translateY(-50%);
            /* width: 70%; */
            border-bottom: 1px solid #dddddd;
            border-top: 1px solid #dddddd;
            margin-left: 20px;
            margin-right: 30px;
        }

        .form-progress-bar .form-progress-bar-steps span.check {
            background-color: #dddddd;
            border-radius: 50%;
            display: inline-block;
            height: 40px;
            line-height: 40px;
            position: relative;
            text-align: center;
            width: 40px;
        }

        .form-progress-bar .form-progress-bar-steps span.pending {
            background-color: #dddddd;
            border-radius: 50%;
            display: inline-block;
            height: 40px;
            line-height: 40px;
            position: relative;
            text-align: center;
            width: 40px;
        }

        .form-progress-bar .form-progress-bar-steps span.pending-secondary {
            background-color: #dddddd;
            border-radius: 50%;
            display: inline-block;
            height: 40px;
            line-height: 40px;
            position: relative;
            text-align: center;
            width: 40px;
        }

        .form-progress-bar .form-progress-bar-steps span.reject {
            background-color: #dddddd;
            border-radius: 50%;
            display: inline-block;
            height: 40px;
            line-height: 40px;
            position: relative;
            text-align: center;
            width: 40px;
        }

        .form-progress-bar .form-progress-bar-steps span.check,
        .form-progress-bar .form-progress-bar-steps span.check {
            background-color: #6f9c40;
            color: #ffffff;
        }

        .form-progress-bar .form-progress-bar-steps span.pending,
        .form-progress-bar .form-progress-bar-steps span.pending {
            background-color: #e69f17;
            color: #ffffff;
        }

        .form-progress-bar .form-progress-bar-steps span.pending-secondary,
        .form-progress-bar .form-progress-bar-steps span.pending-secondary {
            background-color: #909395;
            color: #ffffff;
        }

        .form-progress-bar .form-progress-bar-steps span.reject,
        .form-progress-bar .form-progress-bar-steps span.reject {
            background-color: red;
            color: #ffffff;
        }

        .bg-pending-previous {
            background-color: #909395;
        }

        .bg-check {
            background-color: #6f9c40;
        }

        .bg-pending {
            background-color: #e69f17;
        }
    </style>
@endpush

@section('content')
    <form id="save-form">
        @include('admin.components.step-progress')
        <div class="block {{ __('block.styles') }}">
            <div class="block-content">
                @include('admin.replacement-car-informs.sections.info')
                @include('admin.replacement-car-informs.sections.date-place')
                <x-forms.hidden id="id" name="id" :value="$d->id" />
                @if ($approve_line_owner)
                    <x-forms.hidden id="approve_line_id" :value="$approve_line_owner->id" />
                @endif
            </div>
        </div>
        @if (isset($d->main_car_id))
            @include('admin.replacement-car-informs.sections.car-detail', [
                'car_type' => 'main',
                'car' => $main_car,
            ])
            @include('admin.replacement-car-informs.modals.accident-history', [
                'car_type' => 'main',
            ])
            @include('admin.replacement-car-informs.modals.repair-history', [
                'car_type' => 'main',
            ])
            @include('admin.replacement-car-informs.modals.accessory', [
                'car_type' => 'main',
            ])
            @include('admin.replacement-car-informs.modals.condition', [
                'car_type' => 'main',
            ])
        @endif

        @include('admin.replacement-car-informs.sections.car-detail', [
            'car_type' => 'replace',
            'car' => $replacement_car,
        ])
        @include('admin.replacement-car-informs.modals.accident-history', [
            'car_type' => 'replace',
        ])
        @include('admin.replacement-car-informs.modals.repair-history', [
            'car_type' => 'replace',
        ])
        @include('admin.replacement-car-informs.modals.accessory', [
            'car_type' => 'replace',
        ])
        @include('admin.replacement-car-informs.modals.condition', [
            'car_type' => 'replace',
        ])
        @include('admin.replacement-car-approves.sections.submit')
    </form>
    @include('admin.components.transaction-modal')
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
    'url' => route('admin.util.select2-replacement-car.jobs'),
])

@include('admin.components.select2-ajax', [
    'id' => 'replacement_car_id',
    'url' => route('admin.util.select2-replacement-car.available-replacement-cars'),
])

@include('admin.components.upload-image-scripts')
@include('admin.components.upload-image', [
    'id' => 'documents',
    'max_files' => 10,
    'accepted_files' => '.jpg,.jpeg,.bmp,.png,.pdf',
    'mock_files' => $replacement_car_files,
    'show_url' => true,
    'view_only' => true,
])

@include('admin.replacement-car-informs.scripts.replacement-script')
@include('admin.replacement-cars.scripts.replacement-car-script')

@push('scripts')
    <script>
        $('#worksheet_no_field').prop('disabled', true);
        $('#creator').prop('disabled', true);
        $('#contract_no').prop('disabled', true);
        $('#main_license_plate').prop('disabled', true);
        $('#show_replacement_car_id').prop('disabled', true);

        $('#replacement_type').prop('disabled', true);
        $('#job_type').prop('disabled', true);
        $('#job_id').prop('disabled', true);
        $('#replacement_expect_date').prop('disabled', true);
        $('#replacement_expect_place').prop('disabled', true);
        $('#customer_name').prop('disabled', true);
        $('#tel').prop('disabled', true);
        $('#remark').prop('disabled', true);
        $('input[name="is_need_driver"]').prop('disabled', true);
        $('input[name="is_need_slide"]').prop('disabled', true);

        const mode =
            @if (isset($mode))
                @json($mode)
            @else
                false
            @endif ;
        if (mode == 'MODE_VIEW') {
            $('#replacement_place').prop('disabled', true);
            $('#replacement_date').prop('disabled', true);
            $('#replacement_car_id').prop('disabled', true);
            $('#spec_low_reason').prop('disabled', true);
            $('#is_spec_low_0').prop('disabled', true);
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

        $(".btn-approve-status").on("click", function() {
            let storeUri = "{{ $route_uri }}";
            var formData = new FormData(document.querySelector('#save-form'));
            var status = $(this).attr('data-status');
            formData.append('status_update', status);

            mySwal.fire({
                title: "{{ __('install_equipment_pos.approve_confirm') }}",
                html: 'เมื่อยืนยันใบงานรถทดแทนแล้ว ระบบจะส่งข้อมูลคำขอนี้เพื่อดำเนินการในส่วนต่อไป',
                icon: 'warning',
                customClass: {
                    confirmButton: 'btn btn-primary m-1',
                    cancelButton: 'btn btn-secondary m-1'
                },
                showCancelButton: true,
                confirmButtonText: "{{ __('lang.confirm') }}",
                cancelButtonText: "{{ __('lang.cancel') }}",
                preConfirm: e => {
                    return new Promise(resolve => {
                        setTimeout(() => {
                            resolve();
                        }, 50);
                    });
                }
            }).then((result) => {
                if (result.value) {
                    saveForm(storeUri, formData);
                }
            })
        });

        $(".btn-disapprove-status").on("click", function() {
            let storeUri = "{{ $route_uri }}";
            var formData = new FormData(document.querySelector('#save-form'));
            var status = $(this).attr('data-status');

            formData.append('status_update', status);

            mySwal.fire({
                title: "{{ __('install_equipment_pos.disapprove_confirm') }}",
                html: 'กรุณาให้เหตุผลการไม่อนุมัติใบงานรถทดแทนนี้ ',
                input: 'text',
                icon: 'warning',
                customClass: {
                    confirmButton: 'btn btn-danger m-1',
                    cancelButton: 'btn btn-secondary m-1'
                },
                showCancelButton: true,
                confirmButtonText: "{{ __('lang.confirm') }}",
                cancelButtonText: "{{ __('lang.cancel') }}",
                preConfirm: e => {
                    return new Promise(resolve => {
                        setTimeout(() => {
                            resolve();
                        }, 50);
                    });
                }
            }).then((result) => {
                if (result.value) {
                    var reject_reason = result.value;
                    formData.append('reject_reason', reject_reason);
                    saveForm(storeUri, formData);
                }
            })
        });
    </script>
@endpush
