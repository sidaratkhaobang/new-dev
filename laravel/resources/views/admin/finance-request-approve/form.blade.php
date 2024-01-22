@extends('admin.layouts.layout')
@section('page_title', $page_title)
@section('content')
    {{-- @if (isset($approve_line_list) && $approve_line_list)
        @include('admin.components.step-progress')
    @endif --}}
    <x-approve.step-approve :configenum="ConfigApproveTypeEnum::FINANCE_REQUEST" :id="$prepare->id"
                            :model="get_class($prepare)"/>
    <form id="save-form">
        <div class="block {{ __('block.styles') }}">
            @include('admin.components.block-header', [
                    'text' => __('finance_request.form_header'),
                    'block_icon_class' => 'icon-document',
                ])
            <div class="block-content">
                <div class="justify-content-between mb-4">
                    <div class="form-group row push mb-4">
                        <div class="col-sm-3">
                            <input type="hidden" name="prepare_id" value="{{$prepare?->id ?? null}}">
                            <input type="hidden" name="lot_id" value="{{$lot_id ?? null}}">
                            <x-forms.label id="lot_name"
                                           :value="$lot_name ?? '-'"
                                           :label="__('finance_request.search_lot_no')"/>
                        </div>
                        <div class="col-sm-3">
                            <x-forms.label id="lot_name"
                                           :value="$rental_name ?? '-'"
                                           :label="__('finance_request.search_rental')"/>
                        </div>
                        <div class="col-sm-3">
                            <x-forms.label id="lot_name"
                                           :value="$prepare?->creation_date ?? '-'"
                                           :label="__('finance_request.search_date_create')"/>
                        </div>
                    </div>
                    <div class="form-group row push mb-4">
                        <div class="col-sm-3">
                            <x-forms.label id="lot_name"
                                           :value="$prepare?->billing_date ?? '-'"
                                           :label="__('finance_request.bill_date')"/>
                        </div>
                        <div class="col-sm-3">
                            <x-forms.label id="lot_name"
                                           :value="$prepare?->payment_date ?? '-'"
                                           :label="__('finance_request.payment_date')"/>
                        </div>

                    </div>
                </div>
            </div>
        </div>
        <div class="block {{ __('block.styles') }}">
            @include('admin.components.block-header', [
                    'text' => __('finance_request.car_list'),
                    'block_icon_class' => 'icon-document',
                ])
            <div class="block-content">
                <div class="justify-content-between mb-4">
                    <div class="table-wrap db-scroll">
                        <table class="table table-striped table-vcenter">
                            <thead class="bg-body-dark">
                            <tr>
                                <th>
                                    #
                                </th>
                                <th>
                                    {{ __('finance_request.po_id') }}
                                </th>
                                <th>
                                    {{__('finance_request.engine_no')}}
                                </th>
                                <th>
                                    {{__('finance_request.chassis_no')}}
                                </th>
                                <th>
                                    {{__('finance_request.number_installments')}}
                                </th>
                                <th>
                                    {{__('finance_request.accessory_price_total')}}
                                </th>
                                <th>
                                    {{__('finance_request.car_vat_price')}}
                                </th>
                                <th>
                                    {{__('finance_request.accessory_car_vat')}}
                                </th>
                                <th>
                                    {{__('finance_request.make_finance')}}
                                </th>
                                <th>

                                </th>
                            </tr>
                            </thead>
                            <tbody>
                            @if(!$list?->isEmpty())
                                @foreach($list as $key => $d )
                                    <input type="hidden" name="finance_car_data[{{$key}}][finance_id]"
                                           value="{{$d?->id}}">
                                    <tr>
                                        <td>
                                            {{$list->currentPage() * $list->perPage() - $list->perPage() + 1 +$key}}
                                        </td>
                                        <td>
                                            {{$d?->purchase_order?->po_no ?? '-'}}
                                        </td>
                                        <td>
                                            {{$d?->car?->engine_no ?? '-'}}
                                        </td>
                                        <td>
                                            {{$d?->car?->chassis_no ?? '-'}}
                                        </td>
                                        <td>
                                            {{$d?->number_installments ?? '-'}}
                                        </td>
                                        <td>
                                            {{$d?->accessory_price? number_format($d?->accessory_price): '-'}}
                                        </td>
                                        <td>
                                            {{$d?->car_price_vat? number_format($d?->car_price_vat): '-'}}
                                        </td>
                                        <td>
                                            {{$d?->car_total_price? number_format($d?->car_total_price): '-'}}
                                        </td>
                                        <td>
                                            {{__('finance_request.car_status' . $d?->type_car_financing)}}
                                            {{--                                            <x-forms.select-option id="finance_car_data[{{$key}}][finance_type]"--}}
                                            {{--                                                                   :value="$d?->is_car_financing_only ?? null"--}}
                                            {{--                                                                   :list="$d?->finance_type_list ?? []"--}}
                                            {{--                                                                   :optionals="['placeholder' => __('lang.search_placeholder'),'select_class' => 'finance_car_data js-select2-default']"--}}
                                            {{--                                                                   :label="null"/>--}}
                                        </td>
                                        <td>
                                            @include('admin.components.dropdown-action', [
                                                                                    'view_route' => route('admin.finance-request-approve.finance-request-car-detail.show', ['finance_request_id' => $d->id]),
    //                                                                                'edit_route' => route('admin.finance-request.edit', ['finance_request' => $d->id]),
                                                                                    'view_permission' => Actions::View . '_' . Resources::FinanceRequestApprove,
                                                                                    'manage_permission' => Actions::Manage . '_' . Resources::FinanceRequestApprove,
                                                                                ])
                                        </td>
                                    </tr>
                                @endforeach

                            @else
                                <tr>
                                    <td class="text-center" colspan="11">" {{ __('lang.no_list') }} "</td>
                                </tr>
                            @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </form>
    @if ($approve_line_owner)
        <x-forms.hidden id="approve_line" :value="$approve_line_owner->id"/>
    @endif
    <x-forms.hidden id="id" name="id" :value="$prepare?->id"/>
    <div class="block {{ __('block.styles') }}">
        <div class="block-content">
            <div class="row">
                <div class="text-end">
                    <a class="btn btn-secondary"
                       href="{{ route('admin.finance-request-approve.index') }}">{{ __('lang.back') }}</a>
                    @if ($approve_line_owner)
                        @if ($d->status == FinanceRequestStatusEnum::PENDING_APPROVE)
                            @can(Actions::Manage . '_' . Resources::FinanceRequestApprove)
                                <a class="btn btn-danger btn-disapprove-status">{{ __('lang.disapprove') }}</a>
                                <a class="btn btn-primary btn-finance-request-approve-update-status"
                                   id="{{ FinanceRequestStatusEnum::APPROVE }}">{{ __('lang.approve') }}</a>
                            @endcan
                        @endif
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{--    --}}{{--    submit button   --}}
    {{--    <div class="block {{ __('block.styles') }}">--}}
    {{--        <div class="block-content">--}}
    {{--            <div class="justify-content-between">--}}
    {{--                <x-forms.submit-group--}}
    {{--                    :optionals="['url' => 'admin.finance-request.index', 'view' => empty($view) ? null : $view,'manage_permission' => Actions::Manage . '_' . Resources::InsuranceCompanies]"/>--}}
    {{--            </div>--}}
    {{--        </div>--}}
    {{--    </div>--}}

