@extends('admin.layouts.layout')
@section('page_title', __('long_term_rentals.page_title'))

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
                            <label for="s" class="text-start col-form-label">{{ __('lang.search_label') }}</label>
                            <input type="text" id="s" name="s" class="form-control"
                                placeholder="{{ __('lang.search_placeholder') }}" value="{{ $s }}">
                        </div>
                        <div class="col-sm-3">
                            <x-forms.select-option id="worksheet_no" :value="$worksheet_id" :list="$worksheet_list"
                                :label="__('long_term_rentals.worksheet_no')" />
                        </div>
                        <div class="col-sm-3">
                            <x-forms.select-option id="customer" :value="$customer_id" :list="$customer_list" :label="__('long_term_rentals.customer')" />
                        </div>
                        <div class="col-sm-3">
                            <x-forms.select-option id="won_auction" :value="$won_auction_status_id" :list="$won_auction_list" :label="__('long_term_rentals.won_auction')" />
                        </div>
                    </div>
                    <div class="form-group row push mb-4">
                        <div class="col-sm-3">
                            <x-forms.select-option id="spec_status" :value="$spec_status_id" :list="$spec_status_list"
                                :label="__('long_term_rentals.spec_status')" />
                        </div>
                        <div class="col-sm-3">
                            <x-forms.select-option id="status" :value="$status_id" :list="$status_list"
                                :label="__('long_term_rentals.lt_rental_status')" />
                        </div>
                        <div class="col-sm-3">
                            <x-forms.select-option id="quotation_no" :value="$quotation_id" :list="$quotation_list"
                                :label="__('long_term_rentals.quotation_no')" />
                        </div>
                        {{-- <div class="col-sm-3">
                            <x-forms.select-option id="lt_rental_type" :value="$lt_rental_type" :list="$lt_rental_type_list" :label="__('long_term_rentals.job_type')" />
                        </div> --}}
                    </div>
                    {{-- <div class="form-group row push mb-4">
                        <div class="col-sm-3">
                            <label class="text-start col-form-label" for="from_offer_date">{{ __('long_term_rentals.offer_date') }}</label>
                            <div class="form-group">
                                <div class="input-daterange input-group" data-date-format="mm/dd/yyyy" data-week-start="1" data-autoclose="true" data-today-highlight="true">
                                    <input type="text" class="js-flatpickr form-control flatpickr-input" id="from_offer_date" name="from_offer_date" value="{{ $from_offer_date }}" placeholder="{{ __('lang.select_date') }}" data-week-start="1" data-autoclose="true" data-today-highlight="true">
                                    <div class="input-group-prepend input-group-append">
                                        <span class="input-group-text font-w600">
                                            <i class="fa fa-fw fa-arrow-right"></i>
                                        </span>
                                    </div>
                                    <input type="text" class="js-flatpickr form-control flatpickr-input" id="to_offer_date" name="to_offer_date" value="{{ $to_offer_date }}" placeholder="{{ __('lang.select_date') }}" data-week-start="1" data-autoclose="true" data-today-highlight="true">
                                </div>
                            </div>
                        </div>
                    </div> --}}
                    @include('admin.components.btns.search')
                </form>
            </div>
        </div>
    </div>
@section('block_options_1')
    @can(Actions::Manage . '_' . Resources::LongTermRental)
        <x-btns.add-new btn-text="{{ __('long_term_rentals.add_new') }}"
            route-create="{{ route('admin.long-term-rentals.create') }}" />
    @endcan
