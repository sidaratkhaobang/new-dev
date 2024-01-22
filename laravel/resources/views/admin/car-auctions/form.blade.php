@extends('admin.layouts.layout')
@section('page_title', $page_title)
@section('page_title_no', $car->license_plate ?? '-')
@section('history')
    @include('admin.components.btns.history')
    @include('admin.components.transaction-modal')
@endsection
@section('page_title_sub')
    @if (isset($d->status))
        {!! badge_render(__('car_auctions.class_' . $d->status), __('car_auctions.status_' . $d->status)) !!}
    @endif
@endsection
@section('btn-nav')
    <nav class="flex-sm-00-auto ml-sm-3">
        @if (empty($view))
            @if (in_array($d->status, [CarAuctionStatusEnum::PENDING_SALE]) && !$d->close_cmi_vmi_date)
                <button type="button" class="btn btn-primary" onclick="openCancelCMIVMIModal('{{ $car->license_plate }}')"><i
                        class="icon-menu-document-cancle me-1"></i> {{ __('car_auctions.title_cmi') }}</button>
                @include('admin.car-auctions.modals.cancel-cmi-vmi')
            @endif
            @if (in_array($d->status, [CarAuctionStatusEnum::PENDING_SALE]) && !$d->pick_up_date)
                <button type="button" class="btn btn-primary" onclick="openKeyModal('{{ $car->license_plate }}')"><i
                        class="icon-menu-key me-1"></i>
                    {{ __('car_auctions.title_key') }}</button>
                @include('admin.car-auctions.modals.pick-up-key')
            @endif
        @endif
        @if (in_array($d->status, [
                CarAuctionStatusEnum::SEND_AUCTION,
                CarAuctionStatusEnum::PENDING_AUCTION,
                CarAuctionStatusEnum::SOLD_OUT,
            ]))
            <a href="{{ route('admin.car-auctions.attorney-pdf', [
                'car_auction' => $d,
            ]) }}"
                target="_blank" class="btn btn-primary">
                <i class="icon-printer me-1"></i> {{ __('car_auctions.print_power_attorney') }}
            </a>
        @endif
        @if (in_array($d->status, [CarAuctionStatusEnum::SOLD_OUT]))
            <a href="{{ route('admin.car-auctions.sale-confirm-pdf', [
                'car_auction' => $d,
            ]) }}"
                target="_blank" class="btn btn-primary">
                <i class="icon-printer me-1"></i> {{ __('car_auctions.print_sale_confirm') }}
            </a>
        @endif
    </nav>
@endsection
@push('styles')
    <style>
        .profile-image {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            overflow: hidden;
        }

        .profile-image img {
            width: 10%;
            height: 10%;
            object-fit: cover;
        }

        .items-push {
            border: 1px solid #CBD4E1;
            border-radius: 6px;

        }

        .car-detail-wrapper {
            padding: 0.75rem 1.25rem
        }

        .car-add-border {
            border-right: 1px solid #CBD4E1;
        }

        .car-info {
            padding-left: 5rem;
        }


        .size-text {
            font-size: 16px;
            font-weight: bold;
        }
    </style>
@endpush
@section('content')
    @include('admin.components.creator')
    <form id="save-form">
        {{-- Car Info --}}
        <div class="block {{ __('block.styles') }}">
            @include('admin.selling-prices.sections.modal-btn')
            @include('admin.components.block-header', [
                'text' => __('cmi_cars.car_detail'),
                'block_icon_class' => 'icon-document',
                'block_option_id' => '_btn',
            ])
            <div class="block-content">
                <div class="car-detail-wrapper">
                    <div class="row items-push">
                        <div class="col-lg-4 car-add-border">
                            @include('admin.selling-prices.sections.car-info', ['auction' => true])
                        </div>
                        <div class="col-lg-8">
                            @include('admin.car-auctions.sections.car-detail')
                        </div>
                    </div>
                </div>
            </div>
        </div>
        {{-- Car Forklift --}}
        @if (in_array($d->status, [
                CarAuctionStatusEnum::SEND_AUCTION,
                CarAuctionStatusEnum::PENDING_AUCTION,
                CarAuctionStatusEnum::CHANGE_AUCTION,
                CarAuctionStatusEnum::SOLD_OUT,
            ]))
            @include('admin.car-auctions.sections.slide-info')
        @endif

        {{-- Auction Info --}}
        @include('admin.car-auctions.sections.auction-info')

        {{-- Car Price --}}
        @include('admin.car-auctions.sections.car-price')

        {{-- Before Auction --}}
        @if (in_array($d->status, [
                CarAuctionStatusEnum::SEND_AUCTION,
                CarAuctionStatusEnum::PENDING_AUCTION,
                CarAuctionStatusEnum::CHANGE_AUCTION,
                CarAuctionStatusEnum::SOLD_OUT,
            ]))
            @include('admin.car-auctions.sections.before-auction')
        @endif

        @if (in_array($d->status, [CarAuctionStatusEnum::PENDING_AUCTION, CarAuctionStatusEnum::SOLD_OUT]))
            {{-- After Auction --}}
            @include('admin.car-auctions.sections.after-auction')
            {{-- Customer --}}
            @include('admin.car-auctions.sections.customer-info')
        @endif


        <div class="block {{ __('block.styles') }}">
            <div class="block-content">
                <x-forms.hidden id="id" :value="$d->id" />
                <x-forms.hidden id="status" :value="$d->status" />
                @if (strcmp($d->status, CarAuctionStatusEnum::SEND_AUCTION) == 0)
                    <x-forms.hidden id="prev_status" :value="$d->status" />
                    <x-forms.hidden id="status" :value="CarAuctionStatusEnum::PENDING_AUCTION" />
                @endif
                @if (strcmp($d->status, CarAuctionStatusEnum::PENDING_AUCTION) == 0)
                    <x-forms.hidden id="prev_status" :value="$d->status" />
                    <x-forms.hidden id="status" :value="CarAuctionStatusEnum::SOLD_OUT" />
                @endif
                <x-forms.submit-group :optionals="[
                    'url' => 'admin.car-auctions.index',
                    'view' => empty($view) ? null : $view,
                ]" />
            </div>
        </div>
    </form>
