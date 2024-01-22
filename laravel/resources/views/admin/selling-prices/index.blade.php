@extends('admin.layouts.layout')
@section('page_title', $page_title)
@push('custom_styles')
    <style>
        .btn-group {
            background-color: #ffffff;
            color: #000000;
        }

        .input-group-text {
            padding: 0.7rem .75rem;
            background-color: transparent;
            border-radius: 0;
            color: #6c757d;
        }
    </style>
@endpush
@section('content')
    @include('admin.selling-prices.sections.btn-group')
    <div class="block {{ __('block.styles') }}">
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
                            <x-forms.select-option id="car_id" :value="$car_id" :list="null" :label="__('driving_jobs.license_plate_chassis_no')"
                                :optionals="[
                                    'ajax' => true,
                                    'default_option_label' => $car_name,
                                ]" />
                        </div>
                        <div class="col-sm-3">
                            <x-forms.select-option id="car_class_id" :value="$car_class_id" :list="null" :label="__('gps.car_class')"
                                :optionals="[
                                    'ajax' => true,
                                    'default_option_label' => $car_class_name,
                                ]" />
                        </div>
                        <div class="col-sm-3">
                            <x-forms.select-option id="status_id" :value="$status_id" :list="$status_list" :label="__('lang.status')" />
                        </div>
                        <div class="col-sm-3">
                            <x-forms.select-option id="worksheet_id" :value="$worksheet_id" :list="null"
                                :label="__('selling_prices.worksheet_no')" :optionals="[
                                    'ajax' => true,
                                    'default_option_label' => $worksheet_no,
                                ]" />
                        </div>
                    </div>
                    <div class="form-group row push mb-4">
                        <div class="col-sm-3">
                            <x-forms.select-option id="year_id" :value="$year_id" :list="null" :label="__('car_classes.manufacturing_year')"
                                :optionals="[
                                    'ajax' => true,
                                    'default_option_label' => $year,
                                ]" />
                        </div>
                        <div class="col-sm-3">
                            <x-forms.select-option id="car_color_id" :value="$car_color_id" :list="null"
                                :label="__('selling_prices.car_color')" :optionals="[
                                    'ajax' => true,
                                    'default_option_label' => $car_color_name,
                                ]" />
                        </div>
                        <div class="col-sm-3">
                            <label class="text-start col-form-label"
                                for="from_registered">{{ __('cars.registration_date') }}</label>
                            <div class="form-group">
                                <div class="input-daterange input-group" data-date-format="mm/dd/yyyy" data-week-start="1"
                                    data-autoclose="true" data-today-highlight="true">
                                    <input type="text" class="js-flatpickr form-control flatpickr-input"
                                        id="from_registered" name="from_registered" value="{{ $from_registered }}"
                                        placeholder="{{ __('lang.select_date') }}" data-week-start="1" data-autoclose="true"
                                        data-today-highlight="true">
                                    <div class="input-group-prepend input-group-append">
                                        <span class="input-group-text font-w600">
                                            <i class="fa fa-fw fa-arrow-right"></i>
                                        </span>
                                    </div>
                                    <input type="text" class="js-flatpickr form-control flatpickr-input"
                                        id="to_registered" name="to_registered" value="{{ $to_registered }}"
                                        placeholder="{{ __('lang.select_date') }}" data-week-start="1" data-autoclose="true"
                                        data-today-highlight="true">
                                </div>
                            </div>
                        </div>
                        {{-- <div class="col-sm-3">
                            <label class="text-start col-form-label"
                                for="from_contract_end">{{ __('long_term_rentals.contract_end_date') }}</label>
                            <div class="form-group">
                                <div class="input-daterange input-group" data-date-format="mm/dd/yyyy" data-week-start="1"
                                    data-autoclose="true" data-today-highlight="true">
                                    <input type="text" class="js-flatpickr form-control flatpickr-input"
                                        id="from_contract_end" name="from_contract_end" value=""
                                        placeholder="{{ __('lang.select_date') }}" data-week-start="1" data-autoclose="true"
                                        data-today-highlight="true">
                                    <div class="input-group-prepend input-group-append">
                                        <span class="input-group-text font-w600">
                                            <i class="fa fa-fw fa-arrow-right"></i>
                                        </span>
                                    </div>
                                    <input type="text" class="js-flatpickr form-control flatpickr-input"
                                        id="to_contract_end" name="to_contract_end" value=""
                                        placeholder="{{ __('lang.select_date') }}" data-week-start="1" data-autoclose="true"
                                        data-today-highlight="true">
                                </div>
                            </div>
                        </div> --}}
                    </div>
                    @include('admin.components.btns.search')
                </form>
            </div>
        </div>
    </div>
    <div class="block {{ __('block.styles') }}">
    @section('block_options_btn')
        @can(Actions::Manage . '_' . Resources::SellingPrice)
            <button class="btn btn-primary" onclick="openModalSalePrice()"><i class="fa fa-comment-dollar"></i>
                {{ __('selling_prices.sale_price') }}</button>
        @endcan
        @can(Actions::Manage . '_' . Resources::SellingPrice)
            <button class="btn btn-primary" onclick="openModalRequestApprove()"><i class="far fa-file"></i>
                {{ __('selling_prices.request_approve') }}</button>
        @endcan
    @endsection
    @include('admin.components.block-header', [
        'text' => __('transfer_cars.total_items'),
        'block_icon_class' => 'icon-document',
        'block_option_id' => '_btn',
    ])
    <div class="block-content">
        <div class="table-wrap db-scroll">
            <table class="table table-striped table-vcenter">
                <thead class="bg-body-dark">
                    <tr>
                        <th class="text-center" style="width: 70px;">
                            <div class="form-check d-inline-block">
                                <input class="form-check-input" type="checkbox" value="" id="selectAll"
                                    name="selectAll">
                                <label class="form-check-label" for="selectAll"></label>
                            </div>
                        </th>
                        <th style="width: 1px;">#</th>
                        <th style="width: 10%;">@sortablelink('worksheet_no', __('selling_prices.worksheet_no'))</th>
                        <th style="width: 10%;">@sortablelink('license_plate', __('cars.license_plate'))</th>
                        <th style="width: 10%;">@sortablelink('car_class_name', __('gps.car_class'))</th>
                        <th style="width: 10%;">@sortablelink('chassis_no', __('cars.chassis_no'))</th>
                        <th style="width: 10%;">@sortablelink('engine_no', __('cars.engine_no'))</th>
                        <th style="width: 10%;">@sortablelink('manufacturing_year', __('car_classes.manufacturing_year'))</th>
                        <th style="width: 10%;">@sortablelink('car_color_name', __('selling_prices.car_color'))</th>
                        <th style="width: 10%;" class="text-center">@sortablelink('registered_date', __('cars.registration_date'))</th>
                        <th style="width: 10%;" class="text-center">@sortablelink('status', __('lang.status'))</th>
                        <th style="width: 100px;" class="sticky-col text-center"></th>
                    </tr>
                </thead>
                <tbody>
                    @if (sizeof($list) > 0)
                        @foreach ($list as $index => $d)
                            <tr>
                                <td class="text-center">
                                    <div class="form-check d-inline-block">
                                        <input class="form-check-input form-check-input-each" type="checkbox"
                                            value="" id="row_{{ $d->id }}" name="row_{{ $d->id }}">
                                        <label class="form-check-label" for="row_{{ $d->id }}"></label>
                                    </div>
                                </td>
                                <td>{{ $list->firstItem() + $index }}</td>
                                <td>{{ $d->worksheet_no }}</td>
                                <td>{{ $d->license_plate }}</td>
                                <td>{{ $d->car_class_name }}</td>
                                <td>{{ $d->chassis_no }}</td>
                                <td>{{ $d->engine_no }}</td>
                                <td>{{ $d->manufacturing_year }}</td>
                                <td>{{ $d->car_color_name }}</td>
                                <td>{{ $d->registered_date ? get_thai_date_format($d->registered_date, 'd/m/Y') : null }}
                                </td>
                                <td>{!! badge_render(__('selling_prices.class_' . $d->status), __('selling_prices.status_' . $d->status)) !!}</td>
                                <td class="sticky-col text-center">
                                    @if (strcmp($d->status, SellingPriceStatusEnum::PRE_SALE_PRICE) == 0)
                                        @include('admin.components.dropdown-action', [
                                            'view_route' => route('admin.selling-prices.show', [
                                                'selling_price' => $d,
                                            ]),
                                            'edit_route' => route('admin.selling-prices.edit', [
                                                'selling_price' => $d,
                                            ]),
                                            'view_permission' => Actions::View . '_' . Resources::SellingPrice,
                                            'manage_permission' => Actions::Manage . '_' . Resources::SellingPrice,
                                        ])
                                    @elseif(in_array($d->status, [SellingPriceStatusEnum::CONFIRM, SellingPriceStatusEnum::REJECT]))
                                        <div class="btn-group">
                                            <div class="col-sm-12">
                                                <div class="dropdown dropleft">
                                                    <button type="button"
                                                        class="btn btn-sm btn-alt-secondary dropdown-toggle"
                                                        id="dropdown-dropleft-dark" data-bs-toggle="dropdown"
                                                        aria-haspopup="true" aria-expanded="false">
                                                        <i class="fa fa-ellipsis-vertical"></i>
                                                    </button>
                                                    <div class="dropdown-menu"
                                                        aria-labelledby="dropdown-dropleft-dark">
                                                        @can(Actions::View . '_' . Resources::SellingPrice)
                                                            <a class="dropdown-item"
                                                                href="{{ route('admin.selling-prices.show', ['selling_price' => $d]) }}">
                                                                <i class="fa fa-eye me-1"></i> ดูข้อมูล</a>
                                                        @endcan
                                                        <a class="dropdown-item btn-new-approve-modal"
                                                            data-id="{{ $d->id }}" href="javascript:void(0)">
                                                            <i class="fa fa-arrow-rotate-left"></i> ขออนุมัติราคาใหม่
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @else
                                        @include('admin.components.dropdown-action', [
                                            'view_route' => route('admin.selling-prices.show', [
                                                'selling_price' => $d,
                                            ]),
                                            'view_permission' => Actions::View . '_' . Resources::SellingPrice,
                                        ])
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td class="text-center" colspan="12">" {{ __('lang.no_list') }} "</td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
        {!! $list->appends(\Request::except('page'))->render() !!}
    </div>
    @include('admin.selling-prices.modals.sale-price-modal')
    @include('admin.selling-prices.modals.request-approve-modal')
    @include('admin.selling-prices.modals.sale-price-new-modal')
