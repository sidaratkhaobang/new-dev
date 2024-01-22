<div class="modal fade" id="modal-location" tabindex="-1" aria-labelledby="modal-location" aria-hidden="false">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-popout" style="width:1250px;">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="location-modal-label">เพิ่มข้อมูล</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <h4 class="fw-light text-gray-darker">{{ __('short_term_rentals.location_detail') }}</h4>
                <hr>
                <div class="row push mb-4">
                    <div class="col-sm-6">
                        <x-forms.select-option id="location_field" :value="null" :list="null"
                            :label="__('short_term_rentals.location')" :optionals="['select_class' => 'js-select2-custom', 'ajax' => true]"/>
                    </div>
                    <div class="col-sm-6">
                        <x-forms.input-new-line id="description_field" :value="null" :label="__('short_term_rentals.location_description')" />
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-clear-search"
                    data-bs-dismiss="modal">{{ __('lang.cancel') }}</button>
                <button type="button" class="btn btn-primary"
                    onclick="saveLocation()">{{ __('lang.save') }}</button>
            </div>
        </div>
    </div>
</div>
