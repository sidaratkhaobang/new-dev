@extends('admin.layouts.layout')

@section('page_title', $page_title)
@section('history')
    @include('admin.components.btns.history')
@endsection
@push('styles')
    <style>
      
    </style>
@endpush
@section('content')
{{-- @if (isset($approve_line_list) && $approve_line_list)
        @include('admin.components.step-progress')
    @endif --}}
    <x-approve.step-approve :configenum="ConfigApproveTypeEnum::LT_SPEC_ACCESSORY" :id="$d->id" :model="get_class($d)" />
    <div class="block {{ __('block.styles') }}">
        <div class="block-header">
            <h3 class="block-title">ข้อมูลใบเช่ายาว: {{ $d->worksheet_no }}
                @if ($d->spec_status == SpecStatusEnum::REJECT)
                    {!! badge_render(
                        __('long_term_rentals.spec_status_class_' . $d->spec_status),
                        __('long_term_rentals.spec_status_' . $d->spec_status),
                    ) !!}
                @endif
            </h3>
        </div>
        <div class="block-content">
            <form id="save-form">

                @include('admin.long-term-rental-specs.sections.rental-detail')
                @include('admin.long-term-rental-specs.sections.upload')
                @include('admin.long-term-rental-specs.sections.car-accessory')
                {{-- @include('admin.long-term-rental-specs.views.car-accessory') --}}
                @if ($approve_line_owner)
                    <x-forms.hidden id="approve_line_id" :value="$approve_line_owner->id" />
                @endif
                <x-forms.hidden id="id" :value="$d->id" />
                <div class="row push">
                    <div class="col-sm-12 text-end">
                        {{-- all --}}
                        <a class="btn btn-secondary"
                            href="{{ route('admin.long-term-rental.specs-approve.index') }}">{{ __('lang.back') }}</a>

                        {{-- รออนุมัติ --}}
                        @if ($approve_line_owner)
                            @if ($d->spec_status == SpecStatusEnum::PENDING_REVIEW)
                                @can(Actions::Manage . '_' . Resources::LongTermRentalSpecApprove)
                                    <button type="button" class="btn btn-danger btn-disapprove-status"
                                        data-status="{{ SpecStatusEnum::REJECT }}">{{ __('long_term_rentals.reject') }}</button>
                                    <button type="button" class="btn btn-primary btn-approve-status"
                                        data-status="{{ SpecStatusEnum::CONFIRM }}">{{ __('long_term_rentals.approved') }}</button>
                                @endcan
                            @endif
                        @endif
                    </div>
                </div>
            </form>
        </div>
    </div>
    @include('admin.long-term-rental-spec-approve.modals.approve-modal')
    @include('admin.long-term-rental-spec-approve.modals.cancel-modal')
    @include('admin.components.transaction-modal')
@endsection

@include('admin.components.select2-default')
@include('admin.components.sweetalert')
@include('admin.components.form-save', [
    'store_uri' => route('admin.long-term-rental.specs-approve.update-status'),
])
@include('admin.long-term-rental-spec-approve.scripts.update-status')
@include('admin.long-term-rental-specs.scripts.car-script')
@include('admin.long-term-rental-specs.scripts.accessory-script')
@push('scripts')
    <script>
        $('#worksheet_no').prop('disabled', true);
        $('#job_type').prop('disabled', true);
        $('#rental_duration').prop('disabled', true);
        $('#remark').prop('disabled', true);
        $('#customer_type').prop('disabled', true);
        $('#customer_id').prop('disabled', true);
        $('#customer_name').prop('disabled', true);
        $('#offer_date').prop('disabled', true);

        var spec_status = '{{ $d->spec_status }}';
        if (spec_status != '{{ SpecStatusEnum::PENDING_REVIEW }}') {
            $('input[name="tor_line_check_input[]"]').prop('disabled', true);
        }
        $('.toggle-table').parent().next('tr').toggle();
        $('.toggle-table').click(function() {
            $(this).parent().next('tr').toggle();
            $(this).children().toggleClass('fa fa-angle-down text-muted').toggleClass(
                'fa fa-angle-right text-muted');
        });

        // $(".btn-show-approve-modal").on("click", function() {
        //     document.getElementById("approve_status").value = $(this).attr('data-status');
        //     document.getElementById("approve_id").value = document.getElementById("id").value;
        //     document.getElementById("redirect").value = "{{ route('admin.long-term-rental.specs-approve.index') }}";
        //     $('#modal-approve').modal('show');
        // });

        // $(".btn-show-reject-modal").on("click", function() {
        //     document.getElementById("cancel_status").value = $(this).attr('data-status');
        //     document.getElementById("cancel_id").value = document.getElementById("id").value;
        //     document.getElementById("redirect").value = "{{ route('admin.long-term-rental.specs-approve.index') }}";
        //     $('#modal-cancel').modal('show');
        // });

        $(".btn-disapprove-status").on("click", function() {
            let storeUri = "{{ route('admin.long-term-rental.specs-approve.update-status') }}";
            var formData = new FormData(document.querySelector('#save-form'));
            var status = $(this).attr('data-status');
            var lt_rental_id = document.getElementById("id").value;

            formData.append('spec_status', status);
            formData.append('lt_rental_id', document.getElementById("id").value);

            mySwal.fire({
                title: "{{ __('long_term_rentals.disapprove_confirm') }}",
                html: 'กรุณาให้เหตุผลการไม่อนุมัติสเปครถและอุปกรณ์ในครั้งนี้ ',
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
                    var reject_reason = $('.swal2-input').val();
                    formData.append('reject_reason', reject_reason);
                    saveForm(storeUri, formData);
                }
            })
        });

        $(".btn-approve-status").on("click", function() {
            let storeUri = "{{ route('admin.long-term-rental.specs-approve.update-status') }}";
            var formData = new FormData(document.querySelector('#save-form'));
            var status = $(this).attr('data-status');
            var lt_rental_id = document.getElementById("id").value;

            formData.append('spec_status', status);
            formData.append('lt_rental_id', document.getElementById("id").value);

            mySwal.fire({
                title: "{{ __('long_term_rentals.approve_confirm') }}",
                html: 'เมื่อยืนยันสเปครถและอุปกรณ์แล้ว ระบบจะส่งข้อมูลคำขอนี้เพื่อดำเนินการในส่วนต่อไป',
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
    </script>
@endpush
