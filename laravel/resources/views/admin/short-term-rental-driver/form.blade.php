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
@push('custom_styles')
    <link rel="stylesheet" id="css-main" href="{{ asset('assets/css/admin/dropzone-image.css') }}">
@endpush
@push('styles')
    <style>
        .dz-remove {
            min-width: unset !important;
        }

        .dz-image > img {
            height: 100%;
        }

        #location-vue > .select2 {
            width: 100%;
        }
    </style>
@endpush
@section('content')
    @include('admin.components.creator')
    <x-short-term-rental.step-service :rentalid="$rental_id" :success="true" :step="1" :show="true"
                                      :istoggle="true"/>
    <x-short-term-rental.step-channel :rentalid="$rental_id" :success="true" :show="true" :istoggle="true"/>
    <x-short-term-rental.step-info :rentalid="$rental_id" :success="true" :show="true" :istoggle="true"/>
    <x-short-term-rental.step-asset :rentalid="$rental_id" :success="true" :show="true" :istoggle="true"/>
    <div class="block {{ __('block.styles') }}">
        <x-blocks.block-header-step :title="__('short_term_rentals.step_title.driver')" :step="5"
                                    :optionals="['block_icon_class' => __('short_term_rentals.step_icon.driver')]"/>
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
                @if(strcmp($d?->serviceType?->service_type,ServiceTypeEnum::SELF_DRIVE) !== 0)
                    @include('admin.short-term-rental-driver.sections.location')
                    @include('admin.short-term-rental-driver.modals.location')
                @endif
                <x-forms.hidden id="rental_id" :value="$rental_id"/>
                <x-short-term-rental.submit-group :rentalid="$rental_id" :step="4" :optionals="[
                    'btn_name' => __('short_term_rentals.save_and_next'),
                    'icon_class_name' => 'fa fa-arrow-circle-right',
                    'input_class_submit' => 'btn-save-form-data',
                ]"/>
            </form>
        </div>
    </div>

    <x-short-term-rental.step-promotion :rentalid="null" :success="false"/>
    <x-short-term-rental.step-summary :rentalid="null" :success="false"/>
@endsection

