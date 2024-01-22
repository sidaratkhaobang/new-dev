@extends('admin.layouts.layout')
@section('page_title', $page_title)
@push('custom_styles')
    <style>
        .input-group-date {
            display: flex;
            align-items: center;
            font-size: 1rem;
            font-weight: 400;
            line-height: 1.5;
            text-align: center;
            white-space: nowrap;
            border: 1px solid #d1d8ea;
            padding: 0.7rem .75rem;
            background-color: transparent;
            border-radius: 0;
            color: #6c757d;
        }

        .seperator {
            font-size: xx-large;
            color: #CBD4E1;
            vertical-align: sub;
            font-weight: 100;
        }

        .total-size {
            font-size: 28px;
            font-weight: 700;
            line-height: 16px;
            letter-spacing: 0em;
        }

        .block-rounded {
            border-radius: 0.25rem !important;
            border: 1px solid rgba(0, 0, 0, 0.2);
            border-style: none;
            padding: 16px;
            background-color: #F6F8FC;
            margin: 16px;
        }

        .item-content {
            font-weight: 500;
            font-size: 1.2rem;
            color: #4D4D4D;
            flex: auto;
        }
    </style>
@endpush
@section('block_options_btn')
    @can(Actions::View . '_' . Resources::CarAuction)
        <div class="block-options-item">
            <button class="btn btn-primary" onclick="download()">
                <i class="icon-document-download me-1"></i> {{ __('car_auctions.download_excel') }}</button>
        </div>
        <div class="block-options-item">
            <button class="btn btn-primary" onclick="printSaleSummary()">
                <i class="icon-printer me-1"></i> {{ __('car_auctions.print_sale_summary') }}</button>
        </div>
        <div class="block-options-item seperator">|</div>
    @endcan
    @can(Actions::Manage . '_' . Resources::CarAuction)
        <div class="block-options-item">
            <button class="btn btn-primary" onclick="openSendAuctionMultiModal()">
                <i class="icon-menu-car me-1"></i> {{ __('car_auctions.title_send_auction') }}</button>
            @include('admin.car-auctions.modals.send-auction')
        </div>
        <div class="block-options-item">
            <button class="btn btn-primary" onclick="openCancelCMIVMIMultiModal()">
                <i class="icon-menu-document-cancle me-1"></i> {{ __('car_auctions.title_cmi') }}</button>
            @include('admin.car-auctions.modals.cancel-cmi-vmi-multi')
        </div>
        <div class="block-options-item seperator">|</div>
        <div class="block-options-item">
            <button class="btn btn-primary" onclick="openBookMultiModal()">
                <i class="icon-menu-document-text me-1"></i> {{ __('car_auctions.title_book') }}</button>
            @include('admin.car-auctions.modals.book-multi')
        </div>
        <div class="block-options-item">
            <button class="btn btn-primary" onclick="openKeyMultiModal()">
                <i class="icon-menu-key me-1"></i> {{ __('car_auctions.title_key') }}</button>
            @include('admin.car-auctions.modals.pick-up-key-multi')
        </div>
    @endcan