@endsection

@include('admin.components.select2-default')
@include('admin.components.date-input-script')
@include('admin.components.sweetalert')
@include('admin.components.form-save', [
    'store_uri' => route('admin.car-auctions.store'),
])

@include('admin.components.select2-ajax', [
    'id' => 'auction_place',
    'url' => route('admin.util.select2-car-auction.auction-places'),
])

@include('admin.components.upload-image-scripts')
@include('admin.components.upload-image', [
    'id' => 'document_sale',
    'max_files' => 10,
    'accepted_files' => '.jpg,.jpeg,.bmp,.png,.pdf',
    'mock_files' => isset($document_sale) ? $document_sale : [],
    'show_url' => true,
    'view_only' => isset($view) ? true : null,
])

@push('scripts')
    <script>
        $('#send_auction_date').prop('disabled', true);
        $('#auction_date_show').prop('disabled', true);
        $('#sale_date_show').prop('disabled', true);
        $('#auction_place').prop('disabled', true);

        $status = '{{ $d->status }}';
        $enum_send_auction = '{{ \App\Enums\CarAuctionStatusEnum::SEND_AUCTION }}';
        $enum_pending_auction = '{{ \App\Enums\CarAuctionStatusEnum::PENDING_AUCTION }}';
        $enum_sold_out = '{{ \App\Enums\CarAuctionStatusEnum::SOLD_OUT }}';
        const enum_1 = [$enum_send_auction, $enum_pending_auction];
        if (enum_1.includes($status)) {
            $('#auction_date_show').prop('disabled', false);
            $('#sale_date_show').prop('disabled', false);
        }

        const enum_2 = [$enum_pending_auction, $enum_sold_out];
        if (enum_2.includes($status)) {
            $('#depreciation_age_num').prop('disabled', true);
            $('#target_num').prop('disabled', true);
            $('#auto_grate').prop('disabled', true);
            $('#nature').prop('disabled', true);
            $('#remark').prop('disabled', true);
            $('#redbook').prop('disabled', true);
            $('#auction_price').prop('disabled', true);
            $('#tls_price').prop('disabled', true);
            $('#reason').prop('disabled', true);
            $('#vat_selling_price_num').prop('disabled', true);
            $('#total_selling_price_num').prop('disabled', true);
        }

        const enum_3 = [$enum_sold_out];
        if (enum_3.includes($status)) {
            $('#selling_price').prop('disabled', true);
            $('#profit_loss').prop('disabled', true);
            $('#tax_refund').prop('disabled', true);
            $('#other_price').prop('disabled', true);
            $('#auction_date_show').prop('disabled', true);
            $('#sale_date_show').prop('disabled', true);
        }

        $view = '{{ isset($view) }}';
        if ($view) {
            $('.form-control').prop('disabled', true);
            $('#auction_date_show').prop('disabled', true);
            $('#sale_date_show').prop('disabled', true);
        }

        function openAccessoryModal() {
            $('#accessory-modal').modal('show');
        }

        function openCancelCMIVMIModal(license_plate) {
            $('#license_plate').val(license_plate);
            $('#modal-cancel-cmi-vmi').modal('show');
        }

        function openKeyModal(license_plate) {
            $('#license_plate_key').val(license_plate);
            $('#modal-pick-up-key').modal('show');
        }
    </script>
@endpush
