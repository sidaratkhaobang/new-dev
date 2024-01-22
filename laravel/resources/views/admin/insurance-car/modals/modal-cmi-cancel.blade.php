
<div class="modal fade" id="modal-cmi-cancel" tabindex="-1" aria-labelledby="modal-cost" aria-hidden="false">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-popout" style="max-width: 50%;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            @include('admin.components.block-header', [
                           'text' => 'ยกเลิกประกัน / พรบ.',
                       ])
            <div class="modal-body">
                <div class="row push mb-4">
                    <div class="col-sm-3">
                        <x-forms.date-input id="insurance_cancel_date" name="insurance_cancel_date"
                                            :value="null" :label="'วันที่ขอยกเลิกประกัน / พรบ.'"
                                            :optionals="['required' => true]"/>
                    </div>
                    <div class="col-sm-3">
                        <x-forms.input-new-line id="insurance_cancel_reason" :value="null" :label="__('insurance_car.cancel_reason')" :optionals="['required' => false]"/>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                                                        <button type="button" class="btn btn-outline-secondary btn-custom-size"
                                                                data-bs-dismiss="modal" style="min-width: 150px;cursor: pointer">{{ __('lang.back') }}</button>
                                                        <button type="button"
                                                                class="btn btn-primary btn-custom-size btn-save-cancel" style="min-width: 150px;cursor: pointer">
                                                           <i class="icon-save">

                                                           </i> {{ __('lang.save') }}
                                                        </button>
            </div>
        </div>
    </div>
</div>

