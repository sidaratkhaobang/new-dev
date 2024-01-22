@extends('admin.layouts.layout')
@section('page_title', __('car_park_transfers.page_title'))
@section('content')
<x-blocks.block-search>
    <form action="" method="GET" id="form-search">
        <div class="form-group row push">
            <div class="col-sm-3">
                <label for="s" class="text-start col-form-label">{{ __('lang.search_label') }}</label>
                <input type="text" id="s" name="s" class="form-control"
                    placeholder="{{ __('lang.search_placeholder') }}" value="{{ $s }}">
            </div>
            @include('admin.car-park-transfers.sections.select-search-car') {{-- special for car park transfer --}}
        </div>

        <div class="form-group row push">
            <div class="col-sm-3">
                <x-forms.select-option id="transfer_type" :value="$transfer_type" :list="$transfer_type_list"
                    :label="__('car_park_transfers.transfer_type')" />
            </div>
            <div class="col-sm-3">
                <x-forms.date-input id="est_transfer_date" name="est_transfer_date" :value="$est_transfer_date"
                    :label="__('car_park_transfers.est_transfer_date')" :optionals="['placeholder' => __('lang.select_date')]" />
            </div>
            <div class="col-sm-3">
                <label class="text-start col-form-label"
                    for="period_date">{{ __('car_park_transfers.period_date') }}</label>
                <div class="form-group">
                    <div class="input-daterange input-group" data-date-format="mm/dd/yyyy" data-week-start="1"
                        data-autoclose="true" data-today-highlight="true">
                        <input type="text" class="js-flatpickr form-control flatpickr-input" id="start_date"
                            name="start_date" value="{{ $start_date }}"
                            placeholder="{{ __('lang.select_date') }}" data-week-start="1"
                            data-autoclose="true" data-today-highlight="true">
                        <div class="input-group-prepend input-group-append">
                            <span class="input-group-text font-w600">
                                <i class="fa fa-fw fa-arrow-right"></i>
                            </span>
                        </div>
                        <input type="text" class="js-flatpickr form-control flatpickr-input" id="end_date"
                            name="end_date" value="{{ $end_date }}"
                            placeholder="{{ __('lang.select_date') }}" data-week-start="1"
                            data-autoclose="true" data-today-highlight="true">
                    </div>
                </div>
            </div>
            <div class="col-sm-3">
                <x-forms.select-option id="status" :value="null" :list="$status_list" :label="__('car_park_transfers.status')" />
            </div>
        </div>
        @include('admin.components.btns.search')
    </form>
</x-blocks.block-search>