@include('admin.short-term-rental-driver.components.styles')
@include('admin.components.select2-default')
@include('admin.components.date-input-script')
@include('admin.components.upload-image-scripts')
@include('admin.components.sweetalert')
@include('admin.components.form-save', [
'store_uri' => route('admin.short-term-rental.driver.store'),
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
@if(strcmp($d?->serviceType?->service_type,ServiceTypeEnum::SELF_DRIVE) !== 0)
    @include('admin.short-term-rental-driver.scripts.location-script')
    @include('admin.short-term-rental-info.scripts.origin-script')
    @include('admin.short-term-rental-info.scripts.origin-google-map')
@endif
@include('admin.short-term-rentals.scripts.update-cancel-status')


@include('admin.components.select2-ajax', [
'id' => 'product_additional_id',
'modal' => '#product-additionals',
'url' => route('admin.util.select2.product-additionals'),
])

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
@push('scripts')
    <script>
        $(".btn-save-form-data").on("click", function () {
            let storeUri = "{{ route('admin.short-term-rental.driver.store') }}";
            var formData = new FormData(document.querySelector('#save-form'));
            if (window.addDriverVue) {
                let data = window.addDriverVue.getFiles();

                if (data && data.length > 0) {
                    data.forEach((item) => {

                        if (item.driver_license_files && item.driver_license_files.length > 0) {
                            item.driver_license_files.forEach(function (file) {
                                if ((!file.saved) && (file.raw_file)) {
                                    if (file.media_id === null || file.media_id === undefined) {
                                        formData.append('driver_license_file[table_row_' + item
                                            .index + '][]', file.raw_file);
                                    }
                                }
                            });
                        }
                        if (item.driver_citizen_files && item.driver_citizen_files.length > 0) {
                            item.driver_citizen_files.forEach(function (file) {
                                if ((!file.saved) && (file.raw_file)) {
                                    if (file.media_id === null || file.media_id === undefined) {
                                        formData.append('driver_citizen_files[table_row_' + item
                                            .index + '][]', file.raw_file);
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
                            item.pending_delete_license_files.forEach(function (id) {
                                formData.append('pending_delete_license_files[]', id);
                            });
                        }
                        if (item.pending_delete_citizen_files && item.pending_delete_citizen_files.length >
                            0) {
                            item.pending_delete_citizen_files.forEach(function (id) {
                                formData.append('pending_delete_citizen_files[]', id);
                            });
                        }
                    });
                }
                //delete driver row
                let delete_driver_ids = window.addDriverVue.pending_delete_driver_ids;
                if (delete_driver_ids && (delete_driver_ids.length > 0)) {
                    delete_driver_ids.forEach(function (delete_driver_id) {
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
                            item.product_files.forEach(function (file) {
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
                            item.pending_delete_product_files.forEach(function (id) {
                                formData.append('pending_delete_product_files[]', id);
                            });
                        }

                    });
                }
                //delete  row
                let delete_driver_ids = window.addProductTransportVue.pending_delete_driver_ids;
                if (delete_driver_ids && (delete_driver_ids.length > 0)) {
                    delete_driver_ids.forEach(function (delete_driver_id) {
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
                            item.product_files_return.forEach(function (file) {
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
                console.log(delete_ids);
                if (delete_ids && delete_ids.length > 0) {
                    delete_ids.forEach((item) => {
                        if (item.pending_delete_product_files_return && item
                                .pending_delete_product_files_return.length >
                            0) {
                            item.pending_delete_product_files_return.forEach(function (id) {
                                console.log('work')
                                console.log(id)
                                formData.append('pending_delete_product_files_return[]', id);
                            });
                        }

                    });
                }
                //delete  row
                let delete_driver_ids = window.addProductTransportReturnVue.pending_delete_driver_ids;
                if (delete_driver_ids && (delete_driver_ids.length > 0)) {
                    delete_driver_ids.forEach(function (delete_driver_id) {
                        formData.append('delete_driver_ids[]', delete_driver_id);
                    });
                }
            }

            if (window.myDropzone) {
                var dropzones = window.myDropzone;
                dropzones.forEach((dropzone) => {
                    let dropzone_id = dropzone.options.params.elm_id;
                    let files = dropzone.getQueuedFiles();
                    files.forEach((file) => {
                        formData.append(dropzone_id + '[]', file);
                    });
                    // delete data
                    let pending_delete_ids = dropzone.options.params.pending_delete_ids;
                    if (pending_delete_ids.length > 0) {
                        pending_delete_ids.forEach((id) => {
                            formData.append(dropzone_id + '__pending_delete_ids[]', id);
                        });
                    }


                    let no_delete_ids = dropzone.options.params.no_delete_ids;
                    if (no_delete_ids.length > 0) {
                        no_delete_ids.forEach((id) => {
                            formData.append(dropzone_id + '__no_delete_ids[]', id);
                        });
                    }
                });
            }
            saveForm(storeUri, formData);
        });

        $('input[name="transport"]').change(function () {
            let type = $('input[name="transport"]:checked').val();
            addProductTransportVue.selected_type = type;
            addProductTransportVue.clearDataTable()
            addProductTransportReturnVue.selected_type = type;
            addProductTransportReturnVue.clearDataTable()
        });

        $('input[name="transport_return"]').change(function () {
            let type = $('input[name="transport_return"]:checked').val();
            addProductTransportReturnVue.selected_type = type;
            addProductTransportReturnVue.clearDataTable()
        });

        $('.btn-toggle-car').click(function () {
            $(this).children().toggleClass('icon-arrow-up');
        });
    </script>
@endpush
