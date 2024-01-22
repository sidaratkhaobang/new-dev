<x-modal :id="'modal-destination'" :title="__('short_term_rentals.destination_detail')">
    <div class="row push mb-4">
        <div class="col-sm-12 col-form-label map-section" id="map-destination" style="height: 400px;">
            <div style="padding-left: 14px;">
                <p class="text-danger" id="msg"></p>
            </div>
        </div>
    </div>
    <div class="row push mb-4">
        <div class="col-sm-4">
            <x-forms.input-new-line id="destination_name_temp" :value="null" :label="__('short_term_rentals.address_name')" />
        </div>
        <div class="col-sm-8">
            <x-forms.input-new-line id="destination_address_temp" :value="null" :label="__('short_term_rentals.address')" />
        </div>
    </div>
    <div class="row push mb-4">
        <div class="col-sm-4">
            <x-forms.input-new-line id="destination_lat_temp" :value="null" :label="__('short_term_rentals.lat')" :optionals="['input_class' => 'destination_lat']" />
        </div>
        <div class="col-sm-4">
            <x-forms.input-new-line id="destination_lng_temp" :value="null" :label="__('short_term_rentals.lng')" :optionals="['input_class' => 'destination_lng']" />
        </div>
    </div>
    <x-slot name="footer">
        <button type="button" class="btn btn-outline-secondary btn-custom-size btn-clear-search" data-bs-dismiss="modal">{{ __('lang.cancel') }}</button>
        <button type="button" class="btn btn-primary btn-add-destination" onclick="addNewDestination()">{{ __('lang.save') }}</button>
    </x-slot>
</x-modal>