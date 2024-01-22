@extends('admin.layouts.layout')

@section('page_title', $page_title)
@section('history')
    @include('admin.components.btns.history')
@endsection

@section('content')
{{-- @if (isset($approve_line_list) && $approve_line_list)
    @include('admin.components.step-progress')
@endif --}}
<x-approve.step-approve :configenum="ConfigApproveTypeEnum::LITIGATION" :id="$d->id" :model="get_class($d)" />
<form id="save-form">
    @include('admin.litigations.sections.charge')
    @include('admin.litigations.sections.table-file-upload')
    @include('admin.litigations.sections.incident')
    @includeWhen(strcmp($d->location_case, LitigationLocationEnum::POLICE_STATION) === 0, 'admin.litigations.sections.police')
    @includeWhen(strcmp($d->location_case, LitigationLocationEnum::COURT) === 0, 'admin.litigations.sections.court')
    @includeWhen(!empty($d->location_case), 'admin.litigations.sections.status')
    @includeWhen(strcmp($d->status, LitigationStatusEnum::FOLLOW) === 0, 'admin.litigations.sections.cost')

    
    <div class="block {{ __('block.styles') }}">
        <div class="block-content text-end">
            <x-forms.hidden id="id" name="id" :value="$d->id" />
            <a class="btn btn-outline-secondary btn-custom-size me-2" href="{{ route('admin.litigation-approves.index') }}">{{ __('lang.back') }}</a>
                @if ($d->status == LitigationStatusEnum::PENDING_REVIEW)
                    @can(Actions::Manage . '_' . Resources::LitigationApprove)
                        <a class="btn btn-danger btn-disapprove-status" id="{{ LitigationStatusEnum::REJECT }}">{{ __('lang.disapprove') }}</a>
                        <a class="btn btn-primary btn-update-status"
                            id="{{ LitigationStatusEnum::CONFIRM }}">{{ __('lang.approve') }}</a>
                    @endcan
                @endif
        </div>
    </div>
</form>
@include('admin.components.transaction-modal')
@endsection

@include('admin.components.select2-default')
@include('admin.components.sweetalert')
@include('admin.components.form-save', [
    'store_uri' => route('admin.litigations.store'),
])
@include('admin.components.date-input-script')
@include('admin.components.upload-image-scripts')
@include('admin.litigations.scripts.status-script')
@includeWhen(strcmp($d->status, LitigationStatusEnum::FOLLOW) === 0, 'admin.litigations.scripts.cost-script')
@include('admin.components.upload-image', [
    'id' => 'litigation_files',
    'max_files' => 5,
    'accepted_files' => '.jpg,.jpeg,.bmp,.png,.pdf',
    'mock_files' => $litigation_files ?? [],
    'show_url' => true,
    'view_only' => isset($view) ? true : null,
])

@include('admin.components.upload-image', [
    'id' => 'additional_files',
    'max_files' => 1,
    'accepted_files' => '.jpeg,.jpg,.png,.svg,.pdf,.xls,.xlsx,.doc,.csv',
    'preview_files' => true,
])

@push('scripts')
    <script>
        var status = '{{ $d->status }}';
        var is_view = '{{ $view ?? null }}';
        var location_case = @json($d->location_case);
        if (location_case) {
            disableSectionCharge(true);
            disableIncident(true);
        }
        if (status == '{{ LitigationStatusEnum::PENDING }}') {
            disableSectionCharge(true);
        }

        if (is_view) {
            disableSectionCharge(true);
            disableIncident(true);
            disableExtendIncident(true);
            disableCourt(true);
        }

        function disableSectionCharge(is_disabled) {
            $("#title").prop('disabled', is_disabled);
            $("#case").prop('disabled', is_disabled);
            $("#case_type").prop('disabled', is_disabled);
            $("#tls_type").prop('disabled', is_disabled);
            $("#accuser_defendant").prop('disabled', is_disabled);
            $("#incident_date").prop('disabled', is_disabled);
            $("#consultant").prop('disabled', is_disabled);
            $("#fund").prop('disabled', is_disabled);
            $("#responsible_person_id").prop('disabled', is_disabled);
            $("#legal_service_provider").prop('disabled', is_disabled);
            $("#legal_service_provider").prop('disabled', is_disabled);
            $("#legal_service_fee").prop('disabled', is_disabled);
            $("#legal_note").prop('disabled', is_disabled);
        }

        function disableIncident(is_disabled) {
            $("#location_case").prop('disabled', is_disabled);
            $("#details").prop('disabled', is_disabled);
        }

        function disableExtendIncident(is_disabled) {
            $("#request_date").prop('disabled', is_disabled);
            $("#receive_date").prop('disabled', is_disabled);
        }

        function disableCourt(is_disabled) {
            $("#court_filing_date").prop('disabled', is_disabled);
            $("#location_name").prop('disabled', is_disabled);
            $("#black_number").prop('disabled', is_disabled);
            $("#red_number").prop('disabled', is_disabled);
            $("#age").prop('disabled', is_disabled);
            $("#remark").prop('disabled', is_disabled);
            $("#remark").prop('disabled', is_disabled);
            $("#due_date").prop('disabled', is_disabled);
            $("#inquiry_official").prop('disabled', is_disabled);
            $("#inquiry_official_tel").prop('disabled', is_disabled);
        }


        $(".btn-update-status").on("click", function () {
            var data = {
                status: $(this).attr('id'),
                litigation_id: document.getElementById("id").value,
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
                litigation_id: document.getElementById("id").value,
                // approve_line_id: document.getElementById("approve_line").value,
            };

            mySwal.fire({
                title: "{{ __('litigations.disapprove_confirm') }}",
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
            var updateUri = "{{ route('admin.litigation-approves.update-status') }}";
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