<div class="modal fade" id="modal-share-dealer" tabindex="-1" aria-labelledby="modal-share-dealer" aria-hidden="false">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-popout">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="car-accessory-modal-label">แชร์ให้ Dealer</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row push mb-4">
                    <label for="email" class="text-start col-form-label">{{ __('import_cars.email') }}</label>
                    <div class="col-sm-12">
                        <div class="tag-field js-tags" id="js-tag-car">
                            <input type="text" class="form-control js-tag-input" id="email" name="email"
                                placeholder="ระบุข้อมูล...">
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                @if (isset($dealer_line['dealer_id']))
                    <x-forms.hidden id="dealer_id" :value="$dealer_line['dealer_id']" />
                @endif
                <div class="col-sm-9 text-start"><a href="" class="copy_link">คัดลอกลิงก์</a></div>
                <button type="button" class="btn btn-secondary btn-clear-search"
                    data-bs-dismiss="modal">{{ __('lang.cancel') }}</button>
                <button type="button" class="btn btn-primary" onclick="sendMail()">{{ __('lang.save') }}</button>
            </div>
        </div>
    </div>
</div>
