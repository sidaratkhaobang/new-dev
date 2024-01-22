<div class="modal fade" id="modal-check-gps" tabindex="-1" aria-labelledby="modal-check-gps" style="display: none;"
    aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5>ดาวน์โหลด Excel</h5>
            </div>
            <div class="modal-body pb-1">
                <div class="row push mb-4">
                    <div class="col-sm-12">
                        <p>รอดำเนินการ</p>
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-sm-12 text-end">
                        <button type="button" class="btn btn-secondary btn-block btn-hide-check-gps"
                            data-dismiss="modal">{{ __('lang.cancel') }}</button>
                        <button type="button" class="btn btn-primary btn-block btn-hide-check-gps"><i
                                class="fa fa-cloud-download-alt"></i> {{ __('gps.download_excel') }}</button>
                    </div>
                </div>
            </div>
            <div class="modal-footer"></div>
        </div>
    </div>
</div>

@push('scripts')
    <script>
        $(".btn-hide-check-gps").on("click", function() {
            $('#modal-check-gps').modal('hide');
        });
    </script>
@endpush
