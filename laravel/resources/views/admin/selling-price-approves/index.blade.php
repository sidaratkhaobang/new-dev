@extends('admin.layouts.layout')
@section('page_title', $page_title)

@section('content')
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
                            <x-forms.select-option id="worksheet_id" :value="$worksheet_id" :list="null" :label="__('selling_prices.worksheet_no')"
                                :optionals="[
                                    'ajax' => true,
                                    'default_option_label' => $worksheet_no,
                                ]" />
                        </div>
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
                    </div>
                    @include('admin.components.btns.search')
                </form>
            </div>
        </div>
    </div>
    <div class="block {{ __('block.styles') }}">
        @include('admin.components.block-header', [
            'text' => __('transfer_cars.total_items'),
            'block_icon_class' => 'icon-document',
        ])
        <div class="block-content">
            <div class="table-wrap db-scroll">
                <table class="table table-striped table-vcenter">
                    <thead class="bg-body-dark">
                        <tr>
                            <th style="width: 5%;"></th>
                            <th style="width: 1px;">#</th>
                            <th>@sortablelink('worksheet_no', __('selling_prices.worksheet_no'))</th>
                            <th>@sortablelink('amount_car', __('selling_prices.amount_car'))</th>
                            <th class="text-center">@sortablelink('status', __('lang.status'))</th>
                            <th style="width: 100px;" class="sticky-col text-center"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @if (sizeof($list) > 0)
                            @foreach ($list as $_index => $d)
                                <tr>
                                    <td class="text-center toggle-table" style="width: 5%;">
                                        <i class="fa fa-chevron-circle-right text-muted"></i>
                                    </td>
                                    <td>{{ $list->firstItem() + $_index }}</td>
                                    <td>{{ $d->worksheet_no }}</td>
                                    <td>{{ $d->amount_car }}</td>
                                    <td class="text-center">{!! badge_render(__('selling_prices.class_' . $d->status), __('selling_prices.status_' . $d->status)) !!}</td>
                                    <td class="sticky-col text-center">
                                        @include('admin.components.dropdown-action', [
                                            'view_route' => route('admin.selling-price-approves.show', [
                                                'selling_price_approve' => $d,
                                            ]),
                                            'view_permission' =>
                                                Actions::View . '_' . Resources::SellingPriceApprove,
                                        ])
                                    </td>
                                </tr>
                                <tr style="display: none;">
                                    <td class="td-table" colspan="5">
                                        @include('admin.components.block-header', [
                                            'block_header_class' => 'ps-0',
                                            'text' => __('selling_prices.sub_list'),
                                        ])
                                        <table class="table table-striped">
                                            <thead class="bg-body-dark">
                                                <th>{{ __('cars.license_plate') }}</th>
                                                <th>{{ __('gps.car_class') }}</th>
                                                <th>{{ __('cars.chassis_no') }}</th>
                                                <th>{{ __('cars.engine_no') }}</th>
                                                <th>{{ __('selling_prices.mileage') }}</th>
                                                <th class="text-end">{{ __('selling_prices.price') }}</th>
                                                <th class="text-end">{{ __('selling_prices.vat') }}</th>
                                                <th class="text-end">{{ __('selling_prices.total') }}</th>
                                            <tbody>
                                                @if (sizeof($d->child_list) > 0)
                                                    @foreach ($d->child_list as $index => $item)
                                                        <tr>
                                                            <td>{{ $item->license_plate }}</td>
                                                            <td>{{ $item->car_class_name }}</td>
                                                            <td>{{ $item->chassis_no }}</td>
                                                            <td>{{ $item->engine_no }}</td>
                                                            <td>{{ $item->current_mileage }}</td>
                                                            <td class="text-end">{{ number_format($item->selling_price_line_price, 2, '.', ',') }}
                                                            <td class="text-end">{{ number_format($item->selling_price_line_vat, 2, '.', ',') }}
                                                            <td class="text-end">{{ number_format($item->selling_price_line_total, 2, '.', ',') }}
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                @else
                                                    <tr>
                                                        <td class="text-center" colspan="6">" {{ __('lang.no_list') }} "
                                                        </td>
                                                    </tr>
                                                @endif
                                            </tbody>
                                        </table>
                                    </td>
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td class="text-center" colspan="6">" {{ __('lang.no_list') }} "</td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
            {!! $list->appends(\Request::except('page'))->render() !!}
        </div>
    </div>
@endsection

@include('admin.components.select2-default')
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

@push('scripts')
    <script>
        $('.toggle-table').click(function() {
            $(this).parent().next('tr').toggle();
            $(this).children().toggleClass('fa fa-chevron-circle-down text-muted').toggleClass(
                'fa fa-chevron-circle-right text-muted');
        });
    </script>
@endpush