</div>
@endsection

@include('admin.components.select2-default')
@include('admin.components.date-input-script')
@include('admin.components.sweetalert')

@include('admin.components.select2-ajax', [
    'id' => 'car_id',
    'url' => route('admin.util.select2-car-auction.sale-price-license-plates'),
])

@include('admin.components.select2-ajax', [
    'id' => 'car_class_id',
    'url' => route('admin.util.select2-car-auction.sale-price-car-class'),
])

@include('admin.components.select2-ajax', [
    'id' => 'worksheet_id',
    'url' => route('admin.util.select2-car-auction.sale-price-worksheets'),
])

@include('admin.components.select2-ajax', [
    'id' => 'year_id',
    'url' => route('admin.util.select2-car-auction.sale-price-car-class-year'),
])

@include('admin.components.select2-ajax', [
    'id' => 'car_color_id',
    'url' => route('admin.util.select2-car-auction.sale-price-car-color'),
])

@push('scripts')
<script>
    $(document).ready(function() {
        var $selectAll = $('#selectAll');
        var $table = $('.table');
        var $tdCheckbox = $table.find('tbody input:checkbox');
        var tdCheckboxChecked = 0;

        $selectAll.on('click', function() {
            $tdCheckbox.prop('checked', this.checked);
        });

        $tdCheckbox.on('change', function(e) {
            tdCheckboxChecked = $table.find('tbody input:checkbox:checked').length;
            $selectAll.prop('checked', (tdCheckboxChecked === $tdCheckbox.length));
        })
    });

    function openModalSalePrice() {
        var enum_sale_price = '{{ \App\Enums\SellingPriceStatusEnum::PRE_SALE_PRICE }}';
        var check_list = @json($list);
        var arr_check = [];
        salePriceVue.removeAll();
        if (check_list.data.length > 0) {
            check_list.data.forEach(function(item, index) {
                this_checkbox = $('input[name="row_' + item.id + '"]');
                var is_check = this_checkbox.prop('checked');
                if (is_check) {
                    if (item.status == enum_sale_price) {
                        salePriceVue.addByDefault(item);
                    }
                }
            });
        }
        $('#modal-sale-price').modal('show');
    }

    function openModalRequestApprove() {
        var enum_request_approve = '{{ \App\Enums\SellingPriceStatusEnum::REQUEST_APPROVE }}';
        var check_list = @json($list);
        var arr_check = [];
        requestApprove.removeAll();
        if (check_list.data.length > 0) {
            check_list.data.forEach(function(item, index) {
                this_checkbox = $('input[name="row_' + item.id + '"]');
                var is_check = this_checkbox.prop('checked');
                if (is_check) {
                    if (item.status == enum_request_approve) {
                        requestApprove.addByDefault(item);
                    }
                }
            });
        }
        $('#modal-request-approve').modal('show');
    }

    $('.btn-new-approve-modal').on('click', function() {
        var id = $(this).attr('data-id');
        document.getElementById("sale_car_id").value = id;
        $('#modal-sale-price-new').modal('show');
    });
</script>
@endpush
