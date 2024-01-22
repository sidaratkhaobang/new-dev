<div class="block-content">
    <div class="justify-content-between">
        <form action="" method="GET" id="form-search">
            <div class="row mb-4">
                <div class="col-sm-3">
                    <x-forms.select-option id="install_equipment_no" :value="$install_equipment_no" :list="null"
                        :label="__('install_equipment_pos.ie_worksheet_no')"
                        :optionals="['ajax' => true, 'default_option_label' => $install_equipment_no_text]" />
                </div>
                <div class="col-sm-3">
                    <x-forms.select-option id="purchase_order_no" :value="$purchase_order_no"
                        :list="null"
                        :label="__('install_equipments.po_no')"
                        :optionals="['ajax' => true, 'default_option_label' => $purchase_order_no_text]" />
                </div>
                <div class="col-sm-3">
                    <x-forms.select-option id="supplier_id" :value="$supplier_id" :list="null"
                        :label="__('install_equipments.supplier_en')"
                        :optionals="['ajax' => true, 'default_option_label' => $supplier_text]" />
                </div>
                <div class="col-sm-3">
                    <x-forms.select-option id="install_equipment_po_no" :value="$install_equipment_po_no"
                        :list="$install_equipment_po_no_list"
                        :label="__('install_equipments.install_equipment_po_no')"/>
                </div>
            </div>
            <div class="row mb-4">
                <div class="col-sm-3">
                    <x-forms.date-input id="create_date" :value="$create_date"
                        :label="__('install_equipments.created_at')"/>
                </div>
                <div class="col-sm-3">
                    <x-forms.select-option id="chassis_no" :value="$chassis_no" :list="null"
                        :label="__('install_equipments.chasis_no')"
                        :optionals="['ajax' => true, 'default_option_label' => $chassis_no_text]" />
                </div>
                <div class="col-sm-3">
                    <x-forms.select-option id="license_plate" :value="$license_plate" :list="null"
                        :label="__('install_equipments.license_plate')" 
                        :optionals="['ajax' => true, 'default_option_label' => $license_plate_text]" />
                </div>
                <div class="col-sm-3">
                    <x-forms.select-option id="status_id" :value="$status_id" :list="$status_list"
                        :label="__('lang.status')"/>
                </div>
            </div>
            <div class="row mb-4">
                <div class="col-sm-3">
                    <x-forms.select-option id="lot_no" :value="$lot_no" :list="[]"
                        :label="__('install_equipments.lot')"  :optionals="['ajax' => true, 'default_option_label' => $lot_no]"/>
                </div>
            </div>
            @include('admin.components.btns.search')
        </form>
    </div>
</div>