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
    </style>
@endpush

@section('content')
    <div class="block {{ __('block.styles') }}">
        {{-- <div class="block-header">
            <h3 class="block-title">{{ __('transfer_cars.search') }}</h3>
            <div class="block-options">
                <div class="block-options-item">
                </div>
            </div>
        </div> --}}
        @include('admin.components.block-header', [
            'text' => __('lang.search'),
            'block_icon_class' => 'icon-search',
            'is_toggle' => true,
        ])
        <div class="block-content">
            <div class="justify-content-between">
                <form action="" method="GET" id="form-search">
                    <div class="form-group row push mb-4">
                        <div class="col-sm-3">
                            <x-forms.select-option id="car_id" :value="$car_id" :list="$license_plate_list" :label="__('transfer_cars.license_plate_chassis')" />
                        </div>
                        <div class="col-sm-3">
                            <x-forms.select-option id="from_branch_id" :value="$from_branch_id" :list="$branch_lists" :optionals="['placeholder' => __('lang.search_placeholder')]"
                                :label="__('transfer_cars.from_branch')" />
                        </div>
                        <div class="col-sm-3">
                            <x-forms.select-option id="to_branch_id" :value="$to_branch_id" :list="$branch_lists" :optionals="['placeholder' => __('lang.search_placeholder')]"
                                :label="__('transfer_cars.to_branch')" />
                        </div>
                        <div class="col-sm-3">
                            <x-forms.select-option id="status" :value="$status_id" :list="$status_lists"
                                :label="__('transfer_cars.status')" />
                        </div>
                    </div>
                    <div class="form-group row push mb-4">
                        <div class="col-sm-3">
                            <label class="text-start col-form-label"
                                for="from_delivery_date">{{ __('transfer_cars.pickup_return_date') }}</label>
                            <div class="form-group">
                                <div class="input-daterange input-group" data-date-format="mm/dd/yyyy" data-week-start="1"
                                    data-autoclose="true" data-today-highlight="true">
                                    <input type="text" class="js-flatpickr form-control flatpickr-input" id="from_date"
                                        name="from_date" value="{{ $from_date }}"
                                        placeholder="{{ __('lang.select_date') }}" data-week-start="1" data-autoclose="true"
                                        data-today-highlight="true">
                                    <div class="input-group-prepend input-group-append">
                                        <span class="input-group-text font-w600">
                                            <i class="fa fa-fw fa-arrow-right"></i>
                                        </span>
                                    </div>
                                    <input type="text" class="js-flatpickr form-control flatpickr-input" id="to_date"
                                        name="to_date" value="{{ $to_date }}"
                                        placeholder="{{ __('lang.select_date') }}" data-week-start="1" data-autoclose="true"
                                        data-today-highlight="true">
                                </div>
                            </div>
                        </div>
                    </div>
                    @include('admin.components.btns.search')
                </form>
            </div>
        </div>
    </div>
@section('block_options')
    @can(Actions::Manage . '_' . Resources::TransferCar)
        @if (isset($transfer_car_report))
            <x-btns.add-new btn-text="{{ __('transfer_cars.add_new') }}"
                route-create="{{ route('admin.transfer-cars.create') }}" />
        @endif
    @endcan
