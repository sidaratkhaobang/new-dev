@extends('admin.layouts.layout')

@section('page_title', $page_title)

@section('content')
<form id="save-form">
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
            @include('admin.parking-lots.sections.zone-detail')
        </div>
    </div>
    <div class="block {{ __('block.styles') }}">
        <div class="block-content">
            <x-forms.hidden id="id" :value="$d->id" />
            <x-forms.submit-group :optionals="['url' => 'admin.parking-lots.index','manage_permission' => Actions::Manage . '_' . Resources::ParkingZone]" />
        </div>
    </div>
</form>
@endsection

@include('admin.components.select2-default',[
    'modal' => 'modal-car-slot',
])
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

@include('admin.components.select2-ajax', [
    'id' => 'zone_type_id',
    'url' => route('admin.util.select2.zone-types'),
    'modal' => '#modal-car-slot',
])

@push('scripts')
    <script>
        $(".btn-car-park-area-update-status").on("click", function() {
            var data = {
                car_park_area_status: $(this).attr('data-status'),
                car_park_area_id: $(this).attr('data-id'),
            };
            updateDefaultStatus(data);
        });
    </script>
@endpush