@endsection
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
                            <x-forms.select-option id="auction_place_id" :value="$auction_place_id" :list="null"
                                :label="__('car_auctions.auction_place')" :optionals="[
                                    'ajax' => true,
                                    'default_option_label' => $auction_place_name,
                                ]" />
                        </div>
                        <div class="col-sm-3">
                            <x-forms.select-option id="status_id" :value="$status_id" :list="$status_list"
                                :label="__('lang.status')" />
                        </div>
                    </div>
                    <div class="form-group row push mb-4">
                        <div class="col-sm-3">
                            <label class="text-start col-form-label"
                                for="from_send_auction">{{ __('car_auctions.send_auction_date') }}</label>
                            <div class="form-group">
                                <div class="input-daterange input-group" data-date-format="mm/dd/yyyy" data-week-start="1"
                                    data-autoclose="true" data-today-highlight="true">
                                    <input type="text" class="js-flatpickr form-control flatpickr-input"
                                        id="from_send_auction" name="from_send_auction" value="{{ $from_send_auction }}"
                                        placeholder="{{ __('lang.select_date') }}" data-week-start="1" data-autoclose="true"
                                        data-today-highlight="true">
                                    <div class="input-group-prepend input-group-append">
                                        <span class="input-group-date font-w600">
                                            <i class="fa fa-fw fa-arrow-right"></i>
                                        </span>
                                    </div>
                                    <input type="text" class="js-flatpickr form-control flatpickr-input"
                                        id="to_send_auction" name="to_send_auction" value="{{ $to_send_auction }}"
                                        placeholder="{{ __('lang.select_date') }}" data-week-start="1"
                                        data-autoclose="true" data-today-highlight="true">
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <label class="text-start col-form-label"
                                for="from_auction">{{ __('car_auctions.auction_date') }}</label>
                            <div class="form-group">
                                <div class="input-daterange input-group" data-date-format="mm/dd/yyyy" data-week-start="1"
                                    data-autoclose="true" data-today-highlight="true">
                                    <input type="text" class="js-flatpickr form-control flatpickr-input"
                                        id="from_auction" name="from_auction" value="{{ $from_auction }}"
                                        placeholder="{{ __('lang.select_date') }}" data-week-start="1"
                                        data-autoclose="true" data-today-highlight="true">
                                    <div class="input-group-prepend input-group-append">
                                        <span class="input-group-date font-w600">
                                            <i class="fa fa-fw fa-arrow-right"></i>
                                        </span>
                                    </div>
                                    <input type="text" class="js-flatpickr form-control flatpickr-input" id="to_auction"
                                        name="to_auction" value="{{ $to_auction }}"
                                        placeholder="{{ __('lang.select_date') }}" data-week-start="1"
                                        data-autoclose="true" data-today-highlight="true">
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <label class="text-start col-form-label"
                                for="from_sale">{{ __('car_auctions.sale_date') }}</label>
                            <div class="form-group">
                                <div class="input-daterange input-group" data-date-format="mm/dd/yyyy"
                                    data-week-start="1" data-autoclose="true" data-today-highlight="true">
                                    <input type="text" class="js-flatpickr form-control flatpickr-input"
                                        id="from_sale" name="from_sale" value="{{ $from_sale }}"
                                        placeholder="{{ __('lang.select_date') }}" data-week-start="1"
                                        data-autoclose="true" data-today-highlight="true">
                                    <div class="input-group-prepend input-group-append">
                                        <span class="input-group-date font-w600">
                                            <i class="fa fa-fw fa-arrow-right"></i>
                                        </span>
                                    </div>
                                    <input type="text" class="js-flatpickr form-control flatpickr-input"
                                        id="to_sale" name="to_sale" value="{{ $to_sale }}"
                                        placeholder="{{ __('lang.select_date') }}" data-week-start="1"
                                        data-autoclose="true" data-today-highlight="true">
                                </div>
                            </div>
                        </div>
                    </div>
                    @include('admin.components.btns.search')
                </form>
            </div>
        </div>
    </div>
    <div class="block {{ __('block.styles') }}">
        <div class="block-content">
            <div class="row mt-4">
                <div class="col-3">
                    <div class="block block-rounded">
                        <div class="block-content d-flex align-items-center">
                            <div class="item-content">
                                <p class="text-center">{{ __('car_auctions.total_car') }}</p>
                                <p class="text-center total-size">{{ number_format($total_car) }}</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-3">
                    <div class="block block-rounded">
                        <div class="block-content d-flex align-items-center">
                            <div class="item-content">
                                <p class="text-center">{{ __('car_auctions.total_ready') }}</p>
                                <p class="text-center total-size">{{ number_format($total_ready) }}</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-3">
                    <div class="block block-rounded">
                        <div class="block-content d-flex align-items-center">
                            <div class="item-content">
                                <p class="text-center">{{ __('car_auctions.total_pending') }}</p>
                                <p class="text-center total-size">{{ number_format($total_pending) }}</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-3">
                    <div class="block block-rounded">
                        <div class="block-content d-flex align-items-center">
                            <div class="item-content">
                                <p class="text-center">{{ __('car_auctions.total_sale') }}</p>
                                <p class="text-center total-size">{{ number_format($total_sale) }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="block {{ __('block.styles') }}">
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
                            <th>@sortablelink('license_plate', __('cars.license_plate'))</th>
                            <th>@sortablelink('car_class_name', __('gps.car_class'))</th>
                            <th>@sortablelink('chassis_no', __('cars.chassis_no'))</th>
                            <th>@sortablelink('engine_no', __('cars.engine_no'))</th>
                            <th>@sortablelink('auction_place', __('car_auctions.auction_place'))</th>
                            <th class="text-center">@sortablelink('send_auction_date', __('car_auctions.send_auction_date'))</th>
                            <th class="text-center">@sortablelink('auction_date', __('car_auctions.auction_date'))</th>
                            <th class="text-center">@sortablelink('sale_date', __('car_auctions.sale_date'))</th>
                            <th>@sortablelink('car_park_transfer', __('car_auctions.car_park_transfer'))</th>
                            <th class="text-center">@sortablelink('status', __('lang.status'))</th>
                            <th style="width: 100px;" class="sticky-col text-center"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @if (sizeof($list) > 0)
                            @foreach ($list as $_index => $d)
                                <tr>
                                    <td class="text-center">
                                        <div class="form-check d-inline-block">
                                            <input class="form-check-input form-check-input-each" type="checkbox"
                                                value="{{ $d->id }}" id="row_{{ $d->id }}"
                                                name="row_{{ $d->id }}">
                                            <label class="form-check-label" for="row_{{ $d->id }}"></label>
                                        </div>
                                    </td>
                                    <td>{{ $list->firstItem() + $_index }}</td>
                                    <td>{{ $d->license_plate }}</td>
                                    <td>{{ $d->car_class_name }}</td>
                                    <td>{{ $d->chassis_no }}</td>
                                    <td>{{ $d->engine_no }}</td>
                                    <td>{{ $d->auction_name }}</td>
                                    <td>{{ $d->send_auction_date ? get_thai_date_format($d->send_auction_date, 'd/m/Y') : null }}
                                    </td>
                                    <td>{{ $d->auction_date ? get_thai_date_format($d->auction_date, 'd/m/Y') : null }}
                                    </td>
                                    <td>{{ $d->sale_date ? get_thai_date_format($d->sale_date, 'd/m/Y') : null }}</td>
                                    <td><a href="{{ $d->car_park_link ?? null }}" target="_blank">
                                            {{ $d->car_park_transfer ?? null }}</a></td>
                                    <td>{!! badge_render(__('car_auctions.class_' . $d->status), __('car_auctions.status_' . $d->status)) !!}</td>
                                    <td class="sticky-col text-center">
                                        @if (in_array($d->status, [CarAuctionStatusEnum::SEND_AUCTION, CarAuctionStatusEnum::PENDING_AUCTION]))
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
                                                            @can(Actions::View . '_' . Resources::CarAuction)
                                                                <a class="dropdown-item"
                                                                    href="{{ route('admin.car-auctions.show', [
                                                                        'car_auction' => $d,
                                                                    ]) }}"><i
                                                                        class="fa fa-eye me-1"></i> ดูข้อมูล</a>
                                                            @endcan
                                                            @can(Actions::Manage . '_' . Resources::CarAuction)
                                                                <a class="dropdown-item"
                                                                    href="{{ route('admin.car-auctions.edit', [
                                                                        'car_auction' => $d,
                                                                    ]) }}"><i
                                                                        class="far fa-edit me-1"></i> แก้ไข</a>
                                                            @endcan
                                                            @can(Actions::Manage . '_' . Resources::CarAuction)
                                                                <a class="dropdown-item btn-change-auction-modal"
                                                                    data-id="{{ $d->id }}" href="javascript:void(0)">
                                                                    <i class="icon-menu-building"></i> เปลี่ยนสถานที่ประมูล
                                                                </a>
                                                            @endcan
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @elseif (in_array($d->status, [CarAuctionStatusEnum::CHANGE_AUCTION]))
                                            @include('admin.components.dropdown-action', [
                                                'view_route' => route('admin.car-auctions.show', [
                                                    'car_auction' => $d,
                                                ]),
                                                'view_permission' => Actions::View . '_' . Resources::CarAuction,
                                            ])
                                        @else
                                            @include('admin.components.dropdown-action', [
                                                'view_route' => route('admin.car-auctions.show', [
                                                    'car_auction' => $d,
                                                ]),
                                                'edit_route' => route('admin.car-auctions.edit', [
                                                    'car_auction' => $d,
                                                ]),
                                                'view_permission' => Actions::View . '_' . Resources::CarAuction,
                                                'manage_permission' =>
                                                    Actions::Manage . '_' . Resources::CarAuction,
                                            ])
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td class="text-center" colspan="13">" {{ __('lang.no_list') }} "</td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
            {!! $list->appends(\Request::except('page'))->render() !!}
        </div>
        @include('admin.car-auctions.modals.change-auction')
    </div>
@endsection

@include('admin.components.sweetalert')
@include('admin.components.select2-default')
@include('admin.components.date-input-script')
@include('admin.car-auctions.scripts.open-modal-script')

@include('admin.components.select2-ajax', [
    'id' => 'car_id',
    'url' => route('admin.util.select2-car-auction.sale-car-license-plates'),
])

@include('admin.components.select2-ajax', [
    'id' => 'car_class_id',
    'url' => route('admin.util.select2-car-auction.sale-car-car-class'),
])

@include('admin.components.select2-ajax', [
    'id' => 'auction_place_id',
    'url' => route('admin.util.select2-car-auction.auction-places'),
])

@include('admin.components.select2-ajax', [
    'id' => 'auction_place',
    'modal' => '#modal-send-auction',
    'url' => route('admin.util.select2-car-auction.auction-places'),
])

@include('admin.components.select2-ajax', [
    'id' => 'auction_new_id',
    'parent_id' => 'auction_old_id',
    'modal' => '#modal-change-auction',
    'url' => route('admin.util.select2-car-auction.auction-places'),
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
    </script>
@endpush
