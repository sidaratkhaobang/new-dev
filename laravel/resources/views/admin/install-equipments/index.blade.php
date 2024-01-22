@extends('admin.layouts.layout')
@section('page_title', __('install_equipments.page_title'))

@push('styles')
    <style>
        .img-fluid {
            width: 275px;
            height: 100px;
            object-fit: cover;
            display: block;
            /* margin: auto; */
        }

        .car-border {
            border: 1px solid #CBD4E1;
            border-radius: 6px;
            color: #475569;
        }

        .bg-alt-secondary {
            color: #1c1f23;
            background-color: #d5d8de;
        }

        .badge-pill.text-alt-secondary {
            color: #1c1f23;
        }

        .bg-alt-warning {
            background-color: #f9e7c5 !important;
        }

        .badge-pill.text-alt-warning {
            color: #8a5f0e !important;
        }

        .badge-pill.bg-alt-info {
            color: #245686;
            background-color: #cee3f7;
        }

        .badge-pill.text-alt-info {
            color: #245686 !important;
        }

        .bg-alt-success {
            color: #435e26;
            background-color: #dbe6cf;
        }

        .badge-pill.text-alt-success {
            color: #435e26 !important;
        }

        .bg-alt-danger {
            color: #862f10;
            background-color: #f7d3c6;
        }

        .badge-pill.text-alt-danger {
            color: #862f10 !important;
        }

        table {
            border-collapse: collapse;
            width: 100%;
        }

        tr.hidden {
            display: none;
        }

        .btn-delete-row {
            --bs-bg-opacity: 0;
            background-color: rgba(var(--bs-light-rgb), var(--bs-bg-opacity)) !important;
        }

        .tooltip-block {
            position: relative;
        }

        .tooltip-content {
            display: none;
            position: absolute;
            z-index: 100;
            border: 1px;
            background-color: #555;
            border-style: solid;
            border-radius: 0.275rem;
            padding: 3px;
            color: #fff;
            top: 20px;
            left: 20px;
            padding-right: 1rem;
            opacity: 0.96;
        }

        .tooltip-content ul {
            margin-bottom: 0px;
        }

        .tooltip-block:hover span.tooltip-content {
            display: block;
        }

        .border-none {
            border: none !important;
        }
    </style>
@endpush

@section('block_options_1')
    <div class="block-options-item ">
        @can(Actions::Manage . '_' . Resources::InstallEquipment)
            <button type="button" class="btn btn-primary" onclick="callAllInspection()">
                <i class="icon-direct-up me-1"></i>{{ __('install_equipments.send_to_inspect_all') }}
            </button>
        @endcan
    </div>
    <div class="block-options-item">
        @can(Actions::View . '_' . Resources::InstallEquipment)
            <button type="button" class="btn btn-success" onclick="openExcelModal()">
                <i class="icon-document-download me-1"></i>{{ __('install_equipments.excel') }}
            </button>
        @endcan
    </div>
    <div class="block-options-item">
        @can(Actions::Manage . '_' . Resources::InstallEquipment)
            <a href="{{ route('admin.install-equipments.create') }}" class="btn btn-purple">
                <i class="icon-receipt-add me-1"></i>{{ __('install_equipments.add_new') }}
            </a>
        @endcan
    </div>
