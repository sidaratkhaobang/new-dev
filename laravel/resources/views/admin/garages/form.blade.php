@extends('admin.layouts.layout')

@section('page_title', $page_title)

@section('content')
    <div class="block {{ __('block.styles') }}">
        <div class="block-content">
            <form id="save-form">
                <div class="d-md-flex justify-content-md-between align-items-md-center">
                    <div>
                        <h4>{{ __('garages.garage_detail') }}</h4>
                    </div>
                </div>
                <hr>
                <div class="row push mb-4">
                    <div class="col-sm-3">
                        <x-forms.input-new-line id="garage_name" :value="$d->name" :label="__('garages.name')" :optionals="['required' => true]" />
                    </div>
                    <div class="col-sm-3">
                        <x-forms.select-option id="garage_type" :value="$d->cradle_type" :list="$garage_type_list" :label="__('garages.garage_type')"
                            :optionals="['required' => true]" />
                    </div>
                    <div class="col-sm-3">
                        <x-forms.input-new-line id="email" :value="$d->cradle_email" :label="__('garages.email')" :optionals="['required' => true]" />
                    </div>
                    <div class="col-sm-3">
                        <x-forms.input-new-line id="tel" :value="$d->cradle_tel" :label="__('garages.tel')" :optionals="['required' => true]" />
                    </div>
                    {{-- <div class="col-sm-4">
                        <x-forms.radio-inline id="status" :value="$d->status" :list="$listStatus" :label="__('locations.status')" :optionals="['required' => true ,'input_class' => 'col-sm-6 input-pd']" />
                    </div> --}}
                </div>
                <div class="row push mb-4">
                    <div class="col-sm-3">
                        <x-forms.select-option id="car_type[]" :value="$car_type" :list="$car_type_list" :label="__('garages.car_type')"
                            :optionals="['multiple' => true]" />
                    </div>
                    <div class="col-sm-3">
                        <x-forms.radio-inline id="emcs" :value="$d->emcs" :list="$listStatus" :label="__('garages.EMCS')"
                            :optionals="['required' => true, 'input_class' => 'col-sm-6 input-pd']" />
                    </div>
                    <div class="col-sm-3">
                        <x-forms.radio-inline id="onsite_service" :value="$d->is_onsite_service" :list="$listStatus" :label="__('garages.onsite_service')"
                            :optionals="['required' => true, 'input_class' => 'col-sm-6 input-pd']" />
                    </div>
                    <div class="col-sm-3">
                        <x-forms.select-option id="insurance" :value="$d->insurance" :list="[]" :label="__('garages.insurance')"
                            :optionals="['required' => true]" />
                    </div>
                    {{-- <div class="col-sm-3">
                        <x-forms.input-new-line id="insurance" :value="$d->insurance" :label="__('garages.insurance')" :optionals="['required' => true]" />
                    </div> --}}
                    {{-- <div class="col-sm-4">
                        <x-forms.select-option id="province" :value="$d->province_id" :list="$province_list" :label="__('locations.province')" :optionals="['required' => true]" />
                    </div>
                    <div class="col-sm-4">
                        <x-forms.input-new-line id="lat" :value="$d->lat" :label="__('locations.lat')" />
                    </div>
                    <div class="col-sm-4">
                        <x-forms.input-new-line id="lng" :value="$d->lng" :label="__('locations.lng')" />
                    </div> --}}
                </div>

                <div class="row push mb-4">
                    <div class="col-sm-6">
                        <x-forms.input-new-line id="address" :value="$d->address" :label="__('garages.address')" :optionals="['required' => true]" />
                    </div>
                    <div class="col-sm-3">
                        <x-forms.select-option id="sector" :value="$d->region" :list="$zone" :label="__('garages.sector')"
                            :optionals="['required' => true]" />
                    </div>
                    <div class="col-sm-3">
                        <x-forms.select-option id="province" :value="$d->province" :list="null" :label="__('garages.province')"
                            :optionals="['ajax' => true, 'default_option_label' => $province_name, 'required' => true]" />
                    </div>
                    {{-- <div class="col-sm-6">
                        <x-forms.checkbox-inline id="transportation_types" :value="[$d->can_transportation_car,$d->can_transportation_boat]" :list="$list" :label="__('service_types.transportation_type')" :optionals="['required' => true]" />
                    </div> --}}
                </div>
                <div class="row push mb-4">
                    <div class="col-sm-3">
                        <x-forms.select-option id="district" :value="$d->district" :list="null" :label="__('garages.amphure')"
                            :optionals="['ajax' => true, 'default_option_label' => $amphure_name, 'required' => true]" />
                    </div>
                    <div class="col-sm-3">
                        <x-forms.select-option id="subdistrict" :value="$d->subdistrict" :list="null" :label="__('garages.district')"
                            :optionals="['ajax' => true, 'default_option_label' => $district_name, 'required' => true]" />
                    </div>
                    <div class="col-sm-3">
                        <x-forms.input-new-line id="zip_code" :value="$d->zipCode ? $d->zipCode->zip_code : null" :label="__('garages.zip_code')" :optionals="['required' => true]" />
                    </div>
                </div>

                <div class="row push mb-4">
                    <div class="col-sm-3">
                        <x-forms.input-new-line id="coordinator_name" :value="$d->coordinator_name" :label="__('garages.coordinator_name')"
                            :optionals="['required' => true]" />
                    </div>
                    <div class="col-sm-3">
                        <x-forms.input-new-line id="coordinator_email" :value="$d->coordinator_email" :label="__('garages.email')"
                            :optionals="['required' => true]" />
                    </div>
                    <div class="col-sm-3">
                        <x-forms.input-new-line id="coordinator_tel" :value="$d->coordinator_tel" :label="__('garages.tel')"
                            :optionals="['required' => true]" />
                    </div>
                </div>

                <div class="row push mb-4">
                    <div class="col-sm-3">
                        <x-forms.radio-inline id="status" :value="$d->status" :list="$listStatus" :label="__('garages.status')"
                            :optionals="['required' => true, 'input_class' => 'col-sm-6 input-pd']" />
                    </div>
                </div>
                <x-forms.hidden id="id" :value="$d->id" />

                <x-forms.submit-group :optionals="[
                    'url' => 'admin.garages.index',
                    'view' => empty($view) ? null : $view,
                    'manage_permission' => Actions::Manage . '_' . Resources::Garage,
                ]" />
            </form>
        </div>
    </div>
