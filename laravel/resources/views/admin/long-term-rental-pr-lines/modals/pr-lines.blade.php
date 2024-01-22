<div class="modal fade" id="modal-pr-line" tabindex="-1" aria-labelledby="modal-pr-lines" aria-hidden="false">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-popout" style="width:1250px;">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="pr-line-modal-label">เพิ่มข้อมูล</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <h4 class="fw-light text-gray-darker">{{ __('long_term_rentals.pr_line_detail') }}</h4>
                <hr>
                <div class="row push mb-4">
                    <div class="col-sm-6">
                        <x-forms.select-option id="temp_lt_line" :value="null" :list="null" :label="__('long_term_rentals.car_class_and_color')"
                            :optionals="['ajax' => true, 'required' => true]" />
                    </div>
                    <div class="col-sm-3">
                        <x-forms.input-new-line id="temp_lt_line_amount" :value="null" :label="__('long_term_rentals.car_amount_unit')" 
                        :optionals="['type' => 'number', 'required' => true]" />
                    </div>
                    <div class="col-sm-3">
                        <x-forms.select-option id="temp_lt_month" :value="null" :list="null" :label="__('long_term_rentals.rental_duration')"
                            :optionals="['ajax' => true, 'required' => true]" />
                    </div>
                </div>
                <div class="row push mb-4">
                    <div class="col-sm-6">
                        <x-forms.upload-image :id="'temp_approved_rental_files'" :label="__('long_term_rentals.approved_rental_file')" />
                    </div>
                    <div class="col-sm-6">
                        <x-forms.input-new-line :id="'temp_remark'" :value="null" :label="__('lang.remark')" />
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-clear-search"
                    data-bs-dismiss="modal">{{ __('lang.cancel') }}</button>
                <button type="button" class="btn btn-primary" onclick="saveLongTermRentalPRLine()">{{ __('lang.save') }}</button>
            </div>
        </div>
    </div>
</div>
