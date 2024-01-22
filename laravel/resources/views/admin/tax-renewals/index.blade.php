@extends('admin.layouts.layout')
@section('page_title', __('tax_renewals.page_title'))
@push('custom_styles')
    <style>

    </style>
@endpush
@push('styles')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/plugins/monthSelect/style.css">
@endpush
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
                    <div class="form-group row push mb-4">
                        <div class="col-sm-3">
                            {{-- <x-forms.select-option id="leasing" :value="null" :list="[]" :label="__('change_registrations.leasing')" /> --}}
                            <x-forms.select-option id="leasing" :value="$leasing" :list="null" :label="__('ownership_transfers.leasing')"
                            :optionals="[
                                'select_class' => 'js-select2-custom',
                                'ajax' => true,
                                'default_option_label' => $leasing_text,
                            ]" />
                        </div>

                        <div class="col-sm-3">
                            <x-forms.select-option id="car_class" :value="$car_class" :list="null" :label="__('change_registrations.car_class')"
                                :optionals="[
                                    'select_class' => 'js-select2-custom',
                                    'ajax' => true,
                                    'default_option_label' => $car_class_text,
                                ]" />
                        </div>
                        <div class="col-sm-6">
                            <x-forms.select-option id="car_id" :value="$car" :list="null" :label="__('change_registrations.license_plate_engine_chassis')"
                                :optionals="[
                                    'select_class' => 'js-select2-custom',
                                    'ajax' => true,
                                    'default_option_label' => $car_text,
                                ]" />
                        </div>
                    </div>
                    <div class="form-group row push mb-4">
                        <div class="col-sm-3">
                            <x-forms.date-input id="month_expire" :value="$month_expire" :label="__('tax_renewals.expire_month')"
                                :optionals="[
                                        // 'placeholder' => __('lang.select_date'),
                                    ]" />
                        </div>
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
            @can(Actions::Manage . '_' . Resources::TaxRenewal)
                <div class="block-options">
                    <div class="block-options-item">
                        <button type="button" class="btn btn-primary" onclick="openModalAvanceSelectCar()"><i
                                class="icon-menu-money"></i>
                            {{ __('ownership_transfers.withdraw_avance') }}</button>
                    </div>
                    <div class="block-options-item">
                        <button type="button" class="btn btn-primary" onclick="openModalFaceSheetSelectCar()"><i
                                class="icon-printer"></i>
                            {{ __('registers.face_sheet') }}</button>
                    </div>
                    <div class="block-options-item">
                        <button type="button" class="btn btn-primary" onclick="openModalFaceSheetSelectCar()"><i
                                class="icon-send"></i>
                            {{ __('tax_renewals.send_tax') }}</button>
                    </div>
                    <div class="btn-group" style="position: sticky; min-width:270px;">
                        <button type="button" class="btn btn-primary dropdown-toggle-split" aria-expanded="false"
                            style="width: 220px;"><i
                                class="icon-info-circle"></i>{{ __('ownership_transfers.request_document') }}</button>
                        <button type="button" class="btn btn-primary dropdown-toggle dropdown-toggle-split arrow"
                            style="min-width: 5px !important;" data-bs-toggle="dropdown" aria-expanded="false">
                            <span class="visually-hidden">Toggle Dropdown</span>
                        </button>

                        <ul class="dropdown-menu">
                            <li><button class="dropdown-item btn-request" onclick="openModalTemplateSelectCar()">ดาวน์โหลด
                                    Templete</button>
                            </li>
                            <li>
                                <label for="upload" class="dropdown-item btn-request file"
                                    style="cursor: pointer;">อัปโหลดไฟล์</label>
                                <input id="upload" type="file" name="file[]"
                                    style="position: absolute; top: -9999px; left: -9999px; overflow: hidden;">
                            </li>
                        </ul>
                    </div>

                    <div class="btn-group" style="position: sticky;left: 100%; min-width:270px;">
                        <button type="button" class="btn btn-primary dropdown-toggle-split" aria-expanded="false"
                            style="width: 220px;"><i class="icon-edit"></i>{{ __('registers.edit_multiple') }}</button>
                        <button type="button" class="btn btn-primary dropdown-toggle dropdown-toggle-split arrow"
                            style="min-width: 5px !important;" data-bs-toggle="dropdown" aria-expanded="false">
                            <span class="visually-hidden">Toggle Dropdown</span>
                        </button>

                        <ul class="dropdown-menu">
                            <li><button class="dropdown-item btn-request" onclick="openModalTemplateSelectCar()">ดาวน์โหลด
                                    Templete</button>
                            </li>
                            <li>
                                <label for="upload" class="dropdown-item btn-request file"
                                    style="cursor: pointer;">อัปโหลดไฟล์</label>
                                <input id="upload" type="file" name="file[]"
                                    style="position: absolute; top: -9999px; left: -9999px; overflow: hidden;">
                            </li>
                        </ul>
                    </div>
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
                        <th style="width: 12%;">@sortablelink('car_class', __('tax_renewals.car_class'))</th>
                        <th style="width: 12%;">@sortablelink('license_plate', __('ownership_transfers.license_plate'))</th>
                        <th style="width: 12%;">@sortablelink('engine_no', __('ownership_transfers.engine_no'))</th>
                        <th style="width: 12%;">@sortablelink('chassis_no', __('tax_renewals.chassis_no'))</th>
                        <th style="width: 12%;">@sortablelink('expire_date', __('tax_renewals.expire_date'))</th>
                        <th style="width: 8%;">@sortablelink('status', __('ownership_transfers.status'))</th>
                        <th></th>
                        {{-- <th style="width: 100px;" class="sticky-col">{{ __('lang.tools') }}</th> --}}
                    </tr>
                </thead>
                <tbody>
                    @if (!empty($lists->count()))
                        @foreach ($lists as $index => $d)
                            <tr>
                                <td>{{ $lists->firstItem() + $index }}</td>
                                <td>{{ $d->car && $d->car->creditor ? $d->car->creditor->name : '' }}</td>
                                <td>{{ $d->car && $d->car->carClass && $d->car->carClass->full_name ? $d->car->carClass->full_name : '' }}
                                </td>
                                <td>{{ $d->car && $d->car->license_plate ? $d->car->license_plate : '' }}</td>
                                <td>{{ $d->car && $d->car->engine_no ? $d->car->engine_no : '' }}</td>
                                <td>{{ $d->car && $d->car->chassis_no ? $d->car->chassis_no : '' }}</td>
                                <td>{{ $d->car_tax_exp_date }}</td>

                                <td>
                                    {!! badge_render(
                                        __('tax_renewals.status_' . $d->status . '_class'),
                                        __('tax_renewals.status_' . $d->status . '_text'),
                                    ) !!}
                                </td>
                                <td class="sticky-col text-center">
                                    <div class="dropdown dropleft">
                                        <button type="button" class="btn btn-sm btn-alt-secondary dropdown-toggle"
                                            id="dropdown-dropleft-dark" data-bs-toggle="dropdown"
                                            aria-haspopup="true" aria-expanded="false">
                                            <i class="fa fa-ellipsis-vertical"></i>
                                        </button>
                                        <div class="dropdown-menu" aria-labelledby="dropdown-dropleft-dark">
                                            @can(Actions::View . '_' . Resources::TaxRenewal)
                                                @if (in_array($d->status, [TaxRenewalStatusEnum::PREPARE_DOCUMENT]))
                                                    <a class="dropdown-item"
                                                        href="{{ route('admin.tax-renewals.show', ['tax_renewal' => $d->id]) }}"><i
                                                            class="fa fa-eye me-1"></i>
                                                        {{ __('ownership_transfers.view') }}
                                                    </a>
                                                    <a class="dropdown-item"
                                                        href="{{ route('admin.tax-renewals.edit', ['tax_renewal' => $d->id]) }}"><i
                                                            class="far fa-edit me-1"></i>
                                                        {{ __('lang.edit') }}
                                                    </a>
                                                @elseif (in_array($d->status, [TaxRenewalStatusEnum::WAITING_SEND_TAX]))
                                                    <a class="dropdown-item"
                                                        href="{{ route('admin.tax-renewals.show-waiting-send-tax', ['tax_renewal' => $d->id]) }}"><i
                                                            class="fa fa-eye me-1"></i>
                                                        {{ __('ownership_transfers.view') }}
                                                    </a>
                                                    <a class="dropdown-item"
                                                        href="{{ route('admin.tax-renewals.edit-waiting-send-tax', ['tax_renewal' => $d->id]) }}"><i
                                                            class="far fa-edit me-1"></i>
                                                        {{ __('lang.edit') }}
                                                    </a>
                                                @elseif (in_array($d->status, [TaxRenewalStatusEnum::RENEWING]))
                                                    <a class="dropdown-item"
                                                        href="{{ route('admin.tax-renewals.show-taxing', ['tax_renewal' => $d->id]) }}"><i
                                                            class="fa fa-eye me-1"></i>
                                                        {{ __('ownership_transfers.view') }}
                                                    </a>
                                                    <a class="dropdown-item"
                                                        href="{{ route('admin.tax-renewals.edit-taxing', ['tax_renewal' => $d->id]) }}"><i
                                                            class="far fa-edit me-1"></i>
                                                        {{ __('lang.edit') }}
                                                    </a>
                                                @elseif (in_array($d->status, [TaxRenewalStatusEnum::WAITING_TAX_REGISTER_BOOK]))
                                                    <a class="dropdown-item"
                                                        href="{{ route('admin.tax-renewals.show-waiting-send-tax-register-book', ['tax_renewal' => $d->id]) }}"><i
                                                            class="fa fa-eye me-1"></i>
                                                        {{ __('ownership_transfers.view') }}
                                                    </a>
                                                    <a class="dropdown-item"
                                                        href="{{ route('admin.tax-renewals.edit-waiting-send-tax-register-book', ['tax_renewal' => $d->id]) }}"><i
                                                            class="far fa-edit me-1"></i>
                                                        {{ __('lang.edit') }}
                                                    </a>
                                                @elseif (in_array($d->status, [TaxRenewalStatusEnum::SUCCESS]))
                                                    <a class="dropdown-item"
                                                        href="{{ route('admin.tax-renewals.show-waiting-send-tax-register-book', ['tax_renewal' => $d->id]) }}"><i
                                                            class="fa fa-eye me-1"></i>
                                                        {{ __('ownership_transfers.view') }}
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
    'url' => route('admin.util.select2-tax-renewal.car-license-plate'),
])

@include('admin.components.select2-ajax', [
    'id' => 'car_class',
    'url' => route('admin.util.select2-tax-renewal.car-class'),
])

@include('admin.components.select2-ajax', [
    'id' => 'leasing',
    'url' => route('admin.util.select2-ownership-transfer.leasing-list'),
])
@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/flatpickr@latest/dist/flatpickr.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr@latest/dist/plugins/monthSelect/index.js"></script>


<script>
    jQuery(function() {
        let monthExpireInput = document.querySelector("#month_expire");
        let defaultDate = "{{ $month_expire ?? '' }}";

        let flatpickrInstance = flatpickr(monthExpireInput, {
            plugins: [
                new monthSelectPlugin({
                    dateFormat: "m/Y",
                    shorthand: true,
                    theme: "light",
                })
            ],
            onReady: function(selectedDates, dateStr, instance) {
                instance.calendarContainer.classList.add('flatpickr-monthYear');
            },
            onChange: function(selectedDates, dateStr) {

            },
            defaultDate: defaultDate,
        });
        if (this.value) {
            flatpickrInstance.setDate(this.value);
        }


    });

</script>
@endpush
