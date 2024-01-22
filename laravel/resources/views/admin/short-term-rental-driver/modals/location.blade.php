<x-modal :id="'location'" :title="__('short_term_rentals.location_detail')">
    <div class="row push mb-4">
        <div class="col-sm-12 col-form-label map-section" id="map-origin" style="height: 400px;">
            <div style="padding-left: 14px;">
                <p class="text-danger" id="msg"></p>
            </div>
        </div>
    </div>
    <div class="row push mb-4">
        <div class="col-sm-4">
            <x-forms.input-new-line id="origin_name_temp" :value="null" :label="__('short_term_rentals.address_name')"
                                    :optionals="['required' => true]"/>
        </div>
        <div class="col-sm-4">
            <x-forms.input-new-line id="origin_lat_temp" :value="null" :label="__('short_term_rentals.lat')"
                                    :optionals="['input_class' => 'origin_lat']"/>
        </div>
        <div class="col-sm-4">
            <x-forms.input-new-line id="origin_lng_temp" :value="null" :label="__('short_term_rentals.lng')"
                                    :optionals="['input_class' => 'origin_lng']"/>
        </div>
    </div>
    <div class="row push mb-4">


    </div>
    <x-slot name="footer">
        <button type="button" class="btn btn-outline-secondary btn-custom-size btn-clear-search"
                data-bs-dismiss="modal">{{ __('lang.back') }}</button>
        <button type="button" class="btn btn-primary btn-add-origin"
                onclick="saveModalLocation()">{{ __('lang.save') }}</button>
    </x-slot>
</x-modal>

