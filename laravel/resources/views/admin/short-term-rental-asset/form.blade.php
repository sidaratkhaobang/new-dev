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
    <x-short-term-rental.step-service :rentalid="$rental_id" :success="true" :step="1" :show="true"
                                      :istoggle="true"/>
    <x-short-term-rental.step-channel :rentalid="$rental_id" :success="true" :show="true" :istoggle="true"/>
    <x-short-term-rental.step-info :rentalid="$rental_id" :success="true" :show="true" :istoggle="true"/>
    <div class="block {{ __('block.styles') }}">
        <x-blocks.block-header-step :title="__('short_term_rentals.step_title.asset')" :step="4"
                                    :optionals="['block_icon_class' => __('short_term_rentals.step_icon.asset')]"/>
        <div class="block-content pt-0">
            <form id="save-form" @submit.prevent="">
                @include('admin.short-term-rental-asset.sections.car-type-select')
                @include('admin.short-term-rental-asset.sections.car-select1')
                {{-- @include('admin.short-term-rental-asset.scripts.gantt-script') --}}
                <x-forms.hidden id="rental_id" :value="$rental_id"/>
                {{-- <x-forms.hidden id="rental_bill_id" :value="$rental_bill_id" /> --}}
                {{-- <x-forms.hidden id="rental_line_id" :value="$rental_line_id" /> --}}

                <x-short-term-rental.submit-group :rentalid="$rental_id" :step="3" :optionals="[
                    'btn_name' => __('short_term_rentals.save_and_next'),
                    'icon_class_name' => 'fa fa-arrow-circle-right',
                ]"/>
            </form>
        </div>
    </div>
    <x-short-term-rental.step-driver :rentalid="null" :success="false"/>
    <x-short-term-rental.step-promotion :rentalid="null" :success="false"/>
    <x-short-term-rental.step-summary :rentalid="null" :success="false"/>
@endsection

@include('admin.components.select2-default')
@include('admin.components.sweetalert')
@include('admin.components.form-save', [
    'store_uri' => route('admin.short-term-rental.asset.store'),
])
@include('admin.short-term-rentals.scripts.update-cancel-status')