@endsection

@include('admin.components.select2-default')
@include('admin.components.sweetalert')
@include('admin.components.form-save', [
    'store_uri' => route('admin.garages.store'),
])

@include('admin.components.select2-ajax', [
    'id' => 'province',
    'url' => route('admin.util.select2-garage.province'),
])

@include('admin.components.select2-ajax', [
    'id' => 'district',
    'url' => route('admin.util.select2-garage.amphure'),
    'parent_id' => 'province'
])

@include('admin.components.select2-ajax', [
    'id' => 'subdistrict',
    'url' => route('admin.util.select2-garage.district'),
    'parent_id' => 'district'
])



@push('scripts')
    <script>
        $status = '{{ isset($view) }}';
        if ($status) {
            $('.form-control').prop('disabled', true);
            $("input[type=radio]").attr('disabled', true);
            $("input[type=checkbox]").attr('disabled', true);
        }

        $('#zip_code').prop('disabled', true);

        $("#subdistrict").on('select2:select', function(e) {
            console.log('sdfsfd');
            var data = e.params.data;
            axios.get("{{ route('admin.garages.zip-code') }}", {
                params: {
                    id: data.id,
                }
            }).then(response => {

                if (response.data.success) {
                    // if (response.data.data.length > 0) {
                        // response.data.data.forEach((e) => {
                            // console.log(response.data.data.zip_code)
                            $("#zip_code").val(response.data.data.zip_code);
                        // });
                    // }
                }
            });
        });
    </script>
@endpush
