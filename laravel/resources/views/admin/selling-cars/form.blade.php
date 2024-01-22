@extends('admin.layouts.layout')
@section('page_title', $page_title . ' ' . $car->license_plate ?? '-')
@section('page_title_sub')
    @if (isset($d->status))
        {!! badge_render(__('selling_prices.class_' . $d->status), __('selling_prices.status_' . $d->status)) !!}
    @endif
@endsection
@section('btn-nav')
    <nav class="flex-sm-00-auto ml-sm-3">
        @if (in_array($d->status, [SellingPriceStatusEnum::PENDING_SALE]))
            <button type="button" class="btn btn-danger" onclick="openCloseFinance()"><i
                    class="si si-close"></i>&nbsp;{{ __('selling_prices.noti_finance') }}</button>
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
                            @include('admin.selling-prices.sections.car-info')
                        </div>
                        <div class="col-lg-8">
                            @include('admin.selling-cars.sections.car-detail')
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="block {{ __('block.styles') }}">
            <div class="block-content">
                <x-forms.hidden id="id" :value="$d->id" />
                <x-forms.hidden id="status" :value="$d->status" />
                <x-forms.submit-group :optionals="[
                    'url' => 'admin.selling-cars.index',
                    'view' => empty($view) ? null : $view,
                ]" />
            </div>
        </div>
        @include('admin.selling-cars.modals.close-finance-modal')
    </form>
@endsection

@include('admin.components.select2-default')
@include('admin.components.sweetalert')
@include('admin.components.form-save', [
    'store_uri' => route('admin.selling-cars.store'),
])

@include('admin.components.select2-ajax', [
    'id' => 'transfer_status',
    'url' => route('admin.util.select2-car-auction.status'),
])

@include('admin.components.select2-ajax', [
    'id' => 'ownership',
    'url' => route('admin.util.select2-car-auction.car-ownership'),
])

@push('scripts')
    <script>
        $view = '{{ isset($view) }}';
        if ($view) {
            $("#transfer_status").attr('disabled', true);
            $("#ownership").attr('disabled', true);
        }

        function openAccessoryModal() {
            $('#accessory-modal').modal('show');
        }

        function openCloseFinance() {
            $('#modal-close-finances').modal('show');
        }
    </script>
@endpush
