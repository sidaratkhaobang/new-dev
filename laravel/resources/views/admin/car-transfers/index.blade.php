@extends('admin.layouts.layout')
@section('page_title', __('car_transfers.page_title'))

@push('custom_styles')
    <style>
        .input-group-text {
            padding: 0.7rem .75rem;
            background-color: transparent;
            border-radius: 0;
            color: #6c757d;
        }
    </style>
@endpush

@section('content')
    <div class="block {{ __('block.styles') }}">
        <div class="block-header">
            <h3 class="block-title">{{ __('car_transfers.total_items') }}</h3>
            <div class="block-options">
                <div class="block-options-item">
                    <x-btns.add-new btn-text="{{ __('car_transfers.add_new') }}"
                        route-create="{{ route('admin.car-transfers.create') }}" />
                </div>
            </div>
        </div>
        <div class="block-content">
            <div class="justify-content-between mb-4">
                {{-- @include('admin.components.forms.simple-search') --}}
                <form action="" method="GET" id="form-search">
                    <div class="form-group row push mb-4">
                        <div class="col-sm-3">
                            <label for="s" class="text-start col-form-label">{{ __('lang.search_label') }}</label>
                            <input type="text" id="s" name="s" class="form-control"
                                placeholder="{{ __('lang.search_placeholder') }}" value="{{ $s }}">
                        </div>
                        <div class="col-sm-3">
                            <x-forms.select-option id="car_license" :value="null" :list="null" :label="__('car_transfers.car_license')" />
                        </div>
                        <div class="col-sm-3">
                            <x-forms.select-option id="engine_no" :value="null" :list="null" :label="__('car_transfers.engine_no')" />
                        </div>
                        <div class="col-sm-3">
                            <x-forms.select-option id="chassis_no" :value="null" :list="null" :label="__('car_transfers.chassis_no')" />
                        </div>
                    </div>
                    <div class="form-group row push mb-4">
                        <div class="col-sm-3">
                            <x-forms.select-option id="license_category" :value="null" :list="$license_category"
                                :label="__('car_transfers.license_category')" />
                        </div>
                        <div class="col-sm-3">
                            <x-forms.select-option id="user_open" :value="null" :list="null"
                                :label="__('car_transfers.user_open')" />
                        </div>
                        <div class="col-sm-3">
                            <label class="text-start col-form-label"
                                for="from_open_date">{{ __('car_transfers.period_open_date') }}</label>
                            <div class="form-group">
                                <div class="input-daterange input-group" data-date-format="mm/dd/yyyy" data-week-start="1"
                                    data-autoclose="true" data-today-highlight="true">
                                    <input type="text" class="js-flatpickr form-control flatpickr-input"
                                        id="from_open_date" name="from_open_date" placeholder="{{ __('lang.select_date') }}"
                                        data-week-start="1" data-autoclose="true" data-today-highlight="true">
                                    <div class="input-group-prepend input-group-append">
                                        <span class="input-group-text font-w600">
                                            <i class="fa fa-fw fa-arrow-right"></i>
                                        </span>
                                    </div>
                                    <input type="text" class="js-flatpickr form-control flatpickr-input"
                                        id="to_open_date" name="to_open_date" placeholder="{{ __('lang.select_date') }}"
                                        data-week-start="1" data-autoclose="true" data-today-highlight="true">
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <x-forms.select-option id="status" :value="null" :list="$status"
                                :label="__('car_transfers.status')" />
                        </div>
                    </div>
                    @include('admin.components.btns.search')
                </form>
            </div>
            <div class="table-wrap db-scroll">
                <table class="table table-hover table-vcenter">
                    <thead class="bg-body-dark">
                        <tr>
                            <th>#</th>
                            <th>@sortablelink('code', __('car_transfers.transfer_no'))</th>
                            <th>@sortablelink('name', __('car_transfers.license_category'))</th>
                            <th>@sortablelink('code', __('car_transfers.car_license'))</th>
                            <th>@sortablelink('code', __('car_transfers.engine_no'))</th>
                            <th>@sortablelink('name', __('car_transfers.chassis_no'))</th>
                            <th>@sortablelink('code', __('car_transfers.rental_type'))</th>
                            <th>@sortablelink('name', __('car_transfers.from_transfer'))</th>
                            <th>@sortablelink('name', __('car_transfers.to_transfer'))</th>
                            <th>@sortablelink('name', __('car_transfers.user_open'))</th>
                            <th>@sortablelink('name', __('car_transfers.open_date'))</th>
                            <th>@sortablelink('name', __('car_transfers.status'))</th>
                            <th style="width: 100px;" class="sticky-col"></th>
                        </tr>
                    </thead>
                    <tbody>
                        {{-- @foreach ($list as $d) --}}
                        <tr>
                            <td>#</td>
                            <td>-</td>
                            <td>-</td>
                            <td>-</td>
                            <td>-</td>
                            <td>-</td>
                            <td>-</td>
                            <td>-</td>
                            <td>-</td>
                            <td>-</td>
                            <td>-</td>
                            <td>-</td>
                            <td class="sticky-col text-center">
                                @include('admin.components.dropdown-action', [
                                    // 'view_route' => route('admin.car-transfers.show', ['purchase_requisition' => $d]),
                                    // 'edit_route' => route('admin.car-transfers.edit', ['car_inout_license' => $d]),
                                    // 'delete_route' => route('admin.car-transfers.destroy', [
                                    //     'purchase_requisition' => $d,
                                    // ]),
                                ])
                            </td>
                        </tr>
                        {{-- @endforeach --}}
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
@include('admin.components.date-input-script')

@push('scripts')
    <script src="{{ asset('assets/js/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js') }}"></script>
    <script src="{{ asset('assets/js/plugins/flatpickr/flatpickr.min.js') }}"></script>
    <script src="{{ asset('assets/js/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js') }}"></script>

    <script>
        jQuery(function() {
            Dashmix.helpers(['dm-table-tools-checkable']);
        });

        $('#check-all').change(function() {
            if (this.checked) {
                $('.form-check-input-each').prop('checked', true);
            } else {
                $('.form-check-input-each').prop('checked', false);
            }
        });
    </script>
@endpush
