@extends('admin.layouts.layout')
@section('page_title', $page_title)

@section('content')
    <div class="block {{ __('block.styles') }}">
        <div class="block-content">
            <form id="save-form">
                <div class="justify-content-between mb-4">
                    <div class="row push mb-4">
                        <div class="col-sm-3">
                            <x-forms.input-new-line id="year" :value="$d->year" :label="__('gps.year')" />
                        </div>
                    </div>
                    <div class="table-wrap db-scroll">
                        <table class="table table-vcenter">
                            <thead class="bg-body-dark">
                                <tr>
                                    <th>{{ __('gps.month') }}</th>
                                    <th>{{ __('gps.budget') }}</th>
                                    <th>{{ __('gps.air_time_gps') }}</th>
                                    <th>{{ __('gps.air_time_dvr') }}</th>
                                    <th>{{ __('gps.total') }}</th>
                                    <th style="background: #EFB008;">{{ __('gps.actual') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($data_months as $item)
                                    <tr>
                                        <td>{{ get_name_month($item->month) }}</td>
                                        <td>
                                            <input type="text" class="form-control number-format"
                                                name="data_month[{{ $item->month }}][budget_{{ $item->month }}]"
                                                id="budget_{{ $item->month }}"
                                                value="{{ $item->budget > '0:00' ? $item->budget : null }}">
                                        </td>
                                        <td>
                                            <input type="text" class="form-control number-format"
                                                name="data_month[{{ $item->month }}][air_time_gps_{{ $item->month }}]"
                                                id="air_time_gps_{{ $item->month }}"
                                                value="{{ $item->air_time_gps > '0:00' ? $item->air_time_gps : null }}">
                                        </td>
                                        <td>
                                            <input type="text" class="form-control number-format"
                                                name="data_month[{{ $item->month }}][air_time_dvr_{{ $item->month }}]"
                                                id="air_time_dvr_{{ $item->month }}"
                                                value="{{ $item->air_time_dvr > '0:00' ? $item->air_time_dvr : null }}">
                                        </td>
                                        <td>
                                            <input type="text" class="form-control number-format"
                                                name="data_month[{{ $item->month }}][total_{{ $item->month }}]"
                                                id="total_{{ $item->month }}"
                                                value="{{ $item->total > '0:00' ? $item->total : null }}">
                                        </td>
                                        <td style="background: #FFF3D5;">
                                            <input type="text" class="form-control number-format"
                                                name="data_month[{{ $item->month }}][actual_{{ $item->month }}]"
                                                id="actual_{{ $item->month }}"
                                                value="{{ $item->actual > '0:00' ? $item->actual : null }}">
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <x-forms.hidden id="id" :value="$d->id" />
                <x-forms.hidden id="year" :value="$d->year" />
                <x-forms.submit-group :optionals="[
                    'url' => 'admin.gps-service-charges.index',
                    'view' => empty($view) ? null : $view,
                ]" />
            </form>
        </div>
    </div>
@endsection

@include('admin.components.sweetalert')
@include('admin.components.form-save', [
    'store_uri' => route('admin.gps-service-charges.store'),
])

@push('scripts')
    <script>
        $('#year').prop('disabled', true);
        $view = '{{ isset($view) }}';
        if ($view) {
            var data_months = @json($data_months);
            data_months.forEach(function(item) {
                $('#budget_' + item.month).prop('disabled', true);
                $('#air_time_gps_' + item.month).prop('disabled', true);
                $('#air_time_dvr_' + item.month).prop('disabled', true);
                $('#total_' + item.month).prop('disabled', true);
                $('#actual_' + item.month).prop('disabled', true);
            });
        }
    </script>
@endpush
