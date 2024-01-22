<div id="index-table" class="block {{ __('block.styles') }}">
    @include('admin.components.block-header', [
           'text' => __('insurance_deduct.title_content_customer'),
       ])
    <div class="block-content">
        <div class="justify-content-between mb-4">
            <div class="row push mb-4">
                <div class="col-sm-3">
                    <x-forms.label id="car_license_plate" :value="$d?->car?->license_plate ?? '-'"
                                   :label="__('insurance_deduct.license_plate')"/>
                </div>
                <div class="col-sm-3">
                    <x-forms.label id="car_chassis_no" :value="$d?->car?->chassis_no ?? '-'"
                                   :label="__('insurance_deduct.chassis_no')"/>
                </div>
                <div class="col-sm-3">
                    <x-forms.label id="car_engine_no" :value="$d?->car?->engine_no ?? '-'"
                                   :label="__('insurance_deduct.engine_no')"/>
                </div>
            </div>
            <div class="row push mb-4">
                <div class="col-sm-3">
                    <x-forms.label id="cutomer_detail" :value="$dataCustomer['nameCustomer'] ?? '-'"
                                   :label="__('insurance_deduct.customer')"/>
                </div>
                <div class="col-sm-3">
                    <x-forms.label id="cutomer_group" :value="$dataCustomer['nameGroupCustomer'] ?? ''"
                                   :label="__('insurance_deduct.customer_group')"/>
                </div>
            </div>
        </div>
    </div>
</div>
