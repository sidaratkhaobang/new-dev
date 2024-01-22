@extends('admin.layouts.layout')
@section('page_title', __('change_registrations.page_title_request'))
@section('content')
    <div class="block {{ __('block.styles') }}">
        @include('admin.components.block-header', [
            'text' => __('lang.search'),
            'block_icon_class' => 'icon-search',
            'is_toggle' => true,
        ])
        <div class="block-content">
            <div class="justify-content-between mb-4">
                <form action="" method="GET" id="form-search">
                    <div class="form-group row mb-4">
                        <div class="col-sm-3">
                            <x-forms.select-option id="request_id" :value="$request_id" :list="$request_list" :label="__('change_registrations.request_type')" />
                        </div>
                        <div class="col-sm-6">
                            <x-forms.select-option id="car_id" :value="$car" :list="null" :label="__('change_registrations.license_plate_engine_chassis')"
                                :optionals="[
                                    'select_class' => 'js-select2-custom',
                                    'ajax' => true,
                                    'default_option_label' => $car_text,
                                ]" />
                        </div>
                        <div class="col-sm-3">
                            <x-forms.select-option id="leasing" :value="$leasing" :list="null" :label="__('change_registrations.leasing')"
                                :optionals="[
                                    'select_class' => 'js-select2-custom',
                                    'ajax' => true,
                                    'default_option_label' => $leasing_text,
                                ]" />
                        </div>
                    </div>
                    <div class="form-group row mb-4">
                        <div class="col-sm-3">
                            <x-forms.select-option id="status" :value="$status" :list="$status_list"
                                :label="__('ownership_transfers.status')" />
                        </div>
                    </div>
                    @include('admin.components.btns.search')
                </form>
            </div>
        </div>
    </div>
    <div class="block {{ __('block.styles') }}">
    @section('block_options_list')
        <div class="block-options">
            @can(Actions::Manage . '_' . Resources::RequestChangeRegistration)
                <div class="block-options-item">
                    <x-btns.add-new btn-text="{{ __('change_registrations.add_new') }}"
                        route-create="{{ route('admin.request-change-registrations.create') }}" />
                </div>
            @endcan
        </div>
    @endsection
    @include('admin.components.block-header', [
        'text' => __('lang.total_list'),
        'block_option_id' => '_list',
    ])
    <div class="block-content">
        <div class="table-wrap db-scroll">
            <table class="table table-striped table-vcenter">
                <thead class="bg-body-dark">
                    <tr>

                        <th style="width: 10%;">#</th>
                        <th style="width: 12%;">@sortablelink('worksheet_no', __('ownership_transfers.leasing'))</th>
                        <th style="width: 12%;">@sortablelink('hirePurchase.contract_no', __('ownership_transfers.contract_no'))</th>
                        <th style="width: 12%;">@sortablelink('car.license_plate', __('ownership_transfers.license_plate'))</th>
                        <th style="width: 12%;">@sortablelink('car.engine_no', __('ownership_transfers.engine_no'))</th>
                        <th style="width: 12%;">@sortablelink('request_type', __('change_registrations.request_type'))</th>
                        <th style="width: 8%;">@sortablelink('status', __('ownership_transfers.status'))</th>
                        <th></th>
                        {{-- <th style="width: 100px;" class="sticky-col"></th> --}}
                    </tr>
                </thead>
                <tbody>
                    @if (!empty($lists->count()))
                        @foreach ($lists as $index => $d)
                            <tr>
                                <td>{{ $lists->firstItem() + $index }}</td>
                                <td>{{ $d->hirePurchase && $d->hirePurchase->insurance_lot && $d->hirePurchase->insurance_lot->creditor ? $d->hirePurchase->insurance_lot->creditor->name : '' }}
                                </td>
                                <td>{{ $d->hirePurchase && $d->hirePurchase->contract_no ? $d->hirePurchase->contract_no : '' }}
                                </td>
                                <td>{{ $d->car && $d->car->license_plate ? $d->car->license_plate : '' }}</td>
                                <td>{{ $d->car && $d->car->engine_no ? $d->car->engine_no : '' }}</td>
                                <td>{{ $d->type ? __('change_registrations.request_type_' . $d->type . '_text') : '' }}
                                </td>

                                <td>
                                    {!! badge_render(
                                        __('change_registrations.status_' . $d->status . '_class'),
                                        __('change_registrations.status_' . $d->status . '_text'),
                                    ) !!}
                                </td>
                                <td class="sticky-col text-center">
                                    <div class="dropdown dropleft">
                                        <button type="button" class="btn btn-sm btn-alt-secondary dropdown-toggle"
                                            id="dropdown-dropleft-dark" data-bs-toggle="dropdown" aria-haspopup="true"
                                            aria-expanded="false">
                                            <i class="fa fa-ellipsis-vertical"></i>
                                        </button>
                                        <div class="dropdown-menu" aria-labelledby="dropdown-dropleft-dark">
                                            @can(Actions::View . '_' . Resources::OwnershipTransfer)
                                                <a class="dropdown-item"
                                                    href="{{ route('admin.request-change-registrations.show', ['request_change_registration' => $d->id]) }}"><i
                                                        class="fa fa-eye me-1"></i>
                                                    {{ __('change_registrations.view') }}
                                                </a>
                                                @if (!in_array($d->status, [
                                                        ChangeRegistrationStatusEnum::WAITING_DOCUMENT,
                                                        ChangeRegistrationStatusEnum::WAITING_SEND_DLT,
                                                        ChangeRegistrationStatusEnum::PROCESSING,
                                                        ChangeRegistrationStatusEnum::SUCCESS,
                                                    ]) === 0)
                                                    <a class="dropdown-item"
                                                        href="{{ route('admin.request-change-registrations.edit', ['request_change_registration' => $d->id]) }}"><i
                                                            class="far fa-edit me-1"></i>
                                                        {{ __('lang.edit') }}
                                                    </a>
                                                @endif
                                            @endcan

                                        </div>
                                    </div>
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
        {!! $lists->appends(\Request::except('page'))->render() !!}
    </div>
</div>
@include('admin.prepare-new-cars.modals.edit-purchase')

@endsection


@include('admin.components.select2-default')
@include('admin.components.sweetalert')
@include('admin.components.list-delete')
@include('admin.components.date-input-script')

@include('admin.components.select2-ajax', [
    'id' => 'contract_no',
    'url' => route('admin.util.select2-ownership-transfer.contract-no'),
])

@include('admin.components.select2-ajax', [
    'id' => 'car_id',
    'url' => route('admin.util.select2-change-registration.car-license-plate'),
])

@include('admin.components.select2-ajax', [
    'id' => 'leasing',
    'url' => route('admin.util.select2-ownership-transfer.leasing-list'),
])
@push('scripts')
<script></script>
@endpush