@endsection
<div class="block {{ __('block.styles') }}">
    @include('admin.components.block-header', [
        'text' => __('transfer_cars.total_items'),
        'block_icon_class' => 'icon-document',
        'block_option_id' => '_1',
    ])
    <div class="block-content">
        <div class="table-wrap db-scroll">
            <table class="table table-striped table-vcenter">
                <thead class="bg-body-dark">
                    <tr>
                        <td style="width: 2px;"></td>
                        <td style="width: 1px;">#</td>
                        <th style="width: 13%;">@sortablelink('worksheet_no', __('long_term_rentals.worksheet_no'))</th>
                        <th style="width: 13%;">@sortablelink('lt_rental_type_name', __('long_term_rentals.lt_rental_type'))</th>
                        <th style="width: 18%;">@sortablelink('customer_name', __('long_term_rentals.customer'))</th>
                        <th class="text-center" style="width: 13%;">@sortablelink('status', __('long_term_rentals.lt_rental_status'))</th>
                        <th style="width: 13%;">@sortablelink('won_auction', __('long_term_rentals.won_auction'))</th>
                        {{-- <th style="width: 13%;">@sortablelink('spec_status', __('long_term_rentals.spec_status'))</th> --}}
                        <th style="width: 13%;">@sortablelink('qt_no', __('long_term_rentals.quotation_no'))</th>
                        <th style="width: 100px;" class="sticky-col text-center"></th>
                    </tr>
                </thead>
                <tbody>
                @if(!empty($lists->count()))
                    @foreach ($lists as $index => $d)
                        <tr>

                            @if (in_array($d->status, [LongTermRentalStatusEnum::COMPLETE]))
                                <td class="text-center toggle-table" style="width: 30px">
                                    <i class="fa fa-angle-right text-muted"></i>
                                </td>
                            @else
                                <td></td>
                            @endif
                            <td>{{ $lists->firstItem() + $index }}</td>
                            <td>{{ $d->worksheet_no }}</td>
                            <td>
                                {{ $d->lt_rental_type_name }}
                            </td>
                            <td>{{ $d->customer_name }}
                            </td>
                            {{-- สถานะใบขอเช่า --}}
                            <td class="text-center">
                                {!! badge_render(
                                    __('long_term_rentals.lt_rental_status_class_' . $d->status),
                                    __('long_term_rentals.lt_rental_status_' . $d->status),
                                ) !!}
                            </td>
                            {{-- สถานะงานประมูล --}}
                            <td class="text-center">
                                @if (in_array($d->won_auction, [AuctionResultEnum::WON, AuctionResultEnum::LOSE, AuctionResultEnum::WAITING]))
                                    {!! badge_render(
                                        __('long_term_rentals.won_auction_class_' . $d->won_auction),
                                        __('long_term_rentals.won_auction_' . $d->won_auction),
                                    ) !!}
                                @else
                                    -
                                @endif
                            </td>
                            {{-- สถานะตีสเปค --}}
                            {{-- <td>
                                    @if (!in_array($d->status, [LongTermRentalStatusEnum::NEW]))
                                        {!! badge_render(
                                            __('long_term_rentals.spec_status_class_' . $d->spec_status),
                                            __('long_term_rentals.spec_status_' . $d->spec_status),
                                        ) !!}
                                    @else
                                        -
                                    @endif
                                </td> --}}

                            <td>
                                @if ($d->quotation_id)
                                    <a href="{{ route('admin.quotations.long-term-rental-pdf', ['quotation' => $d->quotation_id]) }}"
                                       target="_blank">{{ $d->qt_no }}</a>
                                @else
                                    -
                                @endif
                            </td>
                            <td class="sticky-col text-center" style="width: 100px;">
                                @if (in_array($d->status, [LongTermRentalStatusEnum::COMPLETE]))
                                    @include('admin.components.dropdown-action', [
                                        'view_route' => route('admin.long-term-rentals.show', [
                                            'long_term_rental' => $d,
                                        ]),
                                        'view_permission' => Actions::View . '_' . Resources::LongTermRental,
                                    ])
                                @else
                                    <div class="dropdown dropleft">
                                        <button type="button" class="btn btn-sm btn-alt-secondary dropdown-toggle"
                                                id="dropdown-dropleft-dark" data-bs-toggle="dropdown" aria-haspopup="true"
                                                aria-expanded="false">
                                            <i class="fa fa-ellipsis-vertical"></i>
                                        </button>
                                        <div class="dropdown-menu" aria-labelledby="dropdown-dropleft-dark">
                                            @can(Actions::View . '_' . Resources::LongTermRental)
                                                <a class="dropdown-item"
                                                   href="{{ route('admin.long-term-rentals.show', ['long_term_rental' => $d]) }}"><i
                                                        class="fa fa-eye me-1"></i>ดูข้อมูล</a>
                                            @endcan

                                            @can(Actions::Manage . '_' . Resources::LongTermRental)
                                                <a class="dropdown-item"
                                                   href="{{ route('admin.long-term-rentals.edit', ['long_term_rental' => $d]) }}"><i
                                                        class="far fa-edit me-1"></i>แก้ไข</a>
                                            @endcan
                                            {{-- @if (in_array($d->quotation_status, [QuotationStatusEnum::CONFIRM]) && in_array($d->status, [LongTermRentalStatusEnum::QUOTATION]))
                                                    @can(Actions::View . '_' . Resources::LongTermRental)
                                                        <a onclick="openModalPrintRentalRequisition('{{ $d->id }}')" href="#"
                                                            class="dropdown-item"><i
                                                            class="fa fa-upload"></i>
                                                            {{ __('long_term_rentals.requisition_pdf') }}
                                                        </a>
                                                    @endcan
                                                @endif --}}
                                        </div>
                                    </div>
                                @endif
                            </td>
                        </tr>
                        <tr style="display: none;">
                            <td></td>
                            <td class="td-table" colspan="7">
                                <table class="table table-striped">
                                    <thead class="bg-body-dark">
                                    <th style="width: 10%" class="text-center">
                                        {{ __('long_term_rentals.order_status') }}</th>
                                    <th style="width: 10%" class="text-center">
                                        {{ __('long_term_rentals.delivery_car_status') }} </th>
                                    <th style="width: 10%" class="text-center">
                                        {{ __('long_term_rentals.install_equipment_status') }}</th>
                                    <th style="width: 10%" class="text-center">
                                        {{ __('long_term_rentals.finance_status') }}</th>
                                    <th style="width: 10%" class="text-center">
                                        {{ __('long_term_rentals.act_status') }}</th>
                                    <th style="width: 10%" class="text-center">
                                        {{ __('long_term_rentals.insurance_status') }}</th>
                                    <th style="width: 10%" class="text-center">
                                        {{ __('long_term_rentals.register_car_status') }}</th>
                                    <th style="width: 10%" class="text-center">
                                        {{ __('long_term_rentals.delivery_customer_status') }}</th>
                                    {{-- <th style="width: 100px;" class="sticky-col text-center">{{ __('lang.tools') }} --}}
                                    </th>
                                    </thead>
                                    <tbody>
                                    {{-- @if (sizeof($d->bills) > 0)
                                            @foreach ($d->bills as $index => $item) --}}
                                    <tr>
                                        <td class="text-center">
                                            @if (isset($d->order_status))
                                                @if (strcmp($d->order_status, LongTermRentalProgressStatusEnum::PROCESSING) === 0)
                                                    {!! badge_render(
                                                        __('long_term_rentals.order_status_class_' . $d->order_status),
                                                        __('long_term_rentals.order_status_' . $d->order_status) . ' ' . $d->amount . '/' . $d->total_amount,
                                                    ) !!}
                                                @else
                                                    {!! badge_render(
                                                        __('long_term_rentals.order_status_class_' . $d->order_status),
                                                        __('long_term_rentals.order_status_' . $d->order_status),
                                                    ) !!}
                                                @endif
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            @if (isset($d->delivery_car_status))
                                                @if (strcmp($d->delivery_car_status, LongTermRentalProgressStatusEnum::DELIVERING) === 0)
                                                    {!! badge_render(
                                                        __('long_term_rentals.delivery_car_status_class_' . $d->delivery_car_status),
                                                        __('long_term_rentals.delivery_car_status_' . $d->delivery_car_status) .
                                                            ' ' .
                                                            $d->delivery_car_amount .
                                                            '/' .
                                                            $d->total_amount,
                                                    ) !!}
                                                @else
                                                    {!! badge_render(
                                                        __('long_term_rentals.delivery_car_status_class_' . $d->delivery_car_status),
                                                        __('long_term_rentals.delivery_car_status_' . $d->delivery_car_status),
                                                    ) !!}
                                                @endif
                                            @endif
                                        </td>
                                        <td class="text-center">


                                            @if (isset($d->install_equipment_status))
                                                @if (strcmp($d->install_equipment_status, InstallEquipmentStatusEnum::INSTALL_IN_PROCESS) === 0)
                                                    {!! badge_render(
                                                        __('long_term_rentals.install_equipment_status_class_' . $d->install_equipment_status),
                                                        __('long_term_rentals.install_equipment_status_' . $d->install_equipment_status) .
                                                            ' ' .
                                                            $d->install_equipment_amount .
                                                            '/' .
                                                            $d->install_equipment_total_amount,
                                                    ) !!}
                                                @else
                                                    {!! badge_render(
                                                        __('long_term_rentals.install_equipment_status_class_' . $d->install_equipment_status),
                                                        __('long_term_rentals.install_equipment_status_' . $d->install_equipment_status),
                                                    ) !!}
                                                @endif
                                            @endif
                                        </td>
                                        <td class="text-center"></td>
                                        <td class="text-center"></td>
                                        <td class="text-center"></td>
                                        <td class="text-center"></td>
                                        <td class="text-center"></td>
                                    </tr>
                                    </tbody>
                                </table>
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
@endsection

@include('admin.components.select2-default')
@include('admin.components.sweetalert')
@include('admin.components.list-delete')
@include('admin.components.date-input-script')
{{-- @include('admin.long-term-rentals.scripts.lt-select2-script') --}}

@push('scripts')
<script>
    $('.toggle-table').click(function() {
        $(this).parent().next('tr').toggle();
        $(this).children().toggleClass('fa fa-angle-down text-muted').toggleClass(
            'fa fa-angle-right text-muted');
    });
</script>
@endpush
