@extends('admin.layouts.layout')
@if (isset($view))
    @section('page_title', __('lang.view') . __('short_term_rentals.sheet'))
@else
    @section('page_title', __('lang.edit') . __('short_term_rentals.sheet'))
@endif

@section('content')

@include('admin.components.creator')
<x-short-term-rental.step-service :rentalid="$rental_id" :success="true" :step="1" :show="true" :istoggle="true" :showstep="false" />
<x-short-term-rental.step-info :rentalid="$rental_id" :success="true" :show="true" :istoggle="true" :showstep="false" />

<div class="block {{ __('block.styles') }}">
    <x-blocks.block-header-step :title="__('short_term_rentals.step_title.driver')" :step="4" 
        :optionals="['block_icon_class' => __('short_term_rentals.step_icon.driver'), 'showstep' => false]" 
    />
    <div class="block-content pt-0">
        <form id="save-form">
            @if ($allow_product_additional)
            @include('admin.short-term-rental-driver.sections.product-additionals')
            @endif

            @include('admin.short-term-rental-driver.sections.rental-purpose')

            @if ($allow_driver)
                @include('admin.short-term-rental-driver.sections.drivers')
            @endif

            @if ($allow_product_transport)
                @include('admin.short-term-rental-driver.sections.product-transport-sends')
                @include('admin.short-term-rental-driver.sections.product-transport-returns')
            @endif

            <x-forms.hidden id="rental_id" :value="$rental_id"/>
            <div class="row">
                <div class="col-sm-12 text-end">
                    {{-- <a class="btn btn-danger btn-cancel-status">{{ __('lang.cancel') }}</a> --}}
                    @if (isset($view))
                        @can(Actions::View . '_' . Resources::ShortTermRental)
                        <a class="btn btn-secondary"
                            href="{{ route('admin.short-term-rentals.show', ['short_term_rental' => $rental_id]) }}">{{ __('lang.back') }}</a>
                        @endcan
                    @else
                        @can(Actions::Manage . '_' . Resources::ShortTermRental)
                            <a class="btn btn-secondary"
                                href="{{ route('admin.short-term-rental.alter.edit', ['rental_id' => $rental_id]) }}">{{ __('lang.back') }}</a>
                        @endcan
                    @endif
                    @if (!isset($view))
                        @can(Actions::Manage . '_' . Resources::ShortTermRental)
                        <button type="button"
                            class="btn btn-primary btn-save-form-data">{{ __('lang.next') }}</button>
                        @endcan
                    @endif
                </div>
            </div>
        </form>
    </div>
</div>

<x-short-term-rental.step-promotion :rentalid="$rental_id" :success="true" :step="5" :show="true" :istoggle="true" :showstep="false" />
<x-short-term-rental.step-summary :rentalid="$rental_id" :success="true" :step="6" :show="true" :istoggle="true" :showstep="false" />

@endsection

@include('admin.short-term-rental-driver.components.styles')
@include('admin.components.date-input-script')
@include('admin.components.select2-default')
@include('admin.components.sweetalert')
@include('admin.components.form-save', [
    'store_uri' => route('admin.short-term-rental.alter.store-driver'),
])
@if ($allow_driver)
    @include('admin.short-term-rental-driver.scripts.driver-script')
@endif
@if ($allow_product_additional)
    @include('admin.short-term-rental-driver.scripts.product-additionals-script')
@endif

@if ($allow_product_transport)
@include('admin.short-term-rental-driver.scripts.product-transport-send-script')
@include('admin.short-term-rental-driver.scripts.product-transport-return-script')
@endif

@include('admin.short-term-rental-alter-driver.scripts.gantt-script')




@include('admin.short-term-rentals.scripts.update-cancel-status')
@include('admin.components.upload-image-scripts')
@if ($allow_driver)
    @include('admin.components.upload-image', [
        'id' => 'driving_license_file',
        'max_files' => 1,
        'accepted_files' => '.jpg,.jpeg,.bmp,.png,.pdf',
        'mock_files' => [],
    ])
    @include('admin.components.upload-image', [
        'id' => 'citizen_file',
        'max_files' => 1,
        'accepted_files' => '.jpg,.jpeg,.bmp,.png,.pdf',
        'mock_files' => [],
    ])
@endif

@if ($allow_product_additional)
    @include('admin.components.select2-ajax', [
        'id' => 'product_additional_id',
        'modal' => '#product-additionals',
        'url' => route('admin.util.select2.product-additionals'),
    ])
@endif

@include('admin.components.select2-ajax', [
    'id' => 'deliver_good_brand_id',
    'modal' => '#modal-deliver-good',
    'url' => route('admin.util.select2.car-brand'),
])
@include('admin.components.select2-ajax', [
    'id' => 'deliver_good_class_id',
    'modal' => '#modal-deliver-good',
    'url' => route('admin.util.select2.car-class'),
])
@include('admin.components.select2-ajax', [
    'id' => 'deliver_good_color_id',
    'modal' => '#modal-deliver-good',
    'url' => route('admin.util.select2.car-class-colors'),
])

