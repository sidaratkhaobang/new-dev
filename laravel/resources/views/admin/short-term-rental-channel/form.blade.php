@extends('admin.layouts.layout')
@section('page_title', $page_title . ' ' . $d->worksheet_no)
@section('page_title_sub')
    @if (isset($d->status))
        {!! badge_render(
            __('short_term_rentals.class_' . $d->status),
            __('short_term_rentals.status_' . $d->status),
            null,
        ) !!}
    @endif
@endsection
@section('content')
    @include('admin.components.creator')
    <x-short-term-rental.step-service :rentalid="$rental_id" :success="true" :show="true" :istoggle="true"/>
    <div class="block {{ __('block.styles') }}">
        <x-blocks.block-header-step :title="__('short_term_rentals.step_title.info')" :step="2"
                                    :optionals="['block_icon_class' => __('short_term_rentals.step_icon.info')]"/>
        <div class="block-content pt-0">
            <form id="save-form">
                <div class="row push">
                    <div class="col-sm-6">
                        <x-forms.radio-inline id="order_channel" :value="$d->order_channel"
                                              :list="$order_channel_list"
                                              :label="'ช่องทางการจอง'"/>
                    </div>
                    <div class="col-sm-3">
                        <x-forms.radio-inline id="type_package" :value="$d->type_package ?? CalculateTypeEnum::DAILY"
                                              :list="$package_type_list"
                                              :label="'รูปแบบแพ็กเกจ'"/>
                    </div>
                    <div class="col-sm-3">
                        <x-forms.radio-inline id="payment_channel"
                                              :value="$d->payment_channel ?? 'CASH'"
                                              :list="$payment_type_list"
                                              :label="'รูปแบบการชำระเงิน'"/>
                    </div>
                </div>
                <x-forms.hidden id="rental_id" :value="$rental_id"/>
                <x-short-term-rental.submit-group :rentalid="$rental_id" :step="1" :optionals="[
                    'btn_name' => __('short_term_rentals.save_and_next'),
                    'icon_class_name' => 'fa fa-arrow-circle-right',
                ]"/>
            </form>
        </div>
    </div>
    <x-short-term-rental.step-info :rentalid="null" :success="false"/>
    <x-short-term-rental.step-asset :rentalid="null" :success="false"/>
    <x-short-term-rental.step-driver :rentalid="null" :success="false"/>
    <x-short-term-rental.step-promotion :rentalid="null" :success="false"/>
    <x-short-term-rental.step-summary :rentalid="null" :success="false"/>
@endsection
@include('admin.components.select2-default')
@include('admin.components.sweetalert')
@include('admin.components.form-save', [
    'store_uri' => route('admin.short-term-rental-channel.store'),
])
@include('admin.components.date-input-script')
@push('scripts')
    <script>
        $(document).ready(function () {
            $('input[name="order_channel"]').change(function () {
                var selectedValue = $('input[name="order_channel"]:checked').val();
                if (selectedValue == "{{OrderChannelEnum::WEBSITE}}") {
                    $('#payment_channelBILL').prop('checked', false)
                    $('#payment_channelBILL').prop('disabled', true)
                    $('#payment_channelCASH').prop('checked', true)
                } else {
                    $('#payment_channelBILL').prop('disabled', false)
                }

            });
        });
    </script>
@endpush