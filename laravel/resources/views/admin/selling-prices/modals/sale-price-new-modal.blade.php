<div class="modal fade" id="modal-sale-price-new" tabindex="-1" aria-labelledby="modal-sale-price-new"
    style="display: none;" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="sale-price-new-modal-label"><i
                        class="fa fa-comment-dollar"></i>ทำราคาขายรถล่วงหน้าใหม่</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body pb-1">
                <form id="save-form-sale-price-new">
                    <div class="row push mb-4">
                        <div class="col-sm-4">
                            <x-forms.input-new-line id="price_new" :value="null" :label="__('selling_prices.price')"
                                :optionals="[
                                    'input_class' => 'number-format col-sm-4',
                                ]" />
                        </div>
                        <div class="col-sm-4">
                            <x-forms.input-new-line id="vat_value_new" :value="null" :label="__('selling_prices.vat')" />
                            <x-forms.hidden id="vat_new" :value="null" />
                        </div>
                        <div class="col-sm-4">
                            <x-forms.input-new-line id="total_value_new" :value="null" :label="__('selling_prices.total')" />
                            <x-forms.hidden id="total_new" :value="null" />
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-sm-12 text-end">
                            <button type="button" class="btn btn-secondary btn-hide-sale-price-new"
                                data-dismiss="modal">{{ __('lang.cancel') }}</button>
                            <button type="button" class="btn btn-primary btn-save-sale-price-new"><i
                                    class="icon-save me-1"></i> {{ __('lang.save') }}</button>
                            <input type="hidden" name="sale_car_id" id="sale_car_id">
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer"></div>
        </div>
    </div>
</div>

@push('scripts')
    <script>
        $('#vat_value_new').prop('disabled', true);
        $('#total_value_new').prop('disabled', true);

        $("#price_new").on("input", function() {
            price = $(this).val();
            price = parseFloat(price.replace(/,/g, ''));
            vat = 0;
            total = 0;
            if ((price)) {
                vat = parseFloat(parseFloat(price) * 7 / 107).toFixed(2);
                total = (parseFloat(parseFloat(price) + parseFloat(vat)).toFixed(2));
            }
            $('#vat_new').val(numberWithCommas(vat));
            $('#vat_value_new').val(numberWithCommas(vat));
            $('#total_new').val(numberWithCommas(total));
            $('#total_value_new').val(numberWithCommas(total));
        });

        function numberWithCommas(x) {
            return x.toString().replace(/\B(?<!\.\d*)(?=(\d{3})+(?!\d))/g, ",");
        }

        $(".btn-hide-sale-price-new").on("click", function() {
            $('#modal-sale-price-new').modal('hide');
        });

        $(".btn-save-sale-price-new").on("click", function() {
            var id = document.getElementById("sale_car_id").value;
            var vat = document.getElementById("vat_new").value;
            var total = document.getElementById("total_new").value;
            var price = document.getElementById("price_new").value;
            var data = {
                id: id,
                vat: vat,
                total: total,
                price: price,
            };
            let storeUri = "{{ route('admin.selling-prices.sale-price-new') }}";
            showLoading();
            axios.post(storeUri, data).then(response => {
                if (response.data.success) {
                    hideLoading();
                    $('#modal-send-sale-car').modal('hide');
                    mySwal.fire({
                        title: "{{ __('lang.store_success_title') }}",
                        text: 'ทำราคาขายรถล่วงหน้าใหม่เรียบร้อย',
                        icon: 'success',
                        confirmButtonText: "{{ __('lang.ok') }}"
                    }).then(value => {
                        if (response.data.redirect) {
                            window.location.href = response.data.redirect;
                        } else {
                            window.location.reload();
                        }
                    });
                }
            }).catch(error => {
                //
            });
        });
    </script>
@endpush
