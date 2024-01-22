@extends('admin.layouts.layout')
@section('page_title', $page_title . ' ' . $car->license_plate ?? '-')
@section('page_title_sub')
    @if (isset($d->status))
        {!! badge_render(__('selling_prices.class_' . $d->status), __('selling_prices.status_' . $d->status)) !!}
    @endif
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
                            @include('admin.selling-prices.sections.car-detail')
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="block {{ __('block.styles') }}">
            <div class="block-content">
                <x-forms.hidden id="id" :value="$d->id" />
                <x-forms.submit-group :optionals="[
                    'url' => 'admin.selling-prices.index',
                    'view' => empty($view) ? null : $view,
                ]" />
            </div>
        </div>
    </form>
@endsection

@include('admin.components.sweetalert')
@include('admin.components.form-save', [
    'store_uri' => route('admin.selling-prices.store'),
])
@push('scripts')
    <script>
        $('#vat_value').prop('disabled', true);
        $('#total_value').prop('disabled', true);

        function openAccessoryModal() {
            $('#accessory-modal').modal('show');
        }

        function openModalAccident() {
            $('#modal-accident-history').modal('show');
        }

        function openModalMaintain() {
            $('#modal-maintain-history').modal('show');
        }
        $view = '{{ isset($view) }}';
        if ($view) {
            $('.form-control').prop('disabled', true);
            $("input[type=radio]").attr('disabled', true);
        }

        $("#price").on("input", function() {
            price = $(this).val();
            price = parseFloat(price.replace(/,/g, ''));
            vat = 0;
            total = 0;
            if ((price)) {
                vat = parseFloat(parseFloat(price) * 7 / 107).toFixed(2);
                total = (parseFloat(parseFloat(price) + parseFloat(vat)).toFixed(2));
            }
            $('#vat').val(numberWithCommas(vat));
            $('#vat_value').val(numberWithCommas(vat));
            $('#total').val(numberWithCommas(total));
            $('#total_value').val(numberWithCommas(total));
        });

        function numberWithCommas(x) {
            return x.toString().replace(/\B(?<!\.\d*)(?=(\d{3})+(?!\d))/g, ",");
        }
    </script>
@endpush
