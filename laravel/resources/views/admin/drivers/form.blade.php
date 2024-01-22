@extends('admin.layouts.layout')

@section('page_title', $page_title)

@push('custom_styles')
    <style>
        .nav-link {
            color: #343a40;
        }

        .nav-tabs-alt .nav-link.active,
        .nav-tabs-alt .nav-item.show .nav-link {
            color: #0665d0;
        }
    </style>
@endpush
@section('content')
    <div class="block block-rounded">
        <div class="row">
            <div class="col-sm-12">
                <ul class="nav nav-tabs nav-tabs-alt" role="tablist">
                    <li class="nav-item">
                        <button class="nav-link active" id="btabs-alt-static-info-tab" data-bs-toggle="tab"
                                data-bs-target="#btabs-alt-static-info" role="tab" aria-controls="btabs-alt-static-info"
                                aria-selected="true">{{ __('drivers.driver_info_table') }}</button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link" id="btabs-alt-static-skill-tab" data-bs-toggle="tab"
                                data-bs-target="#btabs-alt-static-skill" role="tab" aria-controls="btabs-alt-static-skill"
                                aria-selected="false">{{ __('drivers.driver_skill_table') }}</button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link" id="btabs-alt-static-salary-tab" data-bs-toggle="tab"
                                data-bs-target="#btabs-alt-static-salary" role="tab" aria-controls="btabs-alt-static-salary"
                                aria-selected="false">{{ __('drivers.driver_wage_table') }}</button>
                    </li>
                </ul>
                <form id="save-form">
                    <div class="block-content tab-content">
                        <div class="tab-pane active" id="btabs-alt-static-info" role="tabpanel"
                             aria-labelledby="btabs-alt-static-info-tab">
                            @include('admin.drivers.sections.info')
                        </div>
                        <div class="tab-pane" id="btabs-alt-static-skill" role="tabpanel"
                             aria-labelledby="btabs-alt-static-skill-tab">
                            @include('admin.drivers.sections.skill')
                        </div>
                        <div class="tab-pane" id="btabs-alt-static-salary" role="tabpanel"
                             aria-labelledby="btabs-alt-static-salary-tab">
                            @include('admin.drivers.sections.wage')
                        </div>
                        <x-forms.hidden id="id" :value="$d->id"/>
                        <div class="row push">
                            <div class="col-md-12 text-end">
                                @if (!isset($view))
                                    <button type="button"
                                            class="btn btn-primary btn-save-form-driver">{{ __('lang.save') }}</button>
                                @endif
                                <a class="btn btn-secondary"
                                   href="{{ route('admin.drivers.index') }}">{{ __('lang.back') }}</a>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@include('admin.components.select2-default')
@include('admin.components.date-input-script')
@include('admin.components.sweetalert')
@include('admin.drivers.scripts.driver-skill-script')
@include('admin.drivers.scripts.driver-wage-script')

@include('admin.components.form-save', [
    'store_uri' => route('admin.drivers.store'),
])

@include('admin.components.select2-ajax', [
    'id' => 'province_id',
    'url' => route('admin.util.select2.provinces'),
])

@include('admin.components.upload-image-scripts')

@include('admin.components.upload-image', [
    'id' => 'profile_image',
    'max_files' => 1,
    'accepted_files' => '.jpg,.jpeg,.bmp,.png',
    'mock_files' => $profile_image,
    'show_url' => true
])

@include('admin.components.upload-image', [
    'id' => 'citizen_file',
    'max_files' => 1,
    'accepted_files' => '.jpg,.jpeg,.bmp,.png,.pdf',
    'mock_files' => $citizen_files,
    'show_url' => true
])

@include('admin.components.upload-image', [
    'id' => 'skill_file',
    'max_files' => 1,
    'accepted_files' => '.jpg,.jpeg,.bmp,.png,.pdf',
    'mock_files' => [],
])

@include('admin.components.select2-ajax', [
    'id' => 'driving_skill_field',
    'modal' => '#modal-driver-skill',
    'url' => route('admin.util.select2.driving-skill'),
])

@include('admin.components.select2-ajax', [
    'id' => 'driver_wage_field',
    'modal' => '#modal-driver-wage',
    'url' => route('admin.util.select2.driver-wage'),
])