@endsection
@include('admin.components.select2-default')
@include('admin.components.sweetalert')
@include('admin.components.date-input-script')
@include('admin.components.select2-ajax', [
    'id' => 'rental',
    'url' => route('admin.util.select2-finance.creditor-leasing-list'),
])
@include('admin.components.form-save', [
    'store_uri' => route('admin.finance-request.store'),
])

@push('scripts')
    <script>
        @if(isset($view))
        $('#lot_name').prop('readonly', true)
        $('#rental').prop('disabled', true)
        $('#bill_date').prop('disabled', true)
        $('#date_create').prop('disabled', true)
        $('#payment_date').prop('disabled', true)
        $('.finance_car_data').prop('disabled', true)
        @else
        $('#lot_name').prop('readonly', true)
        $('#rental').prop('disabled', true)
        $('.finance_car_data').prop('disabled', true)
        @endif

        function updatePurchaseOrderStatus(data) {
            var updateUri = "{{ route('admin.finance-request-approve.finance-request-update-status') }}";
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

        $(".btn-finance-request-approve-update-status").on("click", function () {
            var data = {
                finance_request_status: $(this).attr('id'),
                finance_request_id: document.getElementById("id").value,
                redirect_route: '{{ route('admin.finance-request-approve.index') }}',
                approve_line_id: document.getElementById("approve_line").value,
                lot_id: "{{$lot_id}}"
            };
            mySwal.fire({
                title: 'ยืนยันอนุมัติ ข้อมูลไฟแนนซ์',
                html: 'เมื่อยืนยันข้อมูลไฟแนนซ์แล้ว ระบบจะส่งข้อมูลคำขอนี้เพื่อดำเนินการในส่วนต่อไป',
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
                    updatePurchaseOrderStatus(data);
                }
            })
        });


        $(".btn-disapprove-status").on("click", function () {
            var data = {
                finance_request_status: '{{ FinanceRequestStatusEnum::REJECT }}',
                finance_request_id: document.getElementById("id").value,
                redirect_route: '{{ route('admin.finance-request-approve.index') }}',
                approve_line_id: document.getElementById("approve_line").value,
            };
            mySwal.fire({
                title: "{{ __('purchase_orders.disapprove_confirm') }}",
                html: 'กรุณาให้เหตุผลการไม่อนุมัตข้อมูลไฟแนนซ์ในครั้งนี้ <span class="text-danger">*</span>',
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
                    updatePurchaseOrderStatus(data);
                } else {
                    warningAlert("{{ __('lang.required_field_inform') }}")
                }
            })
        });
    </script>
@endpush