<x-blocks.block-table>
    <x-slot name="options" >
        <div class="block-options-item">
            <button class="btn btn-primary me-2" onclick="openModalPrint()">{{ __('car_park_transfers.print_license') }}</button>
            @can(Actions::Manage . '_' . Resources::CarParkTransfer)
                {{-- <x-btns.add-new btn-text="{{ __('car_park_transfers.add_new') }}" route-create="{{ route('admin.car-park-transfers.create') }}" /> --}}
            @endcan
        </div>
    </x-slot>
    <div class="table-wrap db-scroll">
        <table class="table table-striped">
            <thead class="bg-body-dark">
            <tr>
                <th class="text-center" style="width: 1px;">
                    <div class="form-check d-inline-block">
                        <input class="form-check-input" type="checkbox" value="" id="check-all"
                               name="check-all">
                        <label class="form-check-label" for="check-all"></label>
                    </div>
                </th>
                <th style="width: 1px;">#</th>
                <th>@sortablelink('worksheet_no', __('car_park_transfers.license_no'))</th>
                <th>@sortablelink('driving_job_no', __('car_park_transfers.driving_job'))</th>
                <th>@sortablelink('est_transfer_date', __('car_park_transfers.est_transfer_date'))</th>
                <th>@sortablelink('start_date', __('car_park_transfers.period_use_date'))</th>
                <th>@sortablelink('car_categories_name', __('car_park_transfers.car_category'))</th>
                <th>@sortablelink('license_plate', __('car_park_transfers.license_plate'))</th>
                <th>@sortablelink('engine_no', __('car_park_transfers.engine_no'))</th>
                <th>@sortablelink('chassis_no', __('car_park_transfers.chassis_no'))</th>
                {{-- <th>@sortablelink('parking_slot', __('car_park_transfers.parking_slot'))</th> --}}
                <th>@sortablelink('transfer_date', __('car_park_transfers.transfer_date'))</th>
                <th>@sortablelink('status', __('car_park_transfers.status'))</th>
                <th style="width: 10px;" class="sticky-col"></th>
            </tr>
            </thead>
            <tbody>
                @if (sizeof($list) > 0)
                    @foreach ($list as $index => $d)
                        <tr>
                            <td class="text-center">
                                <div class="form-check d-inline-block">
                                    <input class="form-check-input form-check-input-each" type="checkbox" value=""
                                        id="row_1" name="row_1">
                                    <label class="form-check-label" for="row_1"></label>
                                </div>
                            </td>
                            <td>{{ $list->firstItem() + $index }}</td>
                            <td>{{ $d->worksheet_no }}</td>
                            <td>{{ $d->driving_job_no }}</td>
                            <td>{{ $d->est_transfer_date ? get_thai_date_format($d->est_transfer_date, 'd/m/Y') : null }}
                            </td>
                            <td>{{ $d->start_date ? get_thai_date_format($d->start_date, 'd/m/Y') : null }} -
                                {{ $d->end_date ? get_thai_date_format($d->end_date, 'd/m/Y') : null }}</td>
                            <td>{{ $d->car_categories_name }}</td>
                            <td>{{ $d->license_plate }}</td>
                            <td>{{ $d->engine_no }}</td>
                            <td>{{ $d->chassis_no }}</td>
                            {{-- <td>{{ $d->zone_code }}{{ $d->car_park_number }}</td> --}}
                            <td>
                                @if ($d->transfer_date)
                                    @if ($d->transfer_type_log == \App\Enums\TransferTypeEnum::IN)
                                        <i class="fa-solid fa-right-to-bracket" style="color:#157CF2"></i>
                                    @else
                                        <i class="fa-solid fa-right-from-bracket" style="color: #E04F1A "></i>
                                    @endif
                                    {{ get_thai_date_format($d->transfer_date, 'd/m/Y H:i') }}
                                @else
                                    {{ null }}
                                @endif
                            </td>
                            <td>{!! badge_render($d->status_car, __('car_park_transfers.text_' . $d->status_car), null) !!}</td>
                            <td class="sticky-col text-center">
                                @if ($d->status === STATUS_INACTIVE)
                                    @include('admin.components.dropdown-action', [
                                        'view_route' => route('admin.car-park-transfers.show', [
                                            'car_park_transfer' => $d,
                                        ]),
                                        'view_permission' => Actions::View . '_' . Resources::CarParkTransfer,
                                        'manage_permission' =>
                                            Actions::Manage . '_' . Resources::CarParkTransfer,
                                    ])
                                @else
                                    @include('admin.components.dropdown-action', [
                                        'view_route' => route('admin.car-park-transfers.show', [
                                            'car_park_transfer' => $d,
                                        ]),
                                        'edit_route' => route('admin.car-park-transfers.edit', [
                                            'car_park_transfer' => $d,
                                        ]),
                                        'view_permission' => Actions::View . '_' . Resources::CarParkTransfer,
                                        'manage_permission' =>
                                            Actions::Manage . '_' . Resources::CarParkTransfer,
                                        'modal' => 'cancelmodal',
                                        'modal_id' => $d->id,
                                        'modal_text' => 'ยกเลิก',
                                    ])
                                @endif
                            </td>
                        </tr>
                    @endforeach
                @else
                <tr>
                    <td class="text-center" colspan="13">" {{ __('lang.no_list') }} "</td>
                </tr>
                @endif
            </tbody>
        </table>
    </div>
    {!! $list->appends(\Request::except('page'))->render() !!}
</x-blocks.block-table>

@include('admin.car-park-transfers.modals.print-modal')
@include('admin.car-park-transfers.modals.cancel-modal')
@endsection


@include('admin.components.select2-default')
@include('admin.components.sweetalert')
@include('admin.components.list-delete')
@include('admin.components.date-input-script')

@include('admin.components.select2-ajax', [
    'id' => 'car_id',
    'url' => route('admin.util.select2.car-license-plate'),
])

@include('admin.components.select2-ajax', [
    'id' => 'engine_no',
    'url' => route('admin.util.select2.car-engine-no'),
])

@include('admin.components.select2-ajax', [
    'id' => 'chassis_no',
    'url' => route('admin.util.select2.car-chassis-no'),
])

@push('scripts')
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

        function cancelmodal(id) {
            document.getElementById("cancel_status").value = {{ STATUS_INACTIVE }}
            document.getElementById("cancel_id").value = id;
            $('#modal-cancel').modal('show');
        }

        function openModalPrint() {
            $('#modal-print').modal('show');
        }
    </script>
@endpush
