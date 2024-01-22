@extends('admin.layouts.layout')

@section('page_title', $page_title)

@section('content')
<x-blocks.block>
    <form id="save-form">
        <div class="row push">
            <div class="col-sm-4">
                <x-forms.input-new-line id="name" :value="$d->name" :label="__('service_types.name')" />
            </div>
            <div class="col-sm-4">
                <x-forms.radio-inline id="transportation_type" :value="$d->transportation_type ? $d->transportation_type : 1" :list="$list" :label="__('service_types.transportation_type')"/>
            </div>
            {{-- <div class="col-sm-4">
                <x-forms.radio-inline id="can_rental_over_days" :value="$d->can_rental_over_days" :list="$listcan" :label="__('service_types.can_rental_over_days')"
                    :optionals="['required' => true]" />
            </div> --}}
        </div>

        {{-- <div class="row push">
            <div class="col-sm-4">
                <x-forms.radio-inline id="can_add_stopover" :value="$d->can_add_stopover" :list="$listcan" :label="__('service_types.can_add_stopover')"
                    :optionals="['required' => true]" />
            </div>
            <div class="col-sm-4">
                <x-forms.radio-inline id="can_add_driver" :value="$d->can_add_driver" :list="$listcan" :label="__('service_types.can_add_driver')"
                    :optionals="['required' => true]" />
            </div>
            <div class="col-sm-4">
                <x-forms.radio-inline id="can_add_products" :value="$d->can_add_products" :list="$listcan" :label="__('service_types.can_add_products')"
                    :optionals="['required' => true]" />
            </div>
        </div> --}}

        {{-- <div class="row push">
            <div class="col-sm-4">
                <x-forms.radio-inline id="can_add_transport_goods" :value="$d->can_add_transport_goods" :list="$listcan"
                    :label="__('service_types.can_add_transport_goods')" :optionals="['required' => true]" />
            </div>
            <div class="col-sm-4">
                <x-forms.radio-inline id="can_add_passengers" :value="$d->can_add_passengers" :list="$listcan"
                    :label="__('service_types.can_add_passengers')" :optionals="['required' => true]" />
            </div>
        </div> --}}

        <div class="row push">
            <div class="col-sm-6">
                <x-forms.upload-image :id="'service_images'" :label="__('service_types.images')" :optionals="['required' => true]" />
            </div>
        </div>
        <x-forms.hidden id="id" :value="$d->id" />
        <x-forms.hidden id="count_files" :value="$count_files" />

        <x-forms.submit-group :optionals="[
            'url' => 'admin.service-types.index',
            'view' => empty($view) ? null : $view,
            'manage_permission' => Actions::Manage . '_' . Resources::ServiceType,
        ]" />
    </form>
</x-blocks.block>
@endsection

@include('admin.components.select2-default')
@include('admin.components.sweetalert')
@include('admin.components.form-save', [
    'store_uri' => route('admin.service-types.store'),
])

@include('admin.components.upload-image-scripts')
@include('admin.components.upload-image', [
    'id' => 'service_images',
    'max_files' => 1,
    'accepted_files' => '.jpg,.jpeg,.png',
    'mock_files' => $service_images_files,
])

@push('scripts')
    <script>
        $status = '{{ isset($view) }}';
        if ($status) {
            $("#service_images").attr('disabled', true);
        }

        $('#name').prop('disabled', true);
        $("input[type=radio]").attr('disabled', true);
    </script>
@endpush