@endsection
@section('content')
    <div class="block {{ __('block.styles') }}">
        @include('admin.components.block-header', [
            'text' => __('lang.search'),
            'block_icon_class' => 'icon-search',
            'is_toggle' => true,
            'block_option_id' => '_0',
        ])
        @include('admin.install-equipments.sections.search')
        @include('admin.install-equipments.modals.inspection-send')
        @include('admin.install-equipments.modals.inspection-send-all')
        @include('admin.install-equipments.modals.excel')

    </div>


    <div class="block {{ __('block.styles') }}">
        @include('admin.components.block-header',[
        'text' =>   __('purchase_requisitions.total_items') ,
        'block_icon_class' => 'icon-document',
        'block_header_class' => 'pb-2 mb-2',
        'block_option_id' => '_1',
        ])
    </div>
    @foreach ($list as $_index => $parent)
        <div class="block {{ __('block.styles') }}">
            <div class="block-content">
                <div class="row">
                    <div class="col-12">
                        <div class="block block-link-pop block-rounded block-bordered border-none mb-0"
                             href="javascript:void(0)">
                            <div class="block-header">
                                <div></div>
                                <div class="block-options">
                                    @can(Actions::Manage . '_' . Resources::InstallEquipment)
                                        <button type="button"
                                                class="btn btn-primary {{ $parent->is_allow_inspect ? '' : 'disabled' }}"
                                                onclick="callInspection('{{ $parent->group_id ?? '' }}')">
                                            <i class="icon-direct-up me-1"></i>{{ __('install_equipments.send_to_inspect') }}
                                        </button>
                                    @endcan
                                </div>
                            </div>
                            <div class="block-content block-content-full">
                                <div class="row">
                                    <div class="col-md-4 col-lg-3 col-xl-2">
                                        <div class="block-content block-content-full car-border">
                                            @if (isset($parent->car_id))
                                                <div class="py-1 mb-3">
                                                    <p class="fs-6 fw-bolder mb-1">{{ $parent->car_class }}</p>
                                                </div>
                                                <div class="py-1 text-center mb-3">
                                                    @if (isset($parent->image) && isset($parent->image['url']))
                                                        <img class="img-fluid img-link"
                                                             src='{{ $parent->image['url'] }}'
                                                             alt="">
                                                    @else
                                                        <img class="img-fluid img-link"
                                                             src='{{ asset('images/car-sample/car-placeholder.png') }}'
                                                             alt="">
                                                    @endif
                                                </div>
                                                <div class="py-1">
                                                    <p class="text-center">{!! badge_render(__('cars.class_' . $parent->car_status), __('cars.status_' . $parent->car_status)) !!} </p>
                                                    <p class="font-w800 mb-1">ประเภทรถ:
                                                        {{ $parent->type ? __('cars.rental_type_' . $parent->type) : '-' }}</p>
                                                    <p class="font-w800 mb-1">หมายเลขตัวถัง:
                                                        {{ $parent->car_chassis_no ?? ' - ' }}</p>
                                                    <p class="font-w800 mb-1">ทะเบียนรถ:
                                                        {{ $parent->car_license_plate ?? ' - ' }}</p>
                                                    <p class="font-w800 mb-1">ใบสั่งซื้อรถ:
                                                        {{ $parent->po_worksheet_no ?? ' - ' }}</p>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-md-8 col-lg-9 col-xl-10">
                                        <div class="table-wrap db-scroll">
                                            <table class="table table-striped my-table">
                                                <thead class="bg-body-dark">
                                                <tr>
                                                    <th>#</th>
                                                    <th style="width: 15%;">{{ __('install_equipments.install_equipment_no') }}
                                                    </th>
                                                    <th style="width: 15%;">
                                                        {{ __('install_equipments.install_equipment_po_no') }}</th>
                                                    <th style="width: 15%;">{{ __('install_equipments.supplier_en') }}</th>
                                                    <th style="width: 15%;">{{ __('install_equipments.accessory') }}</th>
                                                    <th style="width: 15%;">{{ __('install_equipments.created_at') }}</th>
                                                    <th style="width: 100px;" class="text-center">{{ __('lang.status') }}</th>
                                                    <th style="width: 100px;" class="sticky-col"></th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                @foreach ($parent->child_list as $_index => $item)
                                                    <tr @class(['item-row', 'hidden' => $loop->iteration > 4])>
                                                        <td>{{ $_index + 1 }}</td>
                                                        <td>{{ $item->worksheet_no }}</td>
                                                        <td>{{ $item->install_equipment_po ? $item->install_equipment_po->worksheet_no : '' }}
                                                        </td>
                                                        <td>{{ $item->supplier ? $item->supplier->name : '' }}</td>
                                                        <td class="tooltip-block">
                                                            <ul>
                                                                @foreach ($item->accessory_list as $index => $accessory)
                                                                    @if ($loop->iteration >= 3)
                                                                        <li class="fs-sm">...</li>
                                                                        @break
                                                                    @endif
                                                                    <li class="fs-sm">{{ $accessory['name'] ?? ' - ' }}</li>
                                                                @endforeach
                                                            </ul>
                                                            <span class="tooltip-content">
                                                            <ul>
                                                                @foreach ($item->accessory_list as $index => $accessory)
                                                                    <li class="fs-sm">{{ $accessory['name'] ?? ' - ' }}
                                                                    </li>
                                                                @endforeach
                                                            </ul>
                                                        </span>
                                                        </td>
                                                        <td>{{ $item->created_at ? get_thai_date_format($item->created_at, 'd/m/Y') : '-' }}
                                                        </td>
                                                        <td class="text-center">
                                                            @if (in_array($item->status, [InstallEquipmentStatusEnum::OVERDUE, InstallEquipmentStatusEnum::INSTALL_IN_PROCESS]))
                                                                {!! badge_render(
                                                                    __('install_equipments.class_' . $item->status),
                                                                    __('install_equipments.status_' . $item->status) . ' (' . $item->day_amount . ') วัน',
                                                                    'text-' . __('install_equipments.class_' . $item->status),
                                                                ) !!}
                                                            @else
                                                                {!! badge_render(
                                                                    __('install_equipments.class_' . $item->status),
                                                                    __('install_equipments.status_' . $item->status),
                                                                    'text-' . __('install_equipments.class_' . $item->status),
                                                                ) !!}
                                                            @endif
                                                        </td>
                                                        <td class="sticky-col ">
                                                            @include('admin.install-equipments.sections.dropdown-actions')
                                                        </td>
                                                    </tr>
                                                @endforeach
                                                @if (isset($group['install_equipments']) && count($group['install_equipments']) > 4)
                                                    <tr>
                                                        <td class="text-center" colspan="7">
                                                            <a
                                                                class="btn btn-sm btn-alt-secondary fw-semibold show-more-btn show-more">ดูเพิ่มเติม...</a>
                                                        </td>
                                                    </tr>
                                                @endif
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @if ($loop->last && $list->total() >= 5)
            <div class="block block-link-pop block-rounded block-bordered ">
                <div class="block-content ">
                    {!! $list->appends(\Request::except('page'))->render() !!}
                </div>
            </div>
        @endif
    @endforeach
