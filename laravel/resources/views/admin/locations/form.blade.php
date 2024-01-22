@extends('admin.layouts.layout')

@section('page_title', $page_title)

@section('content')
    <x-blocks.block>
        <form id="save-form">
            <div class="row push">
                <div class="col-sm-4">
                    <x-forms.input-new-line id="name" :value="$d->name" :label="__('locations.name')" :optionals="['required' => true]" />
                </div>
                <div class="col-sm-4">
                    <x-forms.select-option id="location_group_id" :value="$d->location_group_id" :list="$location_group_lists" :label="__('locations.location_group')" :optionals="['required' => true]"  />
                </div>
                <div class="col-sm-4">
                    <x-forms.radio-inline id="status" :value="$d->status" :list="$listStatus" :label="__('locations.status')" :optionals="['required' => true]" />
                </div>
            </div>
            <div class="row push">
                <div class="col-sm-4">
                    <x-forms.select-option id="province" :value="$d->province_id" :list="$province_list" :label="__('locations.province')" :optionals="['required' => true]" />
                </div>
                <div class="col-sm-4">
                    <x-forms.input-new-line id="lat" :value="$d->lat" :label="__('locations.lat')" />
                </div>
                <div class="col-sm-4">
                    <x-forms.input-new-line id="lng" :value="$d->lng" :label="__('locations.lng')" />
                </div>
            </div>

            <div class="row push">
                <div class="col-sm-6">
                    <x-forms.checkbox-inline id="transportation_types" :value="[$d->can_transportation_car,$d->can_transportation_boat]" :list="$list" :label="__('service_types.transportation_type')" :optionals="['required' => true]" />
                </div>
            </div>
            <x-forms.hidden id="id" :value="$d->id" />
            <x-forms.submit-group  :optionals="[
                'url' => 'admin.locations.index',
                'view' => empty($view) ? null : $view,
                'manage_permission' => Actions::Manage . '_' . Resources::Location
                ]" 
            />
        </form>
    </x-blocks.block>
@endsection

@include('admin.components.select2-default')
@include('admin.components.sweetalert')
@include('admin.components.form-save', [
    'store_uri' => route('admin.locations.store'),
])

@push('scripts')
<script>
    $status = '{{ isset($view) }}';
    if($status){
    $('#name').prop('disabled', true);
    $('#location_group_id').prop('disabled', true);
    $('#province').prop('disabled', true);
    $('#lat').prop('disabled', true);
    $('#lng').prop('disabled', true);
    $("input[type=radio]").attr('disabled', true);
    $("input[type=checkbox]").attr('disabled', true);
    }

</script>
@endpush