@push('scripts')
    <script>
        $status = '{{ isset($view) }}';
        if ($status) {
            $('#name').prop('disabled' , true);
            $('#code').prop('disabled' , true);
            $('#emp_status').prop('disabled' , true);
            $('#position_id').prop('disabled' , true);
            $('#province_id').prop('disabled' , true);
            $('#citizen_id').prop('disabled' , true);
            $('#tel').prop('disabled' , true);
            $('#phone').prop('disabled' , true);
            $('input[name="working_day_arr[]"]').prop('disabled' , true);
            $('input[name="status"]').prop('disabled' , true);
            $('#start_working_time').prop('disabled' , true);
            $('#end_working_time').prop('disabled' , true);
            $('#branch').prop('disabled' , true);
        }

        let dropdownState = 'baht'

        $('#modal-driver-wage').on('show.bs.modal', function (e) {
            const amount_type = $('#amount_type_field').val();
            if (amount_type === '{{\App\Enums\AmountTypeEnum::PERCENT}}') {
                select_amount_type('percent')
            } else {
                select_amount_type('baht')
            }
        })

        $('#modal-driver-wage').on('hidden.bs.modal', function (e) {
            select_amount_type('baht')
            $('#input_amount').hide()
            $('#service_type_id_field_hidden').val('')
        })

        $('#dropdown-toggle-select-type').on('show.bs.dropdown', function () {
            if (dropdownState === 'baht') {
                $('#select_amount_type_baht').addClass('active')
                $('#select_amount_type_percent').removeClass('active')
            } else {
                $('#select_amount_type_baht').removeClass('active')
                $('#select_amount_type_percent').addClass('active')
            }
        })

        function select_amount_type(type) {
            if (type === 'baht') {
                dropdownState = 'baht'
                $('#amount_type_text').html('฿');
                $('#amount_type_field').val('{{\App\Enums\AmountTypeEnum::BAHT}}')
            }
            else if (type === 'percent') {
                dropdownState = 'percent'
                $('#amount_type_text').html('%');
                $('#amount_type_field').val('{{\App\Enums\AmountTypeEnum::PERCENT}}')
            }
        }

        $(".btn-save-form-driver").on("click" , function () {
            let storeUri = "{{ route('admin.drivers.store') }}";
            var formData = new FormData(document.querySelector('#save-form'));
            if (window.myDropzone) {
                var dropzones = window.myDropzone;
                dropzones.forEach((dropzone) => {
                    let dropzone_id = dropzone.options.params.elm_id;
                    let files = dropzone.getQueuedFiles();
                    files.forEach((file) => {
                        formData.append(dropzone_id + '[]' , file);
                    });
                    // delete data
                    let pending_delete_ids = dropzone.options.params.pending_delete_ids;
                    if (pending_delete_ids.length > 0) {
                        pending_delete_ids.forEach((id) => {
                            formData.append(dropzone_id + '__pending_delete_ids[]' , id);
                        });
                    }
                });
            }
            if (window.addDriverSkillVue) {
                let data = window.addDriverSkillVue.getFiles();
                if (data && data.length > 0) {
                    data.forEach((item) => {
                        if (item.driver_skill_files && item.driver_skill_files.length > 0) {
                            item.driver_skill_files.forEach(function (file) {
                                if ((!file.saved) && (file.raw_file)) {
                                    formData.append('driver_skill_file[table_row_' + item.index +
                                        '][]' , file.raw_file);
                                }
                            });
                        }
                    });
                }
                // deleted exists files
                let delete_ids = window.addDriverSkillVue.getPendingDeleteMediaIds();
                if (delete_ids && delete_ids.length > 0) {
                    delete_ids.forEach((item) => {
                        if (item.pending_delete_skill_files && item.pending_delete_skill_files.length >
                            0) {
                            item.pending_delete_skill_files.forEach(function (id) {
                                formData.append('pending_delete_skill_files[]' , id);
                            });
                        }
                    });
                }
                //delete driver skill row
                let delete_driver_skill_ids = window.addDriverSkillVue.pending_delete_driver_skill_ids;
                if (delete_driver_skill_ids && (delete_driver_skill_ids.length > 0)) {
                    delete_driver_skill_ids.forEach(function (delete_driver_skill_id) {
                        formData.append('delete_driver_skill_ids[]' , delete_driver_skill_id);
                    });
                }
            }
            saveForm(storeUri , formData);
        });
    </script>
@endpush