@endsection

@include('admin.components.date-input-script')
@include('admin.components.select2-default')
@include('admin.components.sweetalert')
@include('admin.components.list-delete')
@include('admin.components.select2-default')


@include('admin.components.date-input-script')

@include('admin.components.select2-ajax', [
    'id' => 'excel_supplier_id',
    'url' => route('admin.util.select2-install-equipment.supplier-install-equipment'),
    'modal' => '#excel-modal',
])

@include('admin.components.select2-ajax', [
    'id' => 'excel_install_equipment_id',
    'parent_id' => 'excel_supplier_id',
    'url' => route('admin.util.select2-install-equipment.install-equipment-po-by-supplier'),
    'modal' => '#excel-modal',
])

@include('admin.components.select2-ajax', [
    'id' => 'lot_no',
    'url' => route('admin.util.select2-install-equipment.lot-no'),
])

@include('admin.components.select2-ajax', [
    'id' => 'install_equipment_no',
    'url' => route('admin.util.select2-install-equipment.all-install-equipments'),
])

@include('admin.components.select2-ajax', [
    'id' => 'purchase_order_no',
    'url' => route('admin.util.select2-install-equipment.po-of-install-equipments'),
])

@include('admin.components.select2-ajax', [
    'id' => 'supplier_id',
    'url' => route('admin.util.select2-install-equipment.supplier-install-equipment'),
])

@include('admin.components.select2-ajax', [
    'id' => 'chassis_no',
    'url' => route('admin.util.select2-install-equipment.chassis-install-equipments'),
])

@include('admin.components.select2-ajax', [
    'id' => 'license_plate',
    'url' => route('admin.util.select2-install-equipment.license-plate-install-equipments'),
])

@include('admin.install-equipments.scripts.inspection-script')
@include('admin.install-equipments.scripts.excel-script')
@push('scripts')
    <script>
        $(document).ready(function () {
            const SHOW_MORE = "ดูเพิ่มเติม...";
            const SHOW_LESS = "ดูน้อยลง";

            $("table").each(function () {
                const rows = $(this).find(".item-row");
                const showMoreBtn = $(this).find(".show-more-btn");

                let visibleRowCount = 4;
                rows.slice(visibleRowCount).addClass("hidden");
                showMoreBtn.on("click", function () {
                    if ($(this).hasClass("show-more")) {
                        rows.slice(visibleRowCount, visibleRowCount + 4).removeClass("hidden");
                        visibleRowCount += 4;

                        if (visibleRowCount >= rows.length) {
                            $(this).text(SHOW_LESS).removeClass("show-more").addClass("show-less");
                        }
                    } else {
                        rows.slice(visibleRowCount - 4, visibleRowCount).addClass("hidden");
                        visibleRowCount -= 4;

                        if (visibleRowCount <= 4) {
                            $(this).text(SHOW_MORE).removeClass("show-less").addClass("show-more");
                        }
                    }
                });
            });
        });

        // You can use JavaScript to create tooltips dynamically or to add functionality to the tooltips
    </script>
@endpush
