@extends('admin.layouts.layout')
@section('page_title', __('prepare_new_cars.page_title'))
@push('custom_styles')
    <style>
        .input-group-prepend > .input-group-text {
            padding: 0.7rem .75rem;
            background-color: transparent;
            border-radius: 0;
            color: #6c757d;
        }
    </style>
@endpush
@section('block_options_list')
    <div class="block-options-item">
        <button class="btn btn-primary">{{ __('import_cars.print_import') }}</button>
    </div>
    @can(Actions::Manage . '_' . Resources::VMI)
        <div class="block-options-item">
            <button class="btn btn-primary" onclick="openLotModal()">{{ __('import_cars.set_insure') }}</button>
        </div>
    @endcan
@endsection
@section('content')
    <div class="block {{ __('block.styles') }}">
        @include('admin.components.block-header', [
            'text' => __('lang.search'),
            'block_icon_class' => 'icon-search',
            'is_toggle' => true,
        ])
        <div class="block-content">
            <div class="justify-content-between">
                <form action="" method="GET" id="form-search">
                    <div class="form-group row push mb-4">
                        <div class="col-sm-3">
                            <label for="s" class="text-start col-form-label">{{ __('lang.search_label') }}</label>
                            <input type="text" id="s" name="s" class="form-control"
                                   placeholder="{{ __('lang.search_placeholder') }}" value="{{ $s }}">
                        </div>
                        {{-- <div class="col-sm-3">
                            <x-forms.input-new-line id="purchase_order_no" :value="$purchase_order_no" :optionals="['placeholder' => __('lang.search_placeholder')]"
                                :label="__('purchase_orders.purchase_order_no')" />
                        </div> --}}
                        <div class="col-sm-3">
                            <x-forms.select-option id="po_no" :value="$po_no" :list="$po_list"
                                                   :label="__('purchase_orders.purchase_order_no')"/>
                        </div>
                        <div class="col-sm-3">
                            <x-forms.select-option id="engine_no" :value="$engine_no" :list="$engine_no_list"
                                                   :label="__('import_cars.engine_no')"/>
                        </div>
                        <div class="col-sm-3">
                            <x-forms.select-option id="chassis_no" :value="$chassis_no" :list="$chassis_no_list"
                                                   :label="__('import_cars.chassis_no')"/>
                        </div>
                    </div>
                    <div class="form-group row push mb-4">
                        <div class="col-sm-3">
                            <label class="text-start col-form-label"
                                   for="from_delivery_date">{{ __('import_cars.delivery_date') }}</label>
                            <div class="form-group search-date">
                                <div class="input-daterange input-group" data-date-format="mm/dd/yyyy"
                                     data-week-start="1"
                                     data-autoclose="true" data-today-highlight="true">
                                    <input type="text" class="js-flatpickr form-control flatpickr-input"
                                           id="from_delivery_date" name="from_delivery_date"
                                           value="{{ $from_delivery_date }}"
                                           placeholder="{{ __('lang.select_date') }}" data-week-start="1"
                                           data-autoclose="true"
                                           data-today-highlight="true">
                                    <div class="input-group-prepend input-group-append">
                                        <span class="input-group-text font-w600">
                                            <i class="fa fa-fw fa-arrow-right"></i>
                                        </span>
                                    </div>
                                    <input type="text" class="js-flatpickr form-control flatpickr-input"
                                           id="to_delivery_date" name="to_delivery_date" value="{{ $to_delivery_date }}"
                                           placeholder="{{ __('lang.select_date') }}" data-week-start="1"
                                           data-autoclose="true" data-today-highlight="true">
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <x-forms.select-option id="delivery_location" :value="$delivery_location"
                                                   :list="$delivery_location_list"
                                                   :label="__('import_cars.delivery_place')"/>
                        </div>
                        <div class="col-sm-3">
                            <x-forms.select-option id="dealer" :value="$dealer" :list="$dealer_list"
                                                   :label="__('import_cars.dealer')"/>
                        </div>
                        <div class="col-sm-3">
                            <x-forms.select-option id="status" :value="$status" :list="$status_list"
                                                   :label="__('import_cars.status')"/>
                        </div>
                    </div>
                    @include('admin.components.btns.search')
                </form>
            </div>
        </div>
        {{--        @include('admin.prepare-new-cars.modals.insure-list')--}}
    </div>
    <div class="block {{ __('block.styles') }}">
        @include('admin.components.block-header', [
            'text' => __('lang.total_list'),
            'block_option_id' => '_list',
        ])
        <div class="block-content">
            <div class="table-wrap db-scroll">
                <table class="table table-striped table-vcenter">
                    <thead class="bg-body-dark">
                    <tr>
                        {{--                        <th class="text-center" style="width: 70px;">--}}
                        {{--                            <div class="form-check d-inline-block">--}}
                        {{--                                <input class="form-check-input" type="checkbox" value="" id="selectAll"--}}
                        {{--                                       name="selectAll">--}}
                        {{--                                <label class="form-check-label" for="selectAll"></label>--}}
                        {{--                            </div>--}}
                        {{--                        </th>--}}
                        <th style="width: 1px;">#</th>
                        <th style="width: 10%;">@sortablelink('insuranceLot.lot_no', __('import_cars.lot_no'))</th>
                        <th style="width: 10%;">@sortablelink('po_no', __('import_cars.purchase_order_no'))</th>
                        <th style="width: 12%;">@sortablelink('engine_no', __('import_cars.engine_no'))</th>
                        <th style="width: 10%;">@sortablelink('chassis_no', __('import_cars.chassis_no'))</th>
                        <th style="width: 10%;">@sortablelink('delivery_date', __('import_cars.delivery_date'))</th>
                        <th style="width: 10%;">@sortablelink('delivery_location', __('import_cars.delivery_place'))</th>
                        <th style="width: 10%;">@sortablelink('creditor_name', __('import_cars.dealer'))</th>
                        <th style="width: 10%;">@sortablelink('car_in_sheet', __('import_cars.car_in_sheet'))</th>
                        <th style="width: 10%;">@sortablelink('car_check_sheet', __('import_cars.car_check_sheet'))</th>
                        <th style="width: 8%;" class="text-center">{{__('import_cars.lot_status')}}</th>
                        <th style="width: 8%;"
                            class="text-center">@sortablelink('status', __('import_cars.status'))</th>
                        <th style="width: 100px;" class="sticky-col text-center">{{ __('lang.tools') }}</th>
                    </tr>
                    </thead>
                    <tbody>
                    @if (sizeof($lists) > 0)
                        @foreach ($lists as $index => $d)
                            <tr>
                                {{--                                <td class="text-center">--}}
                                {{--                                    <div class="form-check d-inline-block">--}}
                                {{--                                        <input class="form-check-input form-check-input-each" type="checkbox"--}}
                                {{--                                               value="{{ $d->id }}" id="row[{{ $d->id }}]"--}}
                                {{--                                               name="row_checkboxes">--}}
                                {{--                                        <label class="form-check-label" for="row_{{ $d->id }}"></label>--}}
                                {{--                                    </div>--}}
                                {{--                                </td>--}}
                                <td>{{ $lists->firstItem() + $index }}</td>
                                <td>{{$d?->insuranceLot?->lot_no ?? '-'}}</td>
                                <td>{{ $d->po_no }}</td>
                                <td>{{ $d->engine_no }}</td>
                                <td>{{ $d->chassis_no }}</td>
                                <td>{{ $d->delivery_date }}</td>
                                <td>{{ $d->delivery_location }}</td>
                                <td>{{ $d->creditor_name }}</td>
                                <td>
                                    @foreach ($car_park_transfer as $item_car)
                                        @if ($item_car->car_id === $d->id)
                                            <a href="{{ route('admin.car-park-transfers.show', ['car_park_transfer' => $item_car->id]) }}"
                                               target="_blank">{{ $item_car->worksheet_no }}</a> <br>
                                        @endif
                                    @endforeach
                                </td>
                                <td>
                                    @foreach ($inspection_job as $item_job)
                                        @if ($item_job->car_id === $d->id)
                                            <a href="{{ route('admin.inspection-job-steps.show', ['inspection_job_step' => $item_job->id]) }}"
                                               target="_blank">{{ $item_job->worksheet_no }}</a> <br>
                                        @endif
                                    @endforeach
                                </td>
                                <td>
                                    {!! badge_render(
                                        $d->lot_id?__('prepare_new_cars.class_lot_SUCCESS'): __('prepare_new_cars.class_lot_PENDING'),
                                        $d->lot_id?__('prepare_new_cars.status_lot_SUCCESS'): __('prepare_new_cars.status_lot_PENDING'),
                                    ) !!}
                                </td>
                                <td>
                                    {!! badge_render(
                                        __('prepare_new_cars.class_' . $d->status_delivery),
                                        __('prepare_new_cars.status_' . $d->status_delivery),
                                    ) !!}
                                </td>
                                <td class="text-center">
                                    <div class="btn-group">
                                        <div class="col-sm-12">
                                            <div class="dropdown dropleft">
                                                <button type="button"
                                                        class="btn btn-sm btn-alt-secondary dropdown-toggle"
                                                        id="dropdown-dropleft-dark" data-bs-toggle="dropdown"
                                                        aria-haspopup="true" aria-expanded="false">
                                                    <i class="fa fa-ellipsis-vertical"></i>
                                                </button>

                                                <div class="dropdown-menu" aria-labelledby="dropdown-dropleft-dark">
                                                    @can(Actions::View . '_' . Resources::ImportCarList)
                                                        <a class="dropdown-item" id="view-click"
                                                           onclick="display('{{ $d->id }}', false)"><i
                                                                class="fa fa-eye me-1"></i>
                                                            ดูข้อมูล</a>
                                                    @endcan
                                                    @can(Actions::Manage . '_' . Resources::ImportCarList)
                                                        <a class="dropdown-item" id="view-click"
                                                           onclick="display('{{ $d->id }}', true)"><i
                                                                class="far fa-edit me-1"></i>
                                                            แก้ไข</a>
                                                    @endcan
                                                </div>
                                            </div>
                                        </div>
                                    </div>
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
            {!! $lists->appends(\Request::except('page'))->render() !!}
        </div>

    </div>
    @include('admin.prepare-new-cars.modals.edit-purchase')
    @include('admin.prepare-new-cars.modals.lot-modal')
    @include('admin.prepare-new-cars.modals.lot-detail')
