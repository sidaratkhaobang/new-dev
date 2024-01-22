@extends('admin.layouts.layout')
@section('page_title', __('accident_informs.page_title'))
@section('block_options_1')
    <div class="block-options">
        <div class="block-options-item">
            @can(Actions::Manage . '_' . Resources::AccidentInform)
                <a href="#" class="btn btn-primary">
                    <i class="icon-document-download"></i>
                    {{ __('accident_informs.download_data_claim') }}
                </a>
            @endcan
        </div>
        <div class="block-options-item">
            @can(Actions::Manage . '_' . Resources::AccidentInform)
                <x-btns.add-new btn-text="{{ __('garages.add_new') }}"
                    route-create="{{ route('admin.accident-informs.create') }}" />
            @endcan
        </div>
    </div>
@endsection
@section('content')
    <div class="block {{ __('block.styles') }}">
        @include('admin.components.block-header', [
            'text' => __('lang.search'),
            'block_icon_class' => 'icon-search',
            'is_toggle' => true,
        ])
        <div class="block-content">
            <div class="justify-content-between mb-4">
                <form action="" method="GET" id="form-search">
                    <div class="form-group row push mb-4">
                        <div class="col-sm-3">
                            {{-- <x-forms.select-option :value="$worksheet" id="worksheet" :list="$worksheet_list" :label="__('accident_informs.worksheet_no')" /> --}}
                            <x-forms.select-option :value="$worksheet" id="worksheet" :list="null"
                            :label="__('accident_informs.worksheet_no')" :optionals="[
                                'select_class' => 'js-select2-custom',
                                'ajax' => true,
                                'default_option_label' => $worksheet_text,
                            ]" />
                        </div>
                        <div class="col-sm-3">
                            <x-forms.select-option :value="$accident_type" id="accident_type" :list="$accident_type_list"
                                :label="__('accident_informs.accident_type')" />
                        </div>
                        <div class="col-sm-3">
                            <x-forms.select-option id="license_plate" :value="$license_plate" :list="null"
                            :optionals="[
                                'select_class' => 'js-select2-custom',
                                'ajax' => true,
                                'default_option_label' => $license_plate_text,
                            ]"
                                :label="__('accident_informs.license_plate')" />
                        </div>
                        <div class="col-sm-3">
                            <x-forms.select-option id="status" :value="$status" :list="$status_list" :label="__('accident_informs.status')" />
                        </div>
                    </div>
                    @include('admin.components.btns.search')
                </form>
            </div>
        </div>
    </div>
    <div class="block {{ __('block.styles') }}">
        {{-- <div class="block-header">
            <h3 class="block-title">{{ __('transfer_cars.total_items') }}</h3>
            <div class="block-options">
                <div class="block-options-item">
                    @can(Actions::Manage . '_' . Resources::AccidentInform)
                        <x-btns.add-new btn-text="{{ __('garages.add_new') }}"
                            route-create="{{ route('admin.accident-informs.create') }}" />
                    @endcan
                </div>
            </div>
        </div> --}}

        @include('admin.components.block-header', [
            'text' => __('lang.total_list'),
            'block_option_id' => '_1',
        ])

        <div class="block-content">
            <div class="table-wrap db-scroll">
                <table class="table table-striped table-vcenter">
                    <thead class="bg-body-dark">
                        <tr>
                            <th class="text-center" style="width: 70px;">
                                <div class="form-check d-inline-block">
                                    <input class="form-check-input" type="checkbox" value="" id="selectAll"
                                        name="selectAll">
                                    <label class="form-check-label" for="selectAll"></label>
                                </div>
                            </th>
                            <th style="width: 25%;">@sortablelink('worksheet_no', __('accident_informs.worksheet_no'))</th>
                            <th style="width: 25%;">@sortablelink('accident_type', __('accident_informs.accident_type'))</th>
                            <th style="width: 25%;">@sortablelink('car.license_plate', __('accident_informs.main_license_plate'))</th>
                            <th style="width: 25%;">@sortablelink('accident_date', __('accident_informs.accident_datetime'))</th>
                            <th style="width: 25%;">@sortablelink('case', __('accident_informs.case'))</th>
                            <th style="width: 25%;">{{ __('accident_informs.customer') }}</th>
                            <th style="width: 20%;" class="text-center">@sortablelink('status', __('accident_informs.status'))</th>
                            <th style="width: 100px;" class="sticky-col"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @if (!$list->isEmpty())
                            @foreach ($list as $d)
                                <tr>
                                    <td class="text-center">
                                        <div class="form-check d-inline-block">
                                            <input class="form-check-input form-check-input-each" type="checkbox"
                                                value="" id="row_{{ $d->id }}" name="row_{{ $d->id }}">
                                            <label class="form-check-label" for="row_{{ $d->id }}"></label>
                                        </div>
                                    </td>
                                    <td>{{ $d->worksheet_no }}</td>
                                    <td>{{ __('accident_informs.accident_type_index_' . $d->accident_type) }}</td>
                                    <td>{{ $d->license_plate }}</td>
                                    <td>{{ get_thai_date_format($d->accident_date, 'd/m/Y H:i') }}</td>
                                    <td>{{ __('accident_informs.case_' . $d->case) }}</td>
                                    <td>{{ $d->customer_name }}</td>
                                    <td class="text-center">{!! badge_render(
                                        __('accident_informs.class_job_' . $d->status),
                                        __('accident_informs.status_job_' . $d->status),
                                        'w-25',
                                    ) !!} </td>
                                    <td class="sticky-col text-center">
                                        @include('admin.components.dropdown-action', [
                                            'edit_route' => route('admin.accident-informs.edit', [
                                                'accident_inform' => $d,
                                            ]),
                                            'view_route' => route('admin.accident-informs.show', [
                                                'accident_inform' => $d,
                                            ]),
                                            'view_permission' => Actions::View . '_' . Resources::AccidentInform,
                                            'manage_permission' =>
                                                Actions::Manage . '_' . Resources::AccidentInform,
                                        ])
                                    </td>
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td class="text-center" colspan="12">" {{ __('lang.no_list') }} "</td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
            {!! $list->appends(\Request::except('page'))->render() !!}
        </div>
    </div>
@endsection

@include('admin.components.select2-default')
@include('admin.components.sweetalert')
@include('admin.components.list-delete')
@include('admin.components.select2-ajax', [
    'id' => 'worksheet',
    'url' => route('admin.util.select2-accident.accident-all-list'),
])
@include('admin.components.select2-ajax', [
    'id' => 'license_plate',
    'url' => route('admin.util.select2-prepare-new-car.get-car-list'),
])

@push('scripts')
    <script>
        $(document).ready(function() {
            var $selectAll = $('#selectAll');
            var $table = $('.table');
            var $tdCheckbox = $table.find('tbody input:checkbox');
            var tdCheckboxChecked = 0;

            $selectAll.on('click', function() {
                $tdCheckbox.prop('checked', this.checked);
            });

            $tdCheckbox.on('change', function(e) {
                tdCheckboxChecked = $table.find('tbody input:checkbox:checked').length;
                $selectAll.prop('checked', (tdCheckboxChecked === $tdCheckbox.length));
            })
        });
    </script>
@endpush
