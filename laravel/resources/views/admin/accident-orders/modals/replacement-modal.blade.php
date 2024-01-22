<div class="modal fade" id="modal-replacement" tabindex="-1" aria-labelledby="modal-replacement" aria-hidden="false">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-popout" style="max-width: 50%;">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="replacement-modal-label">เปิดงานรถหลัก/รถทดแทน</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">

                <div class="row push mb-4">
                    <div class="col-sm-3">
                        <x-forms.select-option id="replacement_type" :value="$d->replacement_type" :list="$replace_list"
                            :label="__('accident_informs.replacement_type')" :optionals="['required' => true]" />
                    </div>
                    <div class="col-sm-3">
                        <x-forms.date-input id="replacement_pickup_date" name="replacement_pickup_date"
                            :value="$d->replacement_pickup_date" :label="__('accident_informs.replacement_pickup_date')" :optionals="['required' => true]" />
                    </div>
                    <div class="col-sm-3">
                        <x-forms.radio-inline id="customer_receive" :value="$d->customer_receive" :list="$receive_status_list"
                            :label="__('accident_informs.customer_receive')" :optionals="['required' => true]" />
                    </div>
                </div>

                <div class="row push mb-4">
                    {{-- @include('admin.components.block-header', [
                        'text' => __('accident_informs.origin_place_detail'),
                    ]) --}}
                    <div class="col-sm-3">
                        <x-forms.select-option id="slide_worksheet" :value="$d->slide_worksheet" :list="$slide_worksheet_list"
                            :label="__('accident_informs.slide_worksheet')" :optionals="['required' => true]" />
                    </div>


                    <div class="col-sm-3">
                        <x-forms.input-new-line id="place" :value="$d->place" :label="__('accident_informs.place')"
                            :optionals="['required' => true]" />
                    </div>

                    {{-- <div class="col-sm-3">
                        <x-forms.input-new-line id="origin_tel" :value="$d->origin_tel" :label="__('accident_informs.origin_tel')"
                            :optionals="['required' => true]" />
                    </div> --}}
                    <div class="col-sm-3">
                        <x-forms.upload-image :id="'replacment_file'" :label="__('accident_informs.optional_file')" />
                    </div>
                    <x-forms.hidden id="accident_id" :value="$d->id" />
                    <x-forms.hidden id="car_id" :value="$d->car_id" />
                    <x-forms.hidden id="id" :value="null" />
                    {{-- <x-forms.hidden id="accident_order_id" :value="null" /> --}}
                </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-clear-search"
                    data-bs-dismiss="modal">{{ __('lang.cancel') }}</button>
                <button type="button" class="btn btn-primary"
                    onclick="saveReplacement()">{{ __('lang.save') }}</button>
                {{-- <button type="button" class="btn btn-primary btn-save-replacement"
                        >{{ __('lang.save') }}</button> --}}
            </div>
        </div>
    </div>
</div>

{{-- @include('admin.components.form-save', [
        'store_uri' => route('admin.accident-informs.store-edit-accident'),
    ]) --}}
