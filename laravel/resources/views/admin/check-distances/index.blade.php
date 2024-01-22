@extends('admin.layouts.layout')
@section('page_title', $page_title)

@section('block_options_list')
    <div class="block-options-item">
        @can(Actions::Manage . '_' . Resources::CheckDistance)
            <x-btns.add-new btn-text="{{ __('lang.add') . __('lang.list') }}"
                route-create="{{ route('admin.check-distances.create') }}" />
        @endcan
    </div>
@endsection

@section('content')
    <div class="block {{ __('block.styles') }}">
        @include('admin.components.block-header', [
            'text' => __('lang.search'),
            'block_icon_class' => 'icon-search',
            'is_toggle' => true,
            'block_option_id' => '_search',
        ])
        <div class="block-content">
            <div class="justify-content-between">
                <form action="" method="GET" id="form-search">
                    <div class="form-group row push mb-4">
                        <div class="col-sm-3">
                            <x-forms.select-option :value="$car_brand_id" id="car_brand_id" :list="null"
                                :label="__('car_classes.car_brand')" :optionals="[
                                    'ajax' => true,
                                    'default_option_label' => $car_brand_name,
                                ]" />
                        </div>
                        <div class="col-sm-3">
                            <x-forms.select-option :value="$car_class_id" id="car_class_id" :list="null"
                                :label="__('car_classes.class')" :optionals="[
                                    'ajax' => true,
                                    'default_option_label' => $car_class_name,
                                ]" />
                        </div>
                    </div>
                    @include('admin.components.btns.search')
                </form>
            </div>
        </div>
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
                            <th style="width: 5%;"></th>
                            <th style="width: 30%;">{{ __('car_classes.car_brand') }}</th>
                            <th style="width: 60%;">{{ __('car_classes.class') }}</th>
                            <th style="width: 100px;" class="sticky-col"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($list as $index_c => $d)
                            <tr class="{{ $loop->iteration % 2 == 0 ? 'table-active' : '' }}">
                                <td class="text-center toggle-table" style="width: 30px">
                                    <i class="fa fa-angle-right text-muted"></i>
                                </td>
                                <td>{{ $d->car_brand }}</td>
                                <td>{{ $d->car_class }}</td>
                                <td class="sticky-col text-center">
                                    <div class="dropdown dropleft">
                                        <button type="button" class="btn btn-sm btn-alt-secondary dropdown-toggle"
                                            id="dropdown-dropleft-dark" data-bs-toggle="dropdown" aria-haspopup="true"
                                            aria-expanded="false">
                                            <i class="fa fa-ellipsis-vertical"></i>
                                        </button>
                                        <div class="dropdown-menu" aria-labelledby="dropdown-dropleft-dark">
                                            @can(Actions::View . '_' . Resources::CheckDistance)
                                                <a class="dropdown-item"
                                                    href="{{ route('admin.check-distances.show', ['check_distance' => $d->id]) }}"><i
                                                        class="fa fa-eye me-1"></i>ดูข้อมูล</a>
                                            @endcan

                                            @can(Actions::Manage . '_' . Resources::CheckDistance)
                                                <a class="dropdown-item"
                                                    href="{{ route('admin.check-distances.edit', ['check_distance' => $d->id]) }}"><i
                                                        class="far fa-edit me-1"></i>แก้ไข</a>
                                            @endcan
                                            @can(Actions::Manage . '_' . Resources::CheckDistance)
                                                <a class="dropdown-item btn-delete-row" href="javascript:void(0)"
                                                    data-route-delete="{{ route('admin.check-distances.destroy', ['check_distance' => $d->id]) }}"><i
                                                        class="fa fa-trash-alt me-1"></i>{{ __('car_inspections.delete') }}</a>
                                            @endcan
                                            @can(Actions::Manage . '_' . Resources::CheckDistance)
                                                <a class="dropdown-item open-copy" type="button"
                                                    data-id="{{ $d->car_class_id }}" data-status="{{ $d->car_class }}"><i
                                                        class="far fa-clone me-1"></i>{{ __('check_distances.copy_btn') }}</a>
                                            @endcan
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            <tr style="display: none;">
                                <td></td>
                                <td class="td-table" colspan="4">
                                    <div class="row">
                                        <div class="col-md-9 text-left">
                                            <span>{{ __('check_distances.distance_table') }}</span>
                                        </div>
                                    </div>
                                    <table class="table table-striped">
                                        <thead class="bg-body-dark">
                                            <th style="width: 5%;"></th>
                                            <th>{{ __('check_distances.distance') }}</th>
                                            <th>{{ __('check_distances.month') }}</th>
                                            <th>{{ __('check_distances.amount') }}</th>
                                        </thead>
                                        <tbody>
                                            @if (sizeof($d->sub_check) > 0)
                                                @foreach ($d->sub_check as $index_d => $item)
                                                    <tr>
                                                        <td class="text-center toggle-table" style="width: 30px">
                                                            <i class="fa fa-angle-right text-muted"></i>
                                                        </td>
                                                        <td>{{ number_format($item->distance, 2) }}</td>
                                                        <td>{{ $item->month }}</td>
                                                        <td>{{ count($item->sub_check_line) > 0 ? count($item->sub_check_line) : '' }}
                                                        </td>
                                                    </tr>
                                                    <tr style="display: none;">
                                                        <td></td>
                                                        <td class="td-table" colspan="5">
                                                            <div class="row">
                                                                <div class="col-md-9 text-left">
                                                                    <span>{{ __('check_distances.check_distance_table') }}</span>
                                                                </div>
                                                            </div>
                                                            <table class="table table-striped">
                                                                <thead class="bg-body-dark">
                                                                    <th>{{ __('check_distances.code_name') }}</th>
                                                                    <th>{{ __('check_distances.is_check') }}</th>
                                                                    <th>{{ __('check_distances.price') }}</th>
                                                                    <th>{{ __('check_distances.remark') }}</th>
                                                                </thead>
                                                                <tbody>
                                                                    @if (sizeof($item->sub_check_line) > 0)
                                                                        @foreach ($item->sub_check_line as $index_l => $item_line)
                                                                            <tr>
                                                                                <td>
                                                                                    {{ $item_line->code }}  -  {{ $item_line->name }}
                                                                                </td>
                                                                                <td>
                                                                                    @if ($item_line->check)
                                                                                        {{ __('check_distances.type_text_' . $item_line->check) }}
                                                                                    @endif
                                                                                </td>
                                                                                <td>
                                                                                    {{ number_format($item_line->price, 2) }}
                                                                                </td>
                                                                                <td>
                                                                                    {{ $item_line->remark }}
                                                                                </td>
                                                                            </tr>
                                                                        @endforeach
                                                                    @else
                                                                        <tr>
                                                                            <td class="text-center" colspan="5">"
                                                                                {{ __('lang.no_list') }} "
                                                                            </td>
                                                                        </tr>
                                                                    @endif
                                                                </tbody>
                                                            </table>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            @else
                                                <tr>
                                                    <td class="text-center" colspan="4">" {{ __('lang.no_list') }} "
                                                    </td>
                                                </tr>
                                            @endif
                                        </tbody>
                                    </table>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            {!! $list->appends(\Request::except('page'))->render() !!}
        </div>
        @include('admin.check-distances.modals.copy-modal')
    </div>
