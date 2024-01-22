@extends('admin.layouts.layout')

@section('page_title', $page_title)

@section('content')
<form id="save-form">
    @include('admin.compensations.sections.car-detail', ['car' => $d->accident?->car])
    @include('admin.compensations.sections.rental-detail')
    @include('admin.compensations.sections.insurance-detail')
    @include('admin.compensations.sections.prescription')
    @include('admin.compensations.sections.party')
    @include('admin.compensations.sections.compens-sum')
    @includeWhen(!in_array($d->status, [CompensationStatusEnum::PENDING]), 'admin.compensations.sections.notice')
    @includeWhen($d->type, 'admin.compensations.sections.negotiation')
    @includeWhen($is_end_nogotiation, 'admin.compensations.sections.terminate')
    <div class="block {{ __('block.styles') }}">
        <div class="block-content text-end">
            <x-forms.hidden id="id" name="id" :value="$d->id" />
            <a class="btn btn-outline-secondary btn-custom-size me-2" href="{{ route('admin.compensation-approves.index') }}">{{ __('lang.back') }}</a>
                @if ($d->status == CompensationStatusEnum::PENDING_REVIEW)
                    @can(Actions::Manage . '_' . Resources::CompensationApprove)
                        <a class="btn btn-danger btn-disapprove-status" id="{{ CompensationStatusEnum::REJECT }}">{{ __('lang.disapprove') }}</a>
                        <a class="btn btn-primary btn-update-status"
                            id="{{ CompensationStatusEnum::CONFIRM }}">{{ __('lang.approve') }}</a>
                    @endcan
                @endif
        </div>
    </div>
</form>
@endsection

@include('admin.components.select2-default')
@include('admin.components.sweetalert')
@include('admin.components.form-save', [
    'store_uri' => route('admin.compensation-approves.store'),
])
@include('admin.components.date-input-script')
@include('admin.compensations.scripts.notice-script')


@include('admin.components.select2-ajax', [
    'id' => 'insurer_parties_id',
    'url' => route('admin.util.select2-insurance.insurance-companies'),
])

@include('admin.components.select2-ajax', [
    'id' => 'creator_id',
    'url' => route('admin.util.select2.users'),
])

@include('admin.components.select2-ajax', [
    'id' => 'car_brand_parties_id',
    'url' => route('admin.util.select2.car-brand'),
])
@include('admin.components.upload-image-scripts')
@include('admin.components.upload-image', [
    'id' => 'termination_files',
    'max_files' => 10,
    'accepted_files' => '.jpg,.jpeg,.bmp,.png,.pdf',
    'mock_files' => $termination_files,
    'show_url' => true
])

@push('scripts')
    <script>
        $("#negotiation_type").on('select2:select', function(e) {
            var data = e.params.data;
            if (data.id === '{{ NegotiationTypeEnum::INSURANCE }}') {
                $(".insurance-section").css("display","block");
                $(".oic-section").css("display","none");
            }

            if (data.id === '{{ NegotiationTypeEnum::OIC }}') {
                $(".insurance-section").css("display","none");
                $(".oic-section").css("display","block");
            }
        });

        $("#negotiation_type").on('select2:unselect', function(e) {
            $(".insurance-section").css("display","none");
            $(".oic-section").css("display","none");
        });

        const inputs_readonly = document.querySelectorAll('.content-readonly input, .content-readonly select');
        inputs_readonly.forEach(input => {
            input.setAttribute('readonly', 'true');
            input.setAttribute('disabled', 'true');
        });

        const is_view = @if (is_view()) true @else false @endif;
        if (is_view) {
            const inputs = document.querySelectorAll('input, select');
            inputs.forEach(input => {
                input.setAttribute('readonly', 'true');
                input.setAttribute('disabled', 'true');
            });
        }

        $(".btn-update-status").on("click", function () {
            var data = {
                status: $(this).attr('id'),
                compensation_id: document.getElementById("id").value,
                // approve_line_id: document.getElementById("approve_line").value,
            };
            mySwal.fire({
                title: 'ยืนยันอนุมัติ',
                html: 'เมื่อยืนยันแล้ว ระบบจะส่งข้อมูลคำขอนี้เพื่อดำเนินการในส่วนต่อไป',
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
                    updateStatus(data);
                }
            })
        });


        $(".btn-disapprove-status").on("click", function() {
            var data = {
                status: $(this).attr('id'),
                compensation_id: document.getElementById("id").value,
                // approve_line_id: document.getElementById("approve_line").value,
            };

            mySwal.fire({
                title: "{{ __('compensation.disapprove_confirm') }}",
                html: 'กรุณาให้เหตุผลการไม่อนุมัติในครั้งนี้ <span class="text-danger">*</span>',
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
                    data.reject_reason = reject_reason;
                    updateStatus(data);
                } else {
                    warningAlert("{{ __('lang.required_field_inform') }}")
                }
            })
        });


        function updateStatus(data) {
            var updateUri = "{{ route('admin.compensation-approves.update-status') }}";
            axios.post(updateUri, data).then(response => {
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
        }
    </script>
@endpush