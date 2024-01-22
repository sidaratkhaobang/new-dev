<div class="justify-content-between">
    <form action="" method="GET" id="form-search">
        <div class="mb-4 form-group row">
            <div class="col-sm-3">
                <x-forms.select-option id="worksheet_no" :value="$worksheet_no" :list="$worksheet_no_list" :label="__('เลขที่สัญญา')" />
            </div>
            <div class="col-sm-3">
                <x-forms.select-option id="contract_type" :value="$contract_type" :list="$contract_type_list" :label="__('ประเภทสัญญา')" />
            </div>
            <div class="col-sm-3">
                <x-forms.select-option id="car_id" :value="$car_id" :list="$car_id_list" :label="__('หมายเลขตัวถัง/ทะเบียนรถ')" />
            </div>
            <div class="col-sm-3">
                <x-forms.select-option id="customer_id" :value="$customer_id" :list="$customer_id_list" :label="__('ลูกค้า')" />
            </div>
        </div>
            <div class="mb-4 form-group row">

            <div class="col-sm-3">
                <x-forms.date-input id="contract_start_date" :value="$contract_start_date" :label="__('วันที่เริ่มสัญญา')" :optionals="['placeholder' => __('lang.select_date')]" />
            </div>
            <div class="col-sm-3">
                <x-forms.date-input id="contract_end_date" :value="$contract_end_date" :label="__('วันที่สิ้นสุดสัญญา')" :optionals="['placeholder' => __('lang.select_date')]" />
            </div>
        </div>
        @include('admin.components.btns.search')
    </form>
</div>
