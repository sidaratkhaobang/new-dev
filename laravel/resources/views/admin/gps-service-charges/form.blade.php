@extends('admin.layouts.layout')
@section('page_title', $page_title)

@section('content')
    <div class="block {{ __('block.styles') }}">
        <div class="block-content">
            <form id="save-form">
                <div class="justify-content-between mb-4">
                    <div class="row push mb-4">
                        <div class="col-sm-3">
                            <x-forms.select-option id="year" :value="null" :list="$fiscal_year_list" :label="__('gps.year')"
                                :optionals="['required' => true]" />
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
                                @foreach ($fiscal_month_list as $item)
                                    <tr>
                                        <td>{{ get_name_month($item) }}</td>
                                        <td>
                                            <input type="text" class="form-control number-format"
                                                name="data_month[{{ $item }}][budget_{{ $item }}]"
                                                id="budget_{{ $item }}">
                                        </td>
                                        <td>
                                            <input type="text" class="form-control number-format"
                                                name="data_month[{{ $item }}][air_time_gps_{{ $item }}]"
                                                id="air_time_gps_{{ $item }}">
                                        </td>
                                        <td>
                                            <input type="text" class="form-control number-format"
                                                name="data_month[{{ $item }}][air_time_dvr_{{ $item }}]"
                                                id="air_time_dvr_{{ $item }}">
                                        </td>
                                        <td>
                                            <input type="text" class="form-control number-format"
                                                name="data_month[{{ $item }}][total_{{ $item }}]"
                                                id="total_{{ $item }}">
                                        </td>
                                        <td style="background: #FFF3D5;">
                                            <input type="text" class="form-control number-format"
                                                name="data_month[{{ $item }}][actual_{{ $item }}]"
                                                id="actual_{{ $item }}">
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <x-forms.hidden id="id" :value="$d->id" />
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

@include('admin.components.select2-default')

@push('scripts')
    <script>
        var fiscal_month_list = @json($fiscal_month_list);
        $('#year').on('select2:select', function(e) {
            fiscal_month_list.forEach(function(month) {
                $('#budget_' + month).val('');
                $('#air_time_gps_' + month).val('');
                $('#air_time_dvr_' + month).val('');
                $('#total_' + month).val('');
                $('#actual_' + month).val('');
            });
        });
    </script>
@endpush