@endsection

@include('admin.components.sweetalert')
@include('admin.components.list-delete')
@include('admin.components.select2-default')

@include('admin.components.select2-ajax', [
    'id' => 'car_brand_id',
    'url' => route('admin.util.select2.car-brand'),
])
@include('admin.components.select2-ajax', [
    'id' => 'car_class_id',
    'url' => route('admin.util.select2.car-class-by-car-brand'),
    'parent_id' => 'car_brand_id',
])

@include('admin.components.select2-ajax', [
    'id' => 'car_brand_id_field',
    'url' => route('admin.util.select2.car-brand'),
    'modal' => '#modal-copy',
])
@include('admin.components.select2-ajax', [
    'id' => 'car_class_id_field',
    'url' => route('admin.check-distances.select_car-class'),
    'modal' => '#modal-copy',
    'parent_id' => 'car_brand_id_field',
])

@push('scripts')
    <script>
        $('.toggle-table').click(function() {
            $(this).parent().next('tr').toggle();
            $(this).children().toggleClass('fa fa-angle-down text-muted').toggleClass(
                'fa fa-angle-right text-muted');
        });

        $('.open-copy').click(function() {
            document.getElementById("car_class").value = $(this).attr('data-status');
            document.getElementById("car_class_copy").value = $(this).attr('data-id');
            $('#modal-copy').modal('show');
        });
    </script>
@endpush
