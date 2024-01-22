<div id="index-table" class="block {{ __('block.styles') }}">
    @include('admin.components.block-header', [
           'text' => __('insurance_deduct.detail').__('insurance_deduct.page_title'),
       ])
    <div class="block-content">
        <div class="justify-content-between mb-4">
            <div class="row push mb-4">
                <div class="col-sm-3">
                    <x-forms.date-input id="deduct_date" name="deduct_date" :value="$d?->date_deductible"
                                        :label="__('insurance_deduct.DD')"
                                        :optionals="['required' => false]"/>
                </div>
                <div class="col-sm-3">
                    <x-forms.input-new-line id="deduct_doc_dd" :value="$d?->doc_deductible"
                                            :label="__('insurance_deduct.Doc_DD')"/>
                </div>
                <div class="col-sm-3">
                    <x-forms.input-new-line id="deduct_all_damage"
                                            :value="$d?->total_damages ?number_format($d?->total_damages,2):null"
                                            :label="__('insurance_deduct.all_damage')"
                                            :optionals="['input_class' => 'number-format col-sm-4']"/>
                </div>
                <div class="col-sm-3">
                    <x-forms.input-new-line id="deduct_deductible" :value="$d?->deductible"
                                            :label="__('insurance_deduct.deductible')"/>
                </div>
            </div>
        </div>
    </div>
</div>