@if ($allow_product_transport)
    @include('admin.components.upload-image', [
        'id' => 'deliver_good_files',
        'max_files' => 1,
        'accepted_files' => '.jpg,.jpeg,.png',
    ])

    @include('admin.components.upload-image', [
        'id' => 'product_files',
        'max_files' => 1,
        'accepted_files' => '.jpg,.jpeg,.bmp,.png,.pdf',
        'mock_files' => [],
    ])

    @include('admin.components.upload-image', [
        'id' => 'product_files_return',
        'max_files' => 1,
        'accepted_files' => '.jpg,.jpeg,.bmp,.png,.pdf',
        'mock_files' => [],
    ])
@endif


@push('scripts')
    <script>
        $(".btn-save-form-data").on("click", function() {
            let storeUri = "{{ route('admin.short-term-rental.alter.store-driver') }}";
            var formData = new FormData(document.querySelector('#save-form'));
            if (window.addDriverVue) {
                let data = window.addDriverVue.getFiles();
                if (data && data.length > 0) {
                    data.forEach((item) => {

                        if (item.driver_license_files && item.driver_license_files.length > 0) {
                            item.driver_license_files.forEach(function (file) {
                                if ((!file.saved) && (file.raw_file)) {
                                    if (!file.raw_file.media_id) {
                                        formData.append('driver_license_file[table_row_' + item.index + '][]', file.raw_file.raw_file);
                                    }
                                }
                            });
                        }
                        if (item.driver_citizen_files && item.driver_citizen_files.length > 0) {
                            item.driver_citizen_files.forEach(function (file) {
                                if ((!file.saved) && (file.raw_file)) {
                                    if (!file.raw_file.media_id) {
                                        formData.append('driver_citizen_files[table_row_' + item.index + '][]', file.raw_file.raw_file);
                                    }

                                }
                            });
                        }
                    });
                }

                // deleted exists files
                let delete_ids = window.addDriverVue.getPendingDeleteMediaIds();
                if (delete_ids && delete_ids.length > 0) {
                    delete_ids.forEach((item) => {
                        if (item.pending_delete_license_files && item.pending_delete_license_files.length >
                            0) {
                            item.pending_delete_license_files.forEach(function(id) {
                                formData.append('pending_delete_license_files[]', id);
                            });
                        }
                        if (item.pending_delete_citizen_files && item.pending_delete_citizen_files.length >
                            0) {
                            item.pending_delete_citizen_files.forEach(function(id) {
                                formData.append('pending_delete_citizen_files[]', id);
                            });
                        }
                    });
                }
                //delete driver row
                let delete_driver_ids = window.addDriverVue.pending_delete_driver_ids;
                if (delete_driver_ids && (delete_driver_ids.length > 0)) {
                    delete_driver_ids.forEach(function(delete_driver_id) {
                        formData.append('delete_driver_ids[]', delete_driver_id);
                    });
                }


            }

            // product transport send
            if (window.addProductTransportVue) {
                let data = window.addProductTransportVue.getFiles();
                if (data && data.length > 0) {
                    data.forEach((item) => {
                        if (item.product_files && item.product_files.length > 0) {
                            item.product_files.forEach(function(file) {
                                if ((!file.saved) && (file.raw_file)) {
                                    formData.append('product_files[table_row_' + item.index +
                                        '][]', file.raw_file);
                                }
                            });
                        }

                    });
                }
                // deleted exists files
                let delete_ids = window.addProductTransportVue.getPendingDeleteMediaIds();
                if (delete_ids && delete_ids.length > 0) {
                    delete_ids.forEach((item) => {
                        if (item.pending_delete_product_files && item.pending_delete_product_files.length >
                            0) {
                            item.pending_delete_product_files.forEach(function(id) {
                                formData.append('pending_delete_product_files[]', id);
                            });
                        }

                    });
                }
                //delete  row
                let delete_driver_ids = window.addProductTransportVue.pending_delete_driver_ids;
                if (delete_driver_ids && (delete_driver_ids.length > 0)) {
                    delete_driver_ids.forEach(function(delete_driver_id) {
                        formData.append('delete_driver_ids[]', delete_driver_id);
                    });
                }
            }

            // product transport return
            if (window.addProductTransportVue) {
                let data = window.addProductTransportReturnVue.getFiles();
                if (data && data.length > 0) {
                    data.forEach((item) => {
                        if (item.product_files_return && item.product_files_return.length > 0) {
                            item.product_files_return.forEach(function(file) {
                                if ((!file.saved) && (file.raw_file)) {
                                    formData.append('product_files_return[table_row_' + item.index +
                                        '][]', file.raw_file);
                                }
                            });
                        }

                    });
                }
                // deleted exists files
                let delete_ids = window.addProductTransportReturnVue.getPendingDeleteMediaIds();
                if (delete_ids && delete_ids.length > 0) {
                    delete_ids.forEach((item) => {
                        if (item.pending_delete_product_files_return && item
                            .pending_delete_product_files_return.length >
                            0) {
                            item.pending_delete_product_files_return.forEach(function(id) {
                                formData.append('pending_delete_product_files_return[]', id);
                            });
                        }

                    });
                }
                //delete  row
                let delete_driver_ids = window.addProductTransportReturnVue.pending_delete_driver_ids;
                if (delete_driver_ids && (delete_driver_ids.length > 0)) {
                    delete_driver_ids.forEach(function(delete_driver_id) {
                        formData.append('delete_driver_ids[]', delete_driver_id);
                    });
                }
            }
            saveForm(storeUri, formData);
        });
    </script>
@endpush