@endsection

@push('scripts')
    <script>
        $(document).ready(function () {
            var $selectAll = $('#selectAll');
            var $table = $('.table');
            var $tdCheckbox = $table.find('tbody input:checkbox');
            var tdCheckboxChecked = 0;

            $selectAll.on('click', function () {
                $tdCheckbox.prop('checked', this.checked);
            });

            $tdCheckbox.on('change', function (e) {
                tdCheckboxChecked = $table.find('tbody input:checkbox:checked').length;
                $selectAll.prop('checked', (tdCheckboxChecked === $tdCheckbox.length));
            })
        });

        function display(id, allow_edit = false) {
            var car_line_id = id;
            $.ajax({
                type: 'POST',
                url: "{{ route('admin.prepare-new-cars.getDataModal') }}",
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                data: {
                    car_line_id: car_line_id
                },
                success: function (data) {
                    console.log(data.characteristic_list_id);
                    $arr_acc = data.accessory;
                    $('#data_acc').val(JSON.stringify($arr_acc));
                    $('#_import_car_line_id').val(data.car_line.id);
                    $('#engine_no2').val(data.car_line.engine_no);
                    $('#chassis_no2').val(data.car_line.chassis_no);
                    $('#setup_date2').val(data.car_line.install_date);
                    $('#delivery_date2').val(data.car_line.delivery_date);
                    $('#delivery_place').val(data.car_line.delivery_location);
                    $('#remark_line').val(data.car_line.remark);
                    $('#_registration_type').val(data.car_line.registration_type).trigger('change');
                    $('#car_characteristic').val(data.characteristic_list_id).trigger('change')
                    $('#engine_no2').prop('disabled', true);
                    $('.input-group-text').css("background-color", "#e9ecef");
                    $('#chassis_no2').prop('disabled', true);
                    $('#setup_date2').prop('disabled', true);
                    $('#delivery_date2').prop('disabled', true);
                    $('#delivery_place').prop('disabled', true);
                    $('#car_entry').prop('disabled', true);
                    $('#car_inspection').prop('disabled', true);
                    $('#remark_line').prop('disabled', true);

                    if (data.accessory.length > 0) {
                        data.accessory.forEach((element, index) => {
                            $('#index').append(`<tr><td>${index + 1}</td>
                            <td>${element.accessory_name}</td>
                            <td>${element.version}</td>
                            <td>${element.pr_line_acc_amount}</td></tr>`)
                        });
                    } else {
                        $('#index').append(`<tr class="table-empty"><td class="text-center" colspan="5">
                            “{{ __('lang.no_list') . __('purchase_orders.purchase_requisition_car_detail') }}“
                            </td></tr>`)
                    }
                    if (data.car_worksheet_no.length > 0) {
                        var worksheet_no = [];
                        data.car_worksheet_no.forEach((e) => {
                            worksheet_no.push(e.worksheet_no);
                        });
                        $('#car_entry').val(worksheet_no);
                    } else {
                        $('#car_entry').val('');
                    }
                    if (data.job_worksheet_no.length > 0) {
                        var worksheet_no = [];
                        data.job_worksheet_no.forEach((e) => {
                            worksheet_no.push(e.worksheet_no);
                        });
                        $('#car_inspection').val(worksheet_no);
                    } else {
                        $('#car_inspection').val('');
                    }

                    $('#_registration_type').prop('disabled', true);
                    $("#btn_save_detail").hide();
                    if (allow_edit) {
                        $('#_registration_type').prop('disabled', false);
                        $("#btn_save_detail").show();
                    }
                    $("#modal-edit-purchase").modal("show");
                }
            });

            $(".modal").on("hidden.bs.modal", function () {
                $("#index").html("");
            });

            $("#btn_save_detail").click(function (e) {
                console.log('btn_save_detail click');

                e.preventDefault();
                let storeUri = "{{ route('admin.prepare-new-cars.update-car-detail') }}";
                var registration_type = $('#_registration_type').val();
                var car_characteristic = $('#car_characteristic').val();
                var id = $('#_import_car_line_id').val();
                if (!registration_type) {
                    return warningAlert("{{ __('lang.required_field_inform') }}");
                }
                var data = {};
                data.id = id;
                data.registration_type = registration_type;
                data.car_characteristic = car_characteristic;
                axios.post(storeUri, data).then(response => {
                    if (response.data.success) {
                        console.log('d');
                        mySwal.fire({
                            title: "{{ __('lang.store_success_title') }}",
                            text: "{{ __('lang.store_success_message') }}",
                            icon: 'success',
                            confirmButtonText: "{{ __('lang.ok') }}"
                        }).then(value => {
                            $("#modal-edit-purchase").modal("hide");
                            window.location.reload();
                        });
                    } else {
                        errorAlert(response.data.message);
                    }
                }).catch(error => {
                    errorAlert();
                });
            });
        }
    </script>
@endpush


@include('admin.components.select2-default')
@include('admin.components.sweetalert')
@include('admin.components.list-delete')
@include('admin.components.date-input-script')
@include('admin.prepare-new-cars.scripts.insure-list-script')
@include('admin.prepare-new-cars.scripts.lot-script')
@include('admin.prepare-new-cars.scripts.lot-detail-script')
@include('admin.components.select2-ajax', [
    'id' => 'leasing_id',
    'modal' => '#modal-lot-detail',
  'url' => route('admin.util.select2-repair.creditor-services'),
])
@include('admin.components.select2-ajax', [
    'id' => 'modal_po_no',
    'modal' => '#modal-lot',
  'url' => route('admin.util.select2-prepare-new-car.po-no'),
])
@include('admin.components.select2-ajax', [
    'id' => 'modal_car',
    'modal' => '#modal-lot',
  'url' => route('admin.util.select2-prepare-new-car.get-car-list'),
])




