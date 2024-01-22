<div class="modal fade" id="modal-print" tabindex="-1" aria-labelledby="modal-print" style="display: none;"
    aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header"></div>
            <div class="modal-body pb-1">
                <div class="row push mb-4">
                    <div class="col-sm-12">
                        <h5>ยังไม่สามารถพิมพ์ใบนำรถเข้าออกได้ เพราะต้องรอระบบไม้กั้น</h5>
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-sm-12 text-end">
                        <button type="button" class="btn btn-secondary btn-block btn-hide-print"
                            data-dismiss="modal">{{ __('lang.cancel') }}</button>
                        <button type="button"
                            class="btn btn-primary btn-block btn-hide-print">{{ __('lang.ok') }}</button>
                    </div>
                </div>
            </div>
            <div class="modal-footer"></div>
        </div>
    </div>
</div>

@push('scripts')
    <script>
        $(".btn-hide-print").on("click", function() {
            $('#modal-print').modal('hide');
        });
    </script>
@endpush
