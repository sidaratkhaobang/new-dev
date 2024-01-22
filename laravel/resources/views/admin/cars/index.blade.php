@extends('admin.layouts.layout')
@section('page_title', __('lang.manage') . __('cars.page_title'))
@push('styles')
    <style>
        .bg-lease {
            background-color: #824DF3;
        }

        .bg-sold-out {
            background-color: #F37500;
        }

        .button-submit-modal {
            width: 180px;
            height: 42px;
        }
    </style>
@endpush
@section('content')
<div class="block {{ __('block.styles') }}">
    @include('admin.components.block-header', [
        'text' => __('lang.search'),
        'block_icon_class' => 'icon-search',
        'is_toggle' => true,
    ])
    <div class="block-content pt-0">
        <form action="" method="GET" id="form-search">
            <div class="form-group row push">
                <div class="col-sm-3">
                    <label for="s" class="text-start col-form-label">{{ __('lang.search_label') }}</label>
                    <input type="text" id="s" name="s" class="form-control"
                        placeholder="{{ __('lang.search_placeholder') }}" value="{{ $s }}">
                </div>

                <div class="col-sm-3">
                    <x-forms.select-option :value="$license_plate" id="license_plate" :list="$license_plate_list"
                        :label="__('cars.license_plate')" />
                </div>
                <div class="col-sm-3">
                    <x-forms.select-option :value="$engine_no" id="engine_no" :list="$engine_no_list" :label="__('cars.engine_no')" />
                </div>
                <div class="col-sm-3">
                    <x-forms.select-option :value="$chassis_no" id="chassis_no" :list="$chassis_no_list" :label="__('cars.chassis_no')" />
                </div>
            </div>
            <div class="form-group row push">
                <div class="col-sm-3">
                    <x-forms.select-option id="rental_type" :value="$rental_type" :list="$rental_type_list"
                        :label="__('cars.car_type')" />
                </div>
                <div class="col-sm-3">
                    <x-forms.select-option id="storage_location" :value="$storage_location" :list="$storage_location_list"
                        :label="__('cars.store_place')" />
                </div>
                <div class="col-sm-3">
                    <x-forms.select-option id="status" :value="$status" :list="$status_list"
                        :label="__('lang.status')" />
                </div>
            </div>
            @include('admin.components.btns.search')
        </form>
    </div>
