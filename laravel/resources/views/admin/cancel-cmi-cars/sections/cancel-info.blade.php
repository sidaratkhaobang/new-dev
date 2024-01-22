<div class="block {{ __('block.styles') }}">
    @include('admin.components.block-header', [
        'text' =>  __('cmi_cars.cancel_info'),

    ])
    <div class="block-content">
        <div class="row mb-4">
            <div class="col-sm-4">
                <x-forms.input-new-line id="reason" :value="$cancel_insurance->reason" 
                    :label="__('cmi_cars.cancel_reason')" />
            </div>
            <div class="col-sm-8">
                <x-forms.input-new-line id="cancel_remark" :value="$cancel_insurance->remark" 
                    :label="__('lang.remark')" />
            </div>
        </div>
        <hr>
        <div class="row mb-4">
            <div class="col-sm-3">
                <x-forms.date-input id="request_cancel_date" :value="$cancel_insurance->request_cancel_date" :label="__('cmi_cars.request_cancel_date')" 
                    :optionals="['date_enable_time' => true]" />
            </div>
            <div class="col-sm-3">
                <x-forms.date-input id="actual_cancel_date" :value="$cancel_insurance->actual_cancel_date" :label="__('cmi_cars.actual_cancel_date')" 
                    :optionals="['date_enable_time' => true]" />
            </div>  
        </div>
    </div>
</div>
