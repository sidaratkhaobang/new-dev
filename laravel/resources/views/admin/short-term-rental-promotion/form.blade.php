@extends('admin.layouts.layout')

@section('page_title', __('short_term_rentals.add_new') . ' ' . $rental->worksheet_no)

@section('page_title_sub')
    @if (isset($rental->status))
        {!! badge_render(
            __('short_term_rentals.class_' . $rental->status),
            __('short_term_rentals.status_' . $rental->status),
            null,
        ) !!}
    @endif
@endsection

@section('content')
    @include('admin.components.creator', [
        'd' => $rental,
    ])
    <x-short-term-rental.step-service :rentalid="$rental_id" :success="true" :step="1" :show="true"
                                      :istoggle="true"/>
    <x-short-term-rental.step-channel :rentalid="$rental_id" :success="true" :show="true" :istoggle="true"/>
    <x-short-term-rental.step-info :rentalid="$rental_id" :success="true" :show="true" :istoggle="true"/>
    <x-short-term-rental.step-asset :rentalid="$rental_id" :success="true" :show="true" :istoggle="true"/>
    <x-short-term-rental.step-driver :rentalid="$rental_id" :success="true" :show="true" :istoggle="true"/>

    <div class="block {{ __('block.styles') }}">
        <x-blocks.block-header-step :title="__('short_term_rentals.step_title.promotion')" :step="6"
                                    :optionals="['block_icon_class' => __('short_term_rentals.step_icon.promotion')]"/>
        <div class="block-content pt-0">
            <form id="save-form">
                <div id="voucher-section" v-cloak>
                    @include('admin.short-term-rental-promotion.sections.promotion')
                    @include('admin.short-term-rental-promotion.sections.voucher')
                    <x-forms.hidden id="rental_id" :value="$rental_id"/>
                    <x-forms.hidden id="promotion_id_selected" :value="$rental->promotion_id"/>
                </div>
                <x-short-term-rental.submit-group :rentalid="$rental_id" :step="5" :optionals="[
                    'btn_name' => __('short_term_rentals.save_and_next'),
                    'icon_class_name' => 'fa fa-arrow-circle-right',
                ]"/>
            </form>
        </div>
    </div>
    <x-short-term-rental.step-summary :rentalid="null" :success="false"/>
@endsection

@include('admin.components.select2-default')
@include('admin.components.sweetalert')
@include('admin.components.form-save', [
    'store_uri' => route('admin.short-term-rental.promotion.store'),
])
@include('admin.short-term-rental-promotion.scripts.voucher-script')
{{-- @include('admin.short-term-rental-promotion.scripts.summary-script') --}}
@include('admin.short-term-rentals.scripts.update-cancel-status')

@push('scripts')
    <script>
        $(document).ready(function () {
            $(document).on('change', "input[type=checkbox][name=promotion_id]", function () {
                $("input[type=checkbox][name=promotion_id][value!='" + $(this).val() + "']").prop('checked',
                    false);
            });

            $(document).on('click', ".btn-remove-voucher", function () {
                $(this).parents('.voucher-item').remove();
            });
        });
    </script>
@endpush
