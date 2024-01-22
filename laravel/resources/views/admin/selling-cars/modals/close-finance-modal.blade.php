<div class="modal fade" id="modal-close-finance" tabindex="-1" aria-labelledby="modal-close-finance" style="display: none;"
    aria-hidden="true">
    <div class="modal-dialog modal-xs modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-body pb-1 mt-4">
                <h5 class="text-center" style="font-size: 18px;"> คุณต้องการปิดไฟแนนซ์รถคันนี้ใช่หรือไม่?</h5>
                <div class="row mb-4 mt-4">
                    <div class="col-6 text-end">
                        <button type="button" class="btn btn-outline-secondary btn-hide-close-finance"
                            style="width:65%;">{{ __('lang.cancel') }}</button>
                    </div>
                    <div class="col-6 text-start">
                        <button type="button" class="btn btn-primary btn-save-close-finance" style="width:65%;"><i
                                class="si si-close"></i> ปิดไฟแนนซ์</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@push('scripts')
    <script>
        $(".btn-hide-close-finances").on("click", function() {
            $('#modal-close-finances').modal('hide');
        });

        $(".btn-save-close-finances").on("click", function() {
            var status = '{{ \App\Enums\SellingPriceStatusEnum::PENDING_SALE }}';
            let storeUri = "{{ route('admin.selling-cars.store') }}";
            var formData = new FormData(document.querySelector('#save-form'));
            formData.append('status_sale', status);
            saveForm(storeUri, formData);
            $('#modal-close-finances').modal('hide');
        });
    </script>
@endpush