</div>
<div class="block {{ __('block.styles') }}">
    @section('block_options_list')
        <div class="block-options">
            <div class="block-options-item">
                @can(Actions::Manage . '_' . Resources::Car)
                    <button class="btn btn-primary me-2" onclick="openModalSaleCar()"><i class="fa fa-location-arrow"></i>
                        {{ __('cars.send_sale_car') }}</button>
                @endcan
                @can(Actions::Manage . '_' . Resources::Car)
                    <x-btns.add-new btn-text="{{ __('cars.add_car') }}" route-create="{{ route('admin.cars.create') }}" />
                @endcan
            </div>
        </div>
    @endsection
    @include('admin.components.block-header', [
        'text' => __('lang.total_list'),
        'block_option_id' => '_list',
    ])
    <div class="block-content">
        <div class="table-wrap db-scroll">
            <table class="table table-striped table-vcenter">
                <thead class="bg-body-dark">
                    <tr>
                        <th class="text-center" style="width: 50px;">
                            <div class="form-check d-inline-block">
                                <input class="form-check-input" type="checkbox" value="" id="selectAll"
                                    name="selectAll">
                                <label class="form-check-label" for="selectAll"></label>
                            </div>
                        </th>
                        <th>#</th>
                        <th>@sortablelink('license_plate', __('cars.license_plate'))</th>
                        <th>@sortablelink('engine_no', __('cars.engine_no'))</th>
                        <th>@sortablelink('chassis_no', __('cars.chassis_no'))</th>
                        <th>@sortablelink('brand', __('cars.brand'))</th>
                        <th>@sortablelink('color', __('purchase_requisitions.car_color'))</th>
                        <th>@sortablelink('rental_type', __('cars.rental_type'))</th>
                        <th>@sortablelink('car_storage_age', __('cars.car_storage_age'))</th>
                        <th>@sortablelink('slot', __('cars.slot'))</th>
                        <th>@sortablelink('status', __('lang.status'))</th>
                        <th style="width: 5px;" class="sticky-col"></th>
                    </tr>
                </thead>
                <tbody>
                    @if (sizeof($list) > 0)
                        @foreach ($list as $index => $d)
                            <tr>
                                <td class="text-center">
                                    <div class="form-check d-inline-block">
                                        <input type="checkbox" class="form-check-input form-check-input-each"
                                            name="row_{{ $d->id }}" id="row_{{ $d->id }}">
                                        <label class="form-check-label" for="row_{{ $d->id }}"></label>
                                    </div>
                                </td>
                                <td>{{ $list->firstItem() + $index }}</td>
                                <td>{{ $d->license_plate }}</td>
                                <td>{{ $d->engine_no }}</td>
                                <td>{{ $d->chassis_no }}</td>
                                <td style="width: 30%; white-space: normal;">{{ $d->class_name }} -
                                    {{ $d->car_class_name }}</td>
                                <td>{{ $d->car_color_name }}</td>
                                <td>{{ __('cars.rental_type_' . $d->rental_type) }}</td>
                                <td>{{ $d->car_age_start }}</td>
                                <td>{{ $d->slot }}</td>
                                <td>{!! badge_render(__('cars.class_' . $d->status), __('cars.status_' . $d->status)) !!} </td>
                                <td class="sticky-col text-center">
                                    @if ($d->sale_car_total > 0)
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
                                                        @can(Actions::View . '_' . Resources::Car)
                                                            <a class="dropdown-item"
                                                                href="{{ route('admin.cars.show', ['car' => $d]) }}">
                                                                <i class="fa fa-eye me-1"></i> ดูข้อมูล</a>
                                                        @endcan
                                                        @can(Actions::Manage . '_' . Resources::Car)
                                                            <a class="dropdown-item"
                                                                href="{{ route('admin.cars.edit', ['car' => $d]) }}"><i
                                                                    class="icon-edit me-1"></i> แก้ไข</a>
                                                        @endcan
                                                        @can(Actions::Manage . '_' . Resources::Car)
                                                            @if ($d?->status_change_type === true)
                                                                <a class="dropdown-item"
                                                                    onclick="openModalCarChangeType('{{ __('cars.rental_type_' . $d->rental_type) }}','{{ $d->id }}')">
                                                                    <i class="icon-edit me-1"></i> แก้ไขประเภทรถ
                                                                </a>
                                                            @endif
                                                        @endcan
                                                        @can(Actions::Manage . '_' . Resources::Car)
                                                            <a class="dropdown-item btn-delete-row"
                                                                href="javascript:void(0)"
                                                                data-route-delete="{{ route('admin.cars.destroy', ['car' => $d]) }}"><i
                                                                    class="fa fa-trash-alt me-1"></i> ลบ</a>
                                                        @endcan
                                                        <a class="dropdown-item btn-show-sale-car-modal"
                                                            data-id="{{ $d->id }}" href="javascript:void(0)">
                                                            <i class="fa fa-location-arrow"></i> ส่งขายรถ
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @else
                                        @include('admin.components.dropdown-action', [
                                            'view_route' => route('admin.cars.show', ['car' => $d]),
                                            'edit_route' => route('admin.cars.edit', ['car' => $d]),
                                            'delete_route' => route('admin.cars.destroy', [
                                                'car' => $d,
                                            ]),
                                            'view_permission' => Actions::View . '_' . Resources::Car,
                                            'manage_permission' => Actions::Manage . '_' . Resources::Car,
                                        ])
                                    @endif
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
    @include('admin.cars.modals.sale-car-modal')
    @include('admin.cars.modals.car-type-change')
</div>
@endsection

@include('admin.components.select2-default')
@include('admin.components.sweetalert')
@include('admin.components.list-delete')


{{-- @include('admin.components.select2-ajax', [
    'id' => 'car_brand_id',
    'url' => route('admin.util.select2.car-brand'),
]) --}}
{{-- @include('admin.components.select2-ajax', [
    'id' => 'car_type_id',
    'url' => route('admin.util.select2.car-type'),
    'parent_id' => 'car_brand_id',
]) --}}

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

    $('.btn-show-sale-car-modal').on('click', function() {
        var id = $(this).attr('data-id');
        document.getElementById("car_data").value = id;
        $('#modal-send-sale-car').modal('show');
    });

    function openModalSaleCar() {
        var check_list = @json($list);
        var arr_check = [];
        if (check_list.data.length > 0) {
            check_list.data.forEach(function(item, index) {
                this_checkbox = $('input[name="row_' + item.id + '"]');
                var is_check = this_checkbox.prop('checked');
                if (is_check) {
                    arr_check.push(item.id);
                }
            });
        }
        axios.get("{{ route('admin.cars.sale-car') }}", {
            params: {
                car_id_arr: arr_check,
            }
        }).then(response => {
            if (response.data.success) {
                document.getElementById("car_data").value = response.data.data;
                $('#modal-send-sale-car').modal('show');
            } else {
                mySwal.fire({
                    title: "{{ __('lang.store_error_title') }}",
                    text: response.data.message,
                    icon: 'error',
                    confirmButtonText: "{{ __('lang.ok') }}",
                }).then(value => {
                    if (value) {
                        //
                    }
                });
            }
        });
    }
</script>
@endpush
