@extends('admin.layouts.layout')

@section('page_title', $page_title)

@section('content')
    {{-- <div class="block {{ __('block.styles') }}">
        <div class="block-content">
            <form id="save-form">
                @include('admin.parking-lots.sections.zone')
                @include('admin.parking-lots.sections.zone-detail-view')
                <x-forms.hidden id="id" :value="$d->id" />
                <x-forms.submit-group :optionals="['url' => 'admin.parking-lots.index', 'view' => empty($view) ? null : $view]" />
            </form>
        </div>
    </div> --}}

    <div class="block {{ __('block.styles') }}">
        @include('admin.components.block-header',[
            'text' => __('parking_lots.zone'),
            'is_toggle' => true
        ])
        <div class="block-content pt-0" >
            @include('admin.parking-lots.sections.zone')
        </div>
    </div>
    <div class="block {{ __('block.styles') }}">
        @include('admin.components.block-header',[
            'text' => __('parking_lots.zone_detail'),
            'is_toggle' => true
        ])
        <div class="block-content pt-0" >
            @include('admin.parking-lots.sections.zone-detail-view')
        </div>
    </div>
    <div class="block {{ __('block.styles') }}">
        <div class="block-content">
            <x-forms.hidden id="id" :value="$d->id" />
            <x-forms.submit-group :optionals="['url' => 'admin.parking-lots.index', 'view' => empty($view) ? null : $view]" />
        </div>
    </div>
@endsection

@include('admin.components.select2-default')
@include('admin.components.sweetalert')
@include('admin.components.form-save', [
    'store_uri' => route('admin.parking-lots.store'),
])
@include('admin.components.update-status', [
    'route' => route('admin.parking-lots.update-status-car-park-area'),
])

@include('admin.parking-lots.scripts.car-slot-script')
@include('admin.components.select2-ajax', [
    'id' => 'car_group_id',
    'url' => route('admin.util.select2.car-groups'),
])

@push('scripts')
    <script>
        $('#code').prop('disabled', true);
        $('#name').prop('disabled', true);
        $('#zone_size').prop('disabled', true);
    </script>
@endpush
