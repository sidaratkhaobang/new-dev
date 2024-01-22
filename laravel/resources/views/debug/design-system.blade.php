@extends('admin.layouts.layout')
@section('page_title', 'ตัวอย่างelement')
@section('page_title_no', 'INS20230500005')
@section('history')
    @include('admin.components.btns.history')
    @include('admin.components.transaction-modal')
@endsection
@section('page_title_sub')
    {!! badge_render(
        __('lang.class_' .  ApproveStatusEnum::CONFIRM),
        __('lang.status_' . ApproveStatusEnum::CONFIRM),
    ) !!}
@endsection
@section('btn-nav')
    <a href="#" class="btn btn-primary float-end ">
        <i class="icon-printer"></i>
        {{ __('accident_informs.print_repair_sheet') }}
    </a>
@endsection

@push('custom_styles')
    <style>
        /* date group*/
        .input-group-text {
            padding: 0.7rem .75rem;
            background-color: transparent;
            border-radius: 0;
            color: #6c757d;
        }

        /* input tag */
        .tag-field {
            display: flex;
            flex-wrap: wrap;
            border: 1px solid #ddd;
            border-radius: 4px;
        }

        .form-control.js-tag-input {
            border: none;
            transition: none;
        }

        input {
            border: 0;
            outline: 0;
        }

        .tag {

            display: block;
            align-items: center;
            height: 30px;
            margin-right: 5px;
            margin-bottom: 1px;
            padding: 0 8px;
            color: #fff;
            background: #0665d0;
            border-radius: 6px;
            cursor: pointer;
        }

        .tag-close {
            display: inline-block;
            margin-left: 0;
            width: 0;
            transition: 0.2s all;
            overflow: hidden;
        }

        .tag:hover .tag-close {
            margin-left: 10px;
            width: 10px;
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
        <div class="block-content">
            <div class="justify-content-between mb-4">
                <form action="" method="GET" id="form-search">
                    <div class="form-group row push mb-4">
                        <div class="col-sm-3">
                            <x-forms.select-option id="select_option" :value="null" :list="$select_option"
                                :label="__('lang.status')" />
                        </div>
                        <div class="col-sm-3">
                            <x-forms.input-new-line id="text_input" :value="null" :label="__('lang.full_name')" />
                        </div>
                        <div class="col-sm-3">
                            <x-forms.input-new-line id="input_number" :value="number_format('10000', 2)" :label="__('products.standard_price')"
                                :optionals="['required' => true, 'input_class' => 'number-format col-sm-4']" />
                        </div>
                        <div class="col-sm-3">
                            <x-forms.select-option id="customer_group" :value="[]" :list="$select_option"
                                :label="__('customers.customer_group')" :optionals="['multiple' => true]" />
                        </div>
                    </div>
                    <div class="form-group row push mb-4">
                        <div class="col-sm-3">
                            <label for="input_tag" class="text-start col-form-label">Input Tag</label>
                            <div class="tag-field js-tags" id="js-tag-car">
                                <input type="text" class="form-control js-tag-input" id="input_tag" name="input_tag"
                                    placeholder="ระบุข้อมูล...">
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <x-forms.date-input id="date" :value="null" :label="__('lang.created_at')" :optionals="['placeholder' => __('lang.select_date')]" />
                        </div>
                        <div class="col-sm-3">
                            <x-forms.date-input id="date_time" :value="null" :label="__('short_term_rentals.return_datetime')" :optionals="[
                                'date_enable_time' => true,
                                'placeholder' => __('lang.select_date'),
                                'required' => true,
                            ]" />
                        </div>
                        <div class="col-sm-3">
                            <label class="text-start col-form-label"
                                for="date_group">{{ __('purchase_orders.delivery_date') }}</label>
                            <div class="form-group">
                                <div class="input-daterange input-group" data-date-format="mm/dd/yyyy" data-week-start="1"
                                    data-autoclose="true" data-today-highlight="true">
                                    <input type="text" class="js-flatpickr form-control flatpickr-input"
                                        id="from_date_group" name="from_date_group"
                                        placeholder="{{ __('lang.select_date') }}" data-week-start="1" data-autoclose="true"
                                        data-today-highlight="true">
                                    <div class="input-group-prepend input-group-append">
                                        <span class="input-group-text font-w600">
                                            <i class="fa fa-fw fa-arrow-right"></i>
                                        </span>
                                    </div>
                                    <input type="text" class="js-flatpickr form-control flatpickr-input"
                                        id="to_date_group" name="to_date_group" placeholder="{{ __('lang.select_date') }}"
                                        data-week-start="1" data-autoclose="true" data-today-highlight="true">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-group row push mb-4">
                        <div class="col-sm-3">
                            <x-forms.radio-inline id="radio" :value="null" :label="__('lang.status')" :list="[
                                ['name' => __('lang.yes'), 'value' => 1],
                                ['name' => __('lang.no'), 'value' => 2],
                            ]" />
                        </div>
                        <div class="col-sm-4">
                            <x-forms.checkbox-inline id="checkbox" :list="$days" :label="__('products.reserve_date')"
                                :value="$booking_day_arr" />
                        </div>
                    </div>
                    <div class="form-group row push mb-4">
                        <div class="col-sm-3">
                            <x-forms.input-new-line id="text_input_disabled" :value="'disabled'" :label="__('lang.full_name')" />
                        </div>
                        <div class="col-sm-6">
                            <x-forms.upload-image :id="'upload_file'" :label="__('drivers.citizen_file')" />
                        </div>
                    </div>
                    <div class="row push mb-4">
                        <div class="col-sm-12">
                            <x-forms.text-area-new-line id="text_area" :value="null" :label="__('creditors.contact_address')" />
                        </div>
                    </div>
                    @include('admin.components.btns.search')
                </form>
            </div>
        </div>
    </div>

    <div class="block {{ __('block.styles') }}">
        <div class="block-content">
            <x-blocks.title-header text="ตัวอย่าง element ใน block content แบบมี underline"/>
            <x-blocks.title-header class="text-primary" text="ตัวอย่าง element ใน block content " :noline="true" icon='icon-settings' />
            <br>
            <button class="btn btn-primary">บันทึก</button>

            <button class="btn btn-success">บันทึก</button>

            <button class="btn btn-warning">บันทึก</button>

            <button class="btn btn-danger">บันทึก</button>

            <button class="btn btn-info">บันทึก</button>

            <button class="btn btn-secondary">บันทึก</button>

            <button class="btn btn-purple">ข้อความ</button>
            <br><br>
            <button class="btn btn-primary"><i class="fa fa-magnifying-glass"></i> ค้นหา</button>
            <br><br>
            <span class="badge badge-custom badge-bg-primary">อยู่ระหว่างดำเนินการ</span>
            <span class="badge badge-custom badge-bg-success">อยู่ระหว่างดำเนินการ ทดสอบข้อความยาว</span>
            <span class="badge badge-custom badge-bg-warning">สำเร็จ</span>
            <span class="badge badge-custom badge-bg-danger">สำเร็จ</span>
            <span class="badge badge-custom badge-bg-info">สำเร็จ</span>
            <span class="badge badge-custom badge-bg-secondary">สำเร็จ</span>
            <br><br>
            <span class="badge badge-custom badge-bg-purple">สำเร็จ</span>
            <br><br>
            <div class="btn-group" role="group" aria-label="Horizontal Outline Primary">
                <button type="button" class="btn btn-primary">Left</button>
                <button type="button" class="btn btn-outline">Middle</button>
                <button type="button" class="btn btn-outline">Right</button>
            </div>
            <br><br>
            {{-- <x-btns.icon btn-text="{{ __('accessories.add_new') }}" icon-class="fa fa-plus-circle"
                route="{{ route('admin.accessories.create') }}" /> --}}
            <button class="btn btn-success" onclick="openExcelModal()"><i class="fa fa-fw fa-download  me-1"></i>
                {{ __('gps.download_excel') }}</button>
            <a target="_blank" href="#" class="btn btn-primary"><i class="fa fa-print"></i>&nbsp;
                {{ __('repair_orders.btn_print') }}
            </a>

        </div>
    </div>

    <div class="block {{ __('block.styles') }}">
    @section('block_options_1')
        <div class="block-options-item">
            <x-btns.add-new btn-text="{{ __('cars.add_car') }}" route-create="{{ route('admin.cars.create') }}" />
        </div>
    @endsection
    @include('admin.components.block-header', [
        'text' => __('lang.total_items'),
        'block_icon_class' => 'icon-document',
        'block_option_id' => '_1',
    ])
    <div class="block-content">
        <div class="table-wrap db-scroll">
            <table class="table table-striped table-vcenter">
                <thead class="bg-body-dark">
                    <tr>
                        <th style="width: 25%;"><a
                                href="http://127.0.0.1:8000/admin/car-service-types?sort=engine_no&amp;direction=desc">หมายเลขเครื่องยนต์</a>
                            <i class="fa fa-angle-up"></i>
                        </th>
                        <th style="width: 25%;"><a
                                href="http://127.0.0.1:8000/admin/car-service-types?sort=chassis_no&amp;direction=asc">เลขตัวถัง</a>
                            <i class="fa fa-angle"></i>
                        </th>
                        <th style="width: 25%;"><a
                                href="http://127.0.0.1:8000/admin/car-service-types?sort=license_plate&amp;direction=asc">เลขทะเบียน</a>
                            <i class="fa fa-angle"></i>
                        </th>
                        <th style="width: 20%;"><a
                                href="http://127.0.0.1:8000/admin/car-service-types?sort=class_name&amp;direction=asc">รุ่นรถ</a>
                            <i class="fa fa-angle"></i>
                        </th>
                        <th class="text-end">ราคา</th>
                        <th class="text-center">สถานะ</th>
                        <th style="width: 100px;" class="sticky-col text-center">เครื่องมือ</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>003261</td>
                        <td>HKQ00541J213</td>
                        <td>6100-01290</td>
                        <td>Hacker-Craft 27' Sport 2013</td>
                        <td class="text-end">{{ number_format(10000, 2) }}</td>
                        <td>{!! badge_render(__('info'), __('สำเร็จ'), null) !!}</td>
                        <td class="sticky-col text-center">
                            <x-tables.dropdown 
                                :view-route="route('admin.car-tires.show', ['car_tire' => 'id_here'])" 
                                :edit-route="route('admin.car-tires.edit', ['car_tire' => 'id_here'])" 
                                :delete-route="route('admin.car-tires.destroy', ['car_tire' => 'id_here'])"
                                :view-permission="Actions::View . '_' . Resources::CarTire" 
                                :manage-permission="Actions::Manage . '_' . Resources::CarTire">
                                <a href="#" class="dropdown-item"><i class="fa fa-plus-circle me-1"></i> Slot Item</a>
                                <x-slot name="end_slot">
                                    <a href="#" class="dropdown-item"><i class="fa fa-minus-circle me-1"></i>Slot Bottom</a>
                                </x-slot>
                            </x-tables.dropdown>
                        </td>
                    </tr>
                    <tr>
                        <td>009786</td>
                        <td>HKQ00516C516</td>
                        <td>6100-01282</td>
                        <td>Hacker-Craft 26' Sterling Runabout 2016</td>
                        <td class="text-end">{{ number_format(10000, 2) }}</td>
                        <td>{!! badge_render(__('info'), __('สำเร็จ'), null) !!}</td>
                        <td class="sticky-col text-center">
                            <div class="btn-group">
                                <div class="col-sm-12">
                                    <div class="dropdown dropleft">
                                        <button type="button" class="btn btn-sm btn-alt-secondary dropdown-toggle"
                                            id="dropdown-dropleft-dark" data-bs-toggle="dropdown"
                                            aria-haspopup="true" aria-expanded="false">
                                            <i class="fa fa-ellipsis-vertical"></i>
                                        </button>
                                        <div class="dropdown-menu" aria-labelledby="dropdown-dropleft-dark">
                                            <a class="dropdown-item"
                                                href="http://127.0.0.1:8000/admin/car-service-types/98f5ac26-d299-4094-97ca-27214b36cee0"><i
                                                    class="fa fa-eye me-1"></i> ดูข้อมูล</a>
                                            <a class="dropdown-item"
                                                href="http://127.0.0.1:8000/admin/car-service-types/98f5ac26-d299-4094-97ca-27214b36cee0/edit"><i
                                                    class="far fa-edit me-1"></i> แก้ไข</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td>1GD-0387053</td>
                        <td>MR0GA3GS000013689</td>
                        <td>6กช 4596</td>
                        <td>TOYOTA FORTUNER 2.8 TRD Sportivo 2WD (Im.) 2017</td>
                        <td class="text-end">{{ number_format(10000, 2) }}</td>
                        <td>{!! badge_render(__('info'), __('สำเร็จ'), null) !!}</td>
                        <td class="sticky-col text-center">
                            <div class="btn-group">
                                <div class="col-sm-12">
                                    <div class="dropdown dropleft">
                                        <button type="button" class="btn btn-sm btn-alt-secondary dropdown-toggle"
                                            id="dropdown-dropleft-dark" data-bs-toggle="dropdown"
                                            aria-haspopup="true" aria-expanded="false">
                                            <i class="fa fa-ellipsis-vertical"></i>
                                        </button>
                                        <div class="dropdown-menu" aria-labelledby="dropdown-dropleft-dark">
                                            <a class="dropdown-item"
                                                href="http://127.0.0.1:8000/admin/car-service-types/98f5ac29-d6eb-4187-945d-7f6daa02a256"><i
                                                    class="fa fa-eye me-1"></i> ดูข้อมูล</a>
                                            <a class="dropdown-item"
                                                href="http://127.0.0.1:8000/admin/car-service-types/98f5ac29-d6eb-4187-945d-7f6daa02a256/edit"><i
                                                    class="far fa-edit me-1"></i> แก้ไข</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <nav>
            <ul class="pagination">
                <li class="page-item disabled" aria-disabled="true" aria-label="« Previous">
                    <span class="page-link" aria-hidden="true">‹</span>
                </li>
                <li class="page-item active" aria-current="page"><span class="page-link">1</span></li>
                <li class="page-item"><a class="page-link"
                        href="http://127.0.0.1:8000/admin/products?sort=sku&amp;direction=asc&amp;page=2">2</a></li>
                <li class="page-item"><a class="page-link"
                        href="http://127.0.0.1:8000/admin/products?sort=sku&amp;direction=asc&amp;page=3">3</a></li>
                <li class="page-item"><a class="page-link"
                        href="http://127.0.0.1:8000/admin/products?sort=sku&amp;direction=asc&amp;page=4">4</a></li>
                <li class="page-item"><a class="page-link"
                        href="http://127.0.0.1:8000/admin/products?sort=sku&amp;direction=asc&amp;page=5">5</a></li>
                <li class="page-item"><a class="page-link"
                        href="http://127.0.0.1:8000/admin/products?sort=sku&amp;direction=asc&amp;page=6">6</a></li>
                <li class="page-item"><a class="page-link"
                        href="http://127.0.0.1:8000/admin/products?sort=sku&amp;direction=asc&amp;page=7">7</a></li>
                <li class="page-item"><a class="page-link"
                        href="http://127.0.0.1:8000/admin/products?sort=sku&amp;direction=asc&amp;page=8">8</a></li>
                <li class="page-item"><a class="page-link"
                        href="http://127.0.0.1:8000/admin/products?sort=sku&amp;direction=asc&amp;page=9">9</a></li>
                <li class="page-item"><a class="page-link"
                        href="http://127.0.0.1:8000/admin/products?sort=sku&amp;direction=asc&amp;page=10">10</a></li>

                <li class="page-item disabled" aria-disabled="true"><span class="page-link">...</span></li>
                <li class="page-item"><a class="page-link"
                        href="http://127.0.0.1:8000/admin/products?sort=sku&amp;direction=asc&amp;page=40">40</a></li>
                <li class="page-item"><a class="page-link"
                        href="http://127.0.0.1:8000/admin/products?sort=sku&amp;direction=asc&amp;page=41">41</a></li>
                <li class="page-item">
                    <a class="page-link"
                        href="http://127.0.0.1:8000/admin/products?sort=sku&amp;direction=asc&amp;page=2"
                        rel="next" aria-label="Next »">›</a>
                </li>
            </ul>
        </nav>
    </div>
    {{-- modal --}}
    <div class="modal fade" id="excel-modal" aria-labelledby="excel-modal" aria-hidden="false">
        <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-popout">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="excel-modal-label">ดาวน์โหลดไฟล์ Excel
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row push mb-4">
                        <div class="col-sm-3">
                            <label class="text-start  col-form-label" for="excel_supplier_id">
                                Supplier
                            </label>
                            <select name="excel_supplier_id" id="excel_supplier_id"
                                class="form-control js-select2-default select2-hidden-accessible" style="width: 100%;"
                                tabindex="-1" aria-hidden="true" data-select2-id="excel_supplier_id">
                                <option value="" data-select2-id="54">
                                    - กรุณาเลือก -
                                </option>
                            </select><span class="select2 select2-container select2-container--default" dir="ltr"
                                data-select2-id="53" style="width: 100%;"><span class="selection"><span
                                        class="select2-selection select2-selection--single" role="combobox"
                                        aria-haspopup="true" aria-expanded="false" tabindex="0"
                                        aria-disabled="false"
                                        aria-labelledby="select2-excel_supplier_id-container"><span
                                            class="select2-selection__rendered"
                                            id="select2-excel_supplier_id-container" role="textbox"
                                            aria-readonly="true"><span class="select2-selection__placeholder"> -
                                                กรุณาเลือก - </span></span><span class="select2-selection__arrow"
                                            role="presentation"><b role="presentation"></b></span></span></span><span
                                    class="dropdown-wrapper" aria-hidden="true"></span></span>
                        </div>
                        <div class="col-sm-3">
                            <label class="text-start  col-form-label" for="excel_install_equipment_id">
                                เลขที่ใบสั่งซื้ออุปกรณ์
                            </label>
                            <select name="excel_install_equipment_id" id="excel_install_equipment_id"
                                class="form-control js-select2-default select2-hidden-accessible" style="width: 100%;"
                                tabindex="-1" aria-hidden="true" data-select2-id="excel_install_equipment_id">
                                <option value="" data-select2-id="56">
                                    - กรุณาเลือก -
                                </option>
                            </select><span class="select2 select2-container select2-container--default" dir="ltr"
                                data-select2-id="55" style="width: 100%;"><span class="selection"><span
                                        class="select2-selection select2-selection--single" role="combobox"
                                        aria-haspopup="true" aria-expanded="false" tabindex="0"
                                        aria-disabled="false"
                                        aria-labelledby="select2-excel_install_equipment_id-container"><span
                                            class="select2-selection__rendered"
                                            id="select2-excel_install_equipment_id-container" role="textbox"
                                            aria-readonly="true"><span class="select2-selection__placeholder"> -
                                                กรุณาเลือก - </span></span><span class="select2-selection__arrow"
                                            role="presentation"><b role="presentation"></b></span></span></span><span
                                    class="dropdown-wrapper" aria-hidden="true"></span></span>
                        </div>
                        <div class="col-sm-3 align-self-end">
                            <label for=""></label>
                            <button type="button" class="btn btn-primary" onclick="addExcel()">
                                <i class="fa fa-fw fa-plus me-1"></i> เพิ่ม
                            </button>
                        </div>
                    </div>
                    <div id="install-equipment-excel" data-detail-uri="" data-title="" class="mb-4">
                        <div class="table-wrap">
                            <table class="table table-striped">
                                <thead class="bg-body-dark">
                                    <tr>
                                        <th>#</th>
                                        <th>เลขที่ใบสั่งซื้ออุปกรณ์</th>
                                        <th>เลขที่ใบขอติดตั้ง</th>
                                        <th>Supplier</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr class="table-empty">
                                        <td colspan="5" class="text-center">" ไม่มีข้อมูล "</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-clear-search"
                        data-bs-dismiss="modal">ยกเลิก</button>
                    <button type="button" class="btn btn-primary" onclick="exportExcel()">ดาวน์โหลด</button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@include('admin.components.select2-default')
@include('admin.components.date-input-script')
@include('admin.accident-informs.scripts.input-tag')
@include('admin.components.upload-image-scripts')
@include('admin.components.upload-image', [
    'id' => 'upload_file',
    'max_files' => 1,
    'accepted_files' => '.jpg,.jpeg,.bmp,.png,.pdf',
    'mock_files' => [],
    'show_url' => true,
])

@push('scripts')
<script>
    $('#text_input_disabled').prop('disabled', true);

    function openExcelModal() {
        $("#excel-modal").modal("show");
    }
</script>
@endpush
