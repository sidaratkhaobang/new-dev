@extends('admin.layouts.layout')
@section('page_title', $page_title)
@push('custom_styles')
    <style>
        .grey-text {
            color: #858585;
        }

        .size-text {
            font-size: 16px;
            font-weight: bold;
        }
    </style>
@endpush
@section('content')
    <div class="block {{ __('block.styles') }}">
        <div class="block-content">
            <form id="save-form">
                <div class="justify-content-between mb-4">
                    <h4>{{ __('gps.user_table') }}</h4>
                    <hr>
                    <div class="row push mb-4">
                        <div class="col-sm-3">
                            <p class="grey-text">{{ __('gps.request_user') }}</p>
                            <p class="size-text" id="request_user">{{ $d->createdBy ? $d->createdBy->name : null }}</p>
                        </div>
                        <div class="col-sm-3">
                            <p class="grey-text">{{ __('gps.request_date') }}</p>
                            <p class="size-text" id="request_date">
                                {{ $d->created_at ? get_thai_date_format($d->created_at, 'd/m/Y') : null }}</p>
                        </div>
                        <div class="col-sm-3">
                            <x-forms.input-new-line id="purpose" :value="$d->purpose" :label="__('gps.purpose')" />
                        </div>
                        <div class="col-sm-3">
                            <x-forms.select-option id="type_file[]" :value="$type_file_arr" :list="$type_file_list" :label="__('gps.type_file')"
                                :optionals="['multiple' => true]" />
                        </div>
                    </div>
                    <div class="row push mb-4">
                        <div class="col-sm-3">
                            <x-forms.select-option id="status" :value="$d->status" :list="$status_approve_list" :label="__('lang.status')"
                                :optionals="['required' => true]" />
                        </div>
                        <div class="col-sm-3" id="reason_id"
                            @if (strcmp($d->status, GPSHistoricalDataStatusEnum::REJECT) == 0) style="display: block;" @else style="display: none;" @endif>
                            <x-forms.input-new-line id="reason" :value="$d->reason" :label="__('gps.reason')" />
                        </div>
                    </div>
                    <div class="row push mt-3">
                        <div class="col-sm-6">
                            <h5>{{ __('gps.request_table') }}</h5>
                        </div>
                        <div class="col-sm-6 text-end">
                            @if (in_array($d->status, [GPSHistoricalDataStatusEnum::REJECT, GPSHistoricalDataStatusEnum::CONFIRM]))
                                <button class="btn btn-success" onclick="exportExcel()"><i
                                        class="fa fa-fw fa-download  me-1"></i>
                                    {{ __('gps.download_excel') }}</button>
                            @endif
                        </div>
                    </div>
                    <div class="table-wrap db-scroll">
                        <table class="table table-striped table-vcenter">
                            <thead class="bg-body-dark">
                                <tr>
                                    <th>{{ __('gps.license_plate') }}</th>
                                    <th>{{ __('gps.date') }}</th>
                                    <th>{{ __('gps.start_time') }}</th>
                                    <th>{{ __('gps.end_time') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if (isset($car_list))
                                    @foreach ($car_list as $item)
                                        <tr>
                                            <td>{{ $item->license_plate_text }}</td>
                                            <td>{{ $item->start_date }}
                                                @if ($item->end_date)
                                                    -
                                                @endif
                                                {{ $item->end_date }}
                                            </td>
                                            <td>{{ $item->start_time }}</td>
                                            <td>{{ $item->end_time }}</td>
                                        </tr>
                                    @endforeach
                                @else
                                    <tr class="table-empty">
                                        <td class="text-center" colspan="7">"
                                            {{ __('lang.no_list') . __('gps.request_table') }} "</td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>

                    <div class="row push mb-4">
                        <div class="col-sm-3">
                            @if (strcmp($d->status, GPSHistoricalDataStatusEnum::REQUEST) == 0)
                                <x-forms.input-new-line id="link" :value="$d->link" :label="__('gps.link')"
                                    :optionals="['placeholder' => 'https://']" />
                            @else
                                <label class="text-start col-form-label" for="link">{{ __('gps.link') }}</label>
                                <br>
                                <a href="{{ $d->link }}" target="_blank" style="line-break: anywhere;">
                                    {{ $d->link }}
                                </a>
                            @endif
                        </div>
                        <div class="col-sm-3">
                            @if (isset($view))
                                <x-forms.view-image :id="'doc_additional'" :label="__('gps.doc_additional')" :list="$doc_additional_files" />
                            @else
                                <x-forms.upload-image :id="'doc_additional'" :label="__('gps.doc_additional')" />
                            @endif
                        </div>
                    </div>

                    <x-forms.hidden id="id" :value="$d->id" />
                    <div class="row push">
                        <div class="col-sm-12 text-end">
                            <a class="btn btn-secondary"
                                href="{{ route('admin.gps-historical-data-jobs.index') }}">{{ __('lang.back') }}</a>
                            @if (!isset($view))
                                <button type="button" class="btn btn-primary btn-save-form"
                                    data-status="">{{ __('lang.save') }}</button>
                            @endif
                        </div>
                    </div>
            </form>
        </div>
    </div>
@endsection

@include('admin.components.sweetalert')
@include('admin.components.select2-default')
@include('admin.components.form-save', [
    'store_uri' => route('admin.gps-historical-data-jobs.store'),
])
@include('admin.components.upload-image-scripts')
@include('admin.components.upload-image', [
    'id' => 'doc_additional',
    'max_files' => 10,
    'accepted_files' => '.xls,.xlsx,.csv,.pdf',
])

@push('scripts')
    <script>
        $('#purpose').prop('disabled', true);
        $('[name="type_file[]"]').prop('disabled', true);
        $view = '{{ isset($view) }}';
        if ($view) {
            $('#status').prop('disabled', true);
            $('#reason').prop('disabled', true);
            $('#link').prop('disabled', true);
        }

        $reject_enum = '{{ GPSHistoricalDataStatusEnum::REJECT }}';
        $('#status').on('select2:select', function(e) {
            var status = document.getElementById("status").value;
            if (status === $reject_enum) {
                document.getElementById("reason_id").style.display = "block"
            } else {
                document.getElementById("reason_id").style.display = "none"
                $("#reason").val('');
            }
        });

        function exportExcel() {
            var id = document.getElementById("id").value;
            $.ajax({
                xhrFields: {
                    responseType: 'blob'
                },
                type: 'GET',
                url: "{{ route('admin.gps-historical-data-jobs.export-excel') }}",
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                data: {
                    id: id,
                },
                success: function(result, status, xhr) {
                    var fileName = 'ข้อมูลการใช้งานรถยนต์ย้อนหลัง.xlsx';
                    var blob = new Blob([result], {
                        type: 'text/csv;charset=utf-8'
                    });
                    var link = document.createElement('a');
                    link.href = window.URL.createObjectURL(blob);
                    link.download = fileName;

                    document.body.appendChild(link);
                    link.click();
                    document.body.removeChild(link);
                },
                error: function(result, status, xhr) {
                    mySwal.fire({
                        title: "{{ __('lang.store_error_title') }}",
                        text: 'ไม่พบข้อมูล',
                        icon: 'warning',
                        confirmButtonText: "{{ __('lang.ok') }}",
                    });
                }
            });
        }
    </script>
@endpush
