<div class="modal fade" id="modal-folklift" tabindex="-1" aria-labelledby="modal-folklift" aria-hidden="false">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-popout" style="max-width: 50%;">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="folklift-modal-label">เพิ่มข้อมูลรถสไลด์</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" >
                <div class="row push mb-4">
                    <div class="col-sm-3">
                        <x-forms.select-option id="slide_type" :value="$d->slide_type" :list="$accident_slide_list"
                            :label="__('accident_informs.slide_type')" :optionals="['required' => true]" />
                    </div>
                    <div class="col-sm-3">
                        <x-forms.input-new-line id="lift_price" :value="$d->lift_price" :label="__('accident_informs.lift_price')" :optionals="[
                            'required' => true,
                            'input_class' => 'number-format col-sm-4',
                            'oninput' => 'true',
                        ]" />
                    </div>
                </div>
              
                <div class="row push mb-4">
                    @include('admin.components.block-header', [
                        'text' => __('accident_informs.origin_place_detail'),
                    ])
                       <div class="col-sm-3">
                        <x-forms.input-new-line id="lift_from" :value="$d->lift_from" :label="__('accident_informs.origin_place')"
                            :optionals="['required' => true]" />
                    </div>
                    
                    <div class="col-sm-3">
                        <x-forms.date-input id="lift_date" name="lift_date" :value="$d->lift_date" :label="__('accident_informs.lift_date_from')"
                            :optionals="['required' => true, 'date_enable_time' => true]" />
                    </div>
                    <div class="col-sm-3">
                        <x-forms.input-new-line id="origin_contact" :value="$d->origin_contact" :label="__('accident_informs.origin_contact')"
                            :optionals="['required' => true]" />
                    </div>
                 
                    <div class="col-sm-3">
                        <x-forms.input-new-line id="origin_tel" :value="$d->origin_tel" :label="__('accident_informs.origin_tel')"
                            :optionals="['required' => true]" />
                    </div>
                </div>
                <div class="row push mb-4">
                    @include('admin.components.block-header', [
                        'text' => __('accident_informs.destination_place_detail'),
                    ])
                      <div class="col-sm-3">
                        <x-forms.input-new-line id="lift_to" :value="$d->lift_to" :label="__('accident_informs.destination_place')"
                            :optionals="['required' => true]" />
                    </div>
                    <div class="col-sm-3">
                        <x-forms.date-input id="lift_date_to" name="lift_date_to" :value="$d->lift_date_to" :label="__('accident_informs.lift_date_to')"
                            :optionals="['required' => true, 'date_enable_time' => true]" />
                    </div>
                    <div class="col-sm-3">
                        <x-forms.input-new-line id="destination_contact" :value="$d->destination_contact" :label="__('accident_informs.destination_contact')"
                            :optionals="['required' => true]" />
                    </div>
                 
                    <div class="col-sm-3">
                        <x-forms.input-new-line id="destination_tel" :value="$d->destination_tel" :label="__('accident_informs.destination_tel')"
                            :optionals="['required' => true]" />
                    </div>
                    <x-forms.hidden id="accident_id" :value="$d->id" />
                    {{-- <div class="col-sm-3">
                        <x-forms.upload-image :id="'slide_file'" :label="__('accident_informs.optional_file')" />
                    </div> --}}
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-clear-search"
                    data-bs-dismiss="modal">{{ __('lang.cancel') }}</button>
                <button type="button" class="btn btn-primary" onclick="saveAccident()">{{ __('lang.save') }}</button>
            </div>
        </div>
    </div>
</div>
