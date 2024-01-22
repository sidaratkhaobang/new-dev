<div class="modal fade" id="modal-dealer" tabindex="-1" aria-labelledby="modal-bom-car" aria-hidden="false">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-popout" style="width:1250px;">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="car-accessory-modal-label">เพิ่ม Dealer</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>หากไม่มีชื่อ Dealer ในระบบ  ให้ทำการเพิ่มรายชื่อ dealer ใหม่ในระบบก่อน</p>
                <hr>
                <div class="row push mb-4">
                    <div class="col-sm-6">
                        <x-forms.select-option id="creditor_id_field" :value="null" :list="null" :label="'รายชื่อ Dealer'"
                            :optionals="['ajax' => true]" />
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-clear-search"
                    data-bs-dismiss="modal">{{ __('lang.cancel') }}</button>
                <button type="button" class="btn btn-primary" onclick="saveDealer()">{{ __('lang.save') }}</button>
            </div>
        </div>
    </div>
</div>
