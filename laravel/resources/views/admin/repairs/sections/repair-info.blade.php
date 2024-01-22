<div class="block {{ __('block.styles') }}">
    <div class="block-content">
        <div class="d-md-flex justify-content-md-between align-items-md-center mb-3">
            <div>
                <h4><i class="fa fa-file-lines me-1"></i>{{ __('repairs.repair_table') }}</h4>
            </div>
        </div>
        <div class="row push mb-4">
            <div class="col-sm-3">
                <x-forms.select-option id="repair_type" :list="$repair_type_list" :value="$d->repair_type" :label="__('repairs.repair_type')"
                    :optionals="['required' => true]" />
            </div>
            <div class="col-sm-3">
                <x-forms.date-input id="repair_date" :value="$d->repair_date" :label="__('repairs.repair_date')" :optionals="['date_enable_time' => true]"
                    :optionals="['required' => true, 'date_enable_time' => true, 'placeholder' => __('lang.select_date')]" />
            </div>
            <div class="col-sm-3">
                <x-forms.select-option id="informer_type" :list="$informer_type_list" :value="$d->informer_type" :label="__('repairs.informer_type')" />
            </div>
            <div class="col-sm-3" id="informer_id" style="display: none;">
                <x-forms.select-option id="informer" :list="$informers" :value="$d->informer" :label="__('repairs.informer')" />
            </div>
        </div>
        <div class="row push mb-4">
            <div class="col-sm-3">
                <x-forms.input-new-line id="contact" :value="$d->contact" :label="__('repairs.contact')" :optionals="['required' => true]" />
            </div>
            <div class="col-sm-3">
                <x-forms.input-new-line id="tel" :value="$d->tel" :label="__('repairs.tel')" :optionals="['required' => true]" />
            </div>
            <div class="col-sm-3">
                <x-forms.input-new-line id="mileage" :value="$d->mileage" :label="__('repairs.mileage')" :optionals="['input_class' => 'number-format col-sm-4', 'required' => true]" />
            </div>
            <div class="col-sm-3">
                <x-forms.input-new-line id="place" :value="$d->place" :label="__('repairs.place')" />
            </div>
        </div>
        <div class="row push mb-4">
            <div class="col-sm-9">
                <x-forms.input-new-line id="remark" :value="$d->remark" :label="__('repairs.remark')" />
            </div>
            <div class="col-sm-3">
                <x-forms.upload-image :id="'repair_documents'" :label="__('repairs.document')" />
            </div>
        </div>
    </div>
</div>
