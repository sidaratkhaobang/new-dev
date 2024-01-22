<div class="row">
    @if (in_array($step_form_check_condition->form_type, [InspectionFormEnum::NEWCAR]))
        <div class="col-sm-2">
            <label class="text-start col-form-label">{{ __('inspection_cars.amount_oil_out') }}</label>
            <input type="range" min="0" max="100" oninput="showVal(this.value)" onchange="showVal(this.value)"
                id="oil_quantity" name="oil_quantity" step="1" class="w-100 mt-2"
                @if ($step_form_status->oil_quantity != null || '') value="{{ $step_form_status->oil_quantity }}" @else value="0" @endif />
        </div>
        <div class="col-sm-1 mt-3">
            <p></p>
            <input class="form-control" oninput="showVal2(this.value)" onchange="showVal2(this.value)" type="number"
                min="0" max="100" id="valBox" class="valBox" style="width: 80px;"
                @if ($step_form_status->oil_quantity != null || '') value="{{ $step_form_status->oil_quantity }}" @else value="0" @endif />
        </div>
    @else
    <div class="col-sm-2">
        <label class="text-start col-form-label">{{ __('inspection_cars.amount_oil_out') }}</label>
        <input type="range" min="0" max="100" oninput="showVal(this.value)" onchange="showVal(this.value)"
            id="oil_quantity" name="oil_quantity" step="1" class="w-100 mt-2"
            @if ($step_form_status->oil_quantity != null || '') value="{{ $step_form_status->oil_quantity }}" @else value="0" @endif />
    </div>
    <div class="col-sm-1 mt-3">
        <p></p>
        <input class="form-control" oninput="showVal2(this.value)" onchange="showVal2(this.value)" type="number"
            min="0" max="100" id="valBox" class="valBox" style="width: 80px;"
            @if ($step_form_status->oil_quantity != null || '') value="{{ $step_form_status->oil_quantity }}" @else value="0" @endif />
    </div>
        @if ($step_form_check_condition->is_need_dpf == STATUS_ACTIVE)
            <div class="col-sm-2">
                <label class="text-start col-form-label">{{ __('inspection_cars.amount_dpf_out') }}</label>
                <input type="range" min="0" max="100" oninput="showVal3(this.value)"
                    onchange="showVal3(this.value)" id="dpf_solution" name="dpf_solution" step="1" class="w-100 mt-2" @if ($step_form_status->dpf_solution != null || '') value="{{ $step_form_status->dpf_solution }}" @else value="0" @endif/>
            </div>
            <div class="col-sm-1 mt-3">
                <p></p>
                <input class=" form-control" oninput="showVal4(this.value)" onchange="showVal4(this.value)" type="number"
                    min="0" max="100" id="valBox2" class="valBox" style="width: 80px;"  @if ($step_form_status->dpf_solution != null || '') value="{{ $step_form_status->dpf_solution }}" @else value="0" @endif   />
            </div>
        @endif
    @endif
    <div class="col-sm-3">
        <x-forms.input-new-line id="mileage" :value="$step_form_status->mileage" :label="__('inspection_cars.mile_out')"  :optionals="['type' => 'text', 'input_class' => 'number-format', 'required' => true]" />
    </div>
</div>



<script>
    function showVal(val) {
        document.getElementById("valBox").value = val;
    }

    function showVal2(val) {
        document.getElementById("oil_quantity").value = val;
    }

    function showVal3(val) {
        document.getElementById("valBox2").value = val;
    }

    function showVal4(val) {
        document.getElementById("range2").value = val;
    }
</script>
