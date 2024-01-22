@extends('admin.layouts.layout')

@section('page_title', $page_title)

@section('content')
    @include('admin.components.creator')
    <div class="block {{ __('block.styles') }}">
        <x-blocks.block-header-step :title="__('short_term_rentals.step_title.service_type')" :step="1"
                                    :optionals="['block_icon_class' => __('short_term_rentals.step_icon.service_type')]"/>

        <div class="block-content pt-0">
            <form id="save-form">
                <div class="row gx-3 gy-3 mb-3">
                    @foreach ($service_types as $index => $service_type)
                        <div class="col-12 col-sm-3 col-md-2 col-lg-2">
                            <x-forms.radio-block id="service_type_id_{{ $index }}" name="service_type_id"
                                                 value="{{ $service_type->id }}" selected="{{ $d->service_type_id }}">
                                <span class="block-title">{{ $service_type->name }}</span>
                                <div class="block-img-wrap">
                                    <img src="{{ $service_type->image_url }}" alt="..." class="block-img">
                                </div>
                            </x-forms.radio-block>
                        </div>
                    @endforeach
                </div>
                <x-forms.hidden id="id" :value="$d->id"/>
                <x-forms.hidden id="worksheet_no" :value="$d->worksheet_no"/>
                <div class="row">
                    <div class="col-sm-12 text-end">
                        <button type="button" class="btn btn-primary btn-save-form">
                            <i class="fa fa-arrow-circle-right mt-1"></i>
                            {{ __('short_term_rentals.save_and_next') }}
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <x-short-term-rental.step-channel :rentalid="null" :success="false"/>
    <x-short-term-rental.step-info :rentalid="null" :success="false"/>
    <x-short-term-rental.step-asset :rentalid="null" :success="false"/>
    <x-short-term-rental.step-driver :rentalid="null" :success="false"/>
    <x-short-term-rental.step-promotion :rentalid="null" :success="false"/>
    <x-short-term-rental.step-summary :rentalid="null" :success="false"/>
@endsection

@include('admin.components.sweetalert')
@include('admin.components.form-save', [
    'store_uri' => route('admin.short-term-rental.service-types.store'),
])
