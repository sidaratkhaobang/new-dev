@extends('admin.layouts.layout')
@section('page_title', __('insurance_car.insurance_car_title'))
@section('styles')
    <style>
        .btn-renew-all {
            height: 42px;
        }
    </style>
@endsection
@section('content')

    {{-- Search Section--}}
    @include('admin.insurance-car.sections.index-search')
    {{--  Table Section  --}}
    @include('admin.insurance-car.sections.index-table')
    {{--    Modal Renew Cmi    --}}
    @include('admin.insurance-car.modals.modal-cmi-renew')
    @include('admin.insurance-car.modals.modal-cmi-cancel')
@endsection

@include('admin.components.select2-default')
@include('admin.components.date-input-script')
@include('admin.components.sweetalert')
@include('admin.insurance-car.scripts.index-scripts')
@include('admin.components.list-delete')
@include('admin.components.form-save', [
    'store_uri' => route('admin.insurance-car.store'),
])
@push('scripts')
    <script>
        $('#modal-renew-cmi-jobtype').prop('disabled', true);
        $(document).ready(function () {
            var $selectAll = $('#selectAll');
            var $table = $('.table');
            var $tdCheckbox = $table.find('tbody input:checkbox');
            var tdCheckboxChecked = 0;
            var url_cancel;
            $selectAll.on('click', function () {
                $tdCheckbox.prop('checked', this.checked);
            });

            $tdCheckbox.on('change', function (e) {
                tdCheckboxChecked = $table.find('tbody input:checkbox:checked').length;
                $selectAll.prop('checked', (tdCheckboxChecked === $tdCheckbox.length));
            })
            // Check All For List
            $(document).on('change', ".check-all-block", function () {
                let cehcked_status = $(this).is(':checked')
                let list_car = $(this).closest('table').find('tbody')
                $(list_car).find('.form-check-input-each').prop('checked', cehcked_status)
            })

            $(document).on('change', ".form-check-input-each", function () {
                let total_checlbox = $(this).closest('tbody').find('.form-check-input-each').length
                let total_checlbox_checked = $(this).closest('tbody').find('.form-check-input-each:checked').length

                if (total_checlbox == total_checlbox_checked) {
                    $(this).closest('table').find('.check-all-block').prop('checked', true)
                } else {
                    $(this).closest('table').find('.check-all-block').prop('checked', false)
                }
            })

            $(document).on('click', ".btn-cancel-status", function () {
                $('#modal-cmi-cancel').modal('toggle')
                url_cancel = $(this).data('url')
                return false

            });
            $(document).on('click', ".btn-save-cancel", function () {
                let insurance_cancel_date = $('#insurance_cancel_date').val()
                let insurance_cancel_reason = $('#insurance_cancel_reason').val()
                if(!insurance_cancel_date){
                    return warningAlert("กรุณากรอก วันที่ขอยกเลิก");
                }
                if(!insurance_cancel_reason){
                    return warningAlert("กรุณากรอก เหตุผลที่ขอยกเลิก");
                }
                axios.post(url_cancel, {insurance_cancel_date: insurance_cancel_date,insurance_cancel_reason:insurance_cancel_reason}).then(response => {
                    if (response.data.success) {
                        mySwal.fire({
                            title: "{{ __('lang.store_success_title') }}",
                            text: "{{ __('lang.store_success_message') }}",
                            icon: 'success',
                            confirmButtonText: "{{ __('lang.ok') }}"
                        }).then(value => {
                            if (response.data.redirect) {
                                window.location.href = response.data.redirect;
                            } else {
                                window.location.reload();
                            }
                        });
                    } else {

                        mySwal.fire({
                            title: "{{ __('lang.store_error_title') }}",
                            text: response.data.message,
                            icon: 'error',
                            confirmButtonText: "{{ __('lang.ok') }}",
                        }).then(value => {
                            if (value) {
                                //
                            }
                        });
                    }

                }).catch(error => {
                    mySwal.fire({
                        title: "{{ __('lang.store_error_title') }}",
                        text: error.response.data.message,
                        icon: 'error',
                        confirmButtonText: "{{ __('lang.ok') }}",
                    }).then(value => {
                        if (value) {
                            //
                        }
                    });
                });
            })
        })

        $(document).on('click', ".car_cmi", function () {
            $(this).closest('tbody').find('.car_cmi').prop('checked', false)
            $(this).prop('checked', true)
            let id = $(this).data('id')
            $('#insurance_cmi_id').val(id)
        })
        $(document).on('click', ".car_vmi", function () {
            $(this).closest('tbody').find('.car_vmi').prop('checked', false)
            $(this).prop('checked', true)
            let id = $(this).data('id')
            $('#insurance_vmi_id').val(id)
        })
    </script>
@endpush