@endsection
<div class="block {{ __('block.styles') }}">
    {{-- <div class="block-header">
            <h3 class="block-title">{{ __('transfer_cars.total_items') }}</h3>
            <div class="block-options">
                <div class="block-options-item">
                    @can(Actions::Manage . '_' . Resources::TransferCar)
                        @if (isset($transfer_car_report))
                            <x-btns.add-new btn-text="{{ __('transfer_cars.add_new') }}"
                                route-create="{{ route('admin.transfer-cars.create') }}" />
                        @endif
                    @endcan
                </div>
            </div>
        </div> --}}
    @include('admin.components.block-header', [
        'text' => __('transfer_cars.total_items'),
        'block_icon_class' => 'icon-document',
    ])
    <div class="block-content">
        <div class="table-wrap db-scroll">
            <table class="table table-striped table-vcenter">
                <thead class="bg-body-dark">
                    <tr>
                        <th style="width: 1px;">#</th>
                        <th>@sortablelink('worksheet_no', __('transfer_cars.worksheet'))</th>
                        <th>@sortablelink('branch_id', __('transfer_cars.from_branch'))</th>
                        <th>@sortablelink('transfer_branch_id', __('transfer_cars.to_branch'))</th>
                        <th>@sortablelink('car.license_plate', __('transfer_cars.license_plate'))</th>
                        <th>@sortablelink('car.chassis_no', __('transfer_cars.chassis_no'))</th>
                        <th>@sortablelink('car_id', __('transfer_cars.car_class'))</th>
                        <th>@sortablelink('delivery_date', __('transfer_cars.pickup_return_date'))</th>
                        <th>@sortablelink('status', __('transfer_cars.status'))</th>
                        <th style="width: 100px;" class="sticky-col"></th>
                    </tr>
                </thead>
                <tbody>
                    @if (!$list->isEmpty())
                        @foreach ($list as $index => $d)
                            <tr>
                                <td>{{ $list->firstItem() + $index }}</td>
                                <td>{{ $d->worksheet_no }}</td>
                                <td>{{ $d->branch ? $d->branch->name : null }}</td>
                                <td>{{ $d->branchTransfer ? $d->branchTransfer->name : null }}</td>
                                <td>{{ $d->car ? $d->car->license_plate : null }}</td>
                                <td>{{ $d->car ? $d->car->chassis_no : null }}</td>
                                <td>{{ $d->car && $d->car->carClass ? $d->car->carClass->full_name : null }}</td>
                                <td>{{ get_thai_date_format($d->delivery_date, 'd/m/Y') }}</td>
                                <td> {!! badge_render(
                                    __('transfer_cars.status_' . $d->status . '_class'),
                                    __('transfer_cars.status_' . $d->status . '_text'),
                                    null,
                                ) !!}</td>
                                <td class="sticky-col text-center">
                                    @if (isset($d->can_edit_receive) && $d->can_edit_receive)
                                        @include('admin.components.dropdown-action', [
                                            'view_route' => route('admin.transfer-car-receives.show', [
                                                'transfer_car_receife' => $d,
                                            ]),
                                            'edit_route' => route('admin.transfer-car-receives.edit', [
                                                'transfer_car_receife' => $d,
                                            ]),
                                            'view_permission' =>
                                                Actions::View . '_' . Resources::TransferCarReceive,
                                            'manage_permission' =>
                                                Actions::Manage . '_' . Resources::TransferCarReceive,
                                        ])
                                    @elseif (isset($d->can_edit_receive) && !$d->can_edit_receive)
                                        @include('admin.components.dropdown-action', [
                                            'view_route' => route('admin.transfer-car-receives.show', [
                                                'transfer_car_receife' => $d,
                                            ]),
                                            'view_permission' =>
                                                Actions::View . '_' . Resources::TransferCarReceive,
                                            'manage_permission' =>
                                                Actions::Manage . '_' . Resources::TransferCarReceive,
                                        ])
                                    @elseif (isset($d->can_edit) && $d->can_edit)
                                        @include('admin.components.dropdown-action', [
                                            'view_route' => route('admin.transfer-cars.show', [
                                                'transfer_car' => $d,
                                            ]),
                                            'edit_route' => route('admin.transfer-cars.edit', [
                                                'transfer_car' => $d,
                                            ]),
                                            'view_permission' => Actions::View . '_' . Resources::TransferCar,
                                            'manage_permission' => Actions::Manage . '_' . Resources::TransferCar,
                                        ])
                                    @elseif(isset($d->can_edit) && !$d->can_edit)
                                        @include('admin.components.dropdown-action', [
                                            'view_route' => route('admin.transfer-cars.show', [
                                                'transfer_car' => $d,
                                            ]),
                                            'view_permission' => Actions::View . '_' . Resources::TransferCar,
                                            'manage_permission' => Actions::Manage . '_' . Resources::TransferCar,
                                        ])
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td class="text-center" colspan="10">" {{ __('lang.no_list') }} "</td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
        {!! $list->appends(\Request::except('page'))->render() !!}
    </div>
</div>
@include('admin.purchase-requisition-approve.modals.cancel-modal')
@endsection


@include('admin.components.select2-default')
@include('admin.components.sweetalert')
@include('admin.components.list-delete')
@include('admin.purchase-requisition-approve.scripts.update-status')
@include('admin.components.date-input-script')

@push('scripts')
<script>
    function cancelmodal(id) {
        document.getElementById("cancel_status").value = {{ PRStatusEnum::CANCEL }}
        document.getElementById("cancel_id").value = id;
        document.getElementById("redirect").value = "{{ route('admin.purchase-requisitions.index') }}";
        $('#modal-cancel').modal('show');
    }


    $('.duplicate_pr').click(function(e) {

        var purchase_requisition_id = $(this).attr("data-id");
        $.ajax({
            type: 'GET',
            url: "{{ route('admin.purchase-requisition.duplicate') }}",
            data: {
                purchase_requisition_id: purchase_requisition_id,
            },
            success: function(data) {
                mySwal.fire({
                    title: "{{ __('lang.store_success_title') }}",
                    text: 'คัดลอกเรียบร้อย',
                    icon: 'success',
                    confirmButtonText: "{{ __('lang.ok') }}"
                }).then(value => {
                    window.location.reload();
                });
            }
        });
    });
</script>
@endpush
