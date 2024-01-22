<div class="block {{ __('block.styles') }}">
    @include('admin.components.block-header', [
        'text' => __('car_auctions.title_after'),
        'block_icon_class' => 'icon-document',
    ])
    <div class="block-content">
        <div class="table-wrap">
            <table class="table table-striped">
                <thead class="bg-body-dark text-center">
                    <th>{{ __('car_auctions.selling_price') }} <span class="text-danger">*</span></th>
                    <th>{{ __('car_auctions.vat_selling_price') }}</th>
                    <th>{{ __('car_auctions.total_selling_price') }}</th>
                    <th>{{ __('car_auctions.profit_loss') }} <span class="text-danger">*</span></th>
                    <th>{{ __('car_auctions.tax_refund') }} <span class="text-danger">*</span></th>
                    <th>{{ __('car_auctions.other_price') }}</th>
                </thead>
                <tbody>
                    <tr>
                        <td>
                            <input type="text" class="form-control col-sm-4 number-format" id="selling_price"
                                name="selling_price" value="{{ $d->selling_price }}">
                        </td>
                        <td>
                            <input type="text" class="form-control col-sm-4" id="vat_selling_price_num"
                                name="vat_selling_price_num" value="{{ number_format($d->vat_selling_price, 2) }}">
                            <x-forms.hidden id="vat_selling_price" :value="$d->vat_selling_price" />
                        </td>
                        <td>
                            <input type="text" class="form-control col-sm-4" id="total_selling_price_num"
                                name="total_selling_price_num" value="{{ number_format($d->total_selling_price, 2) }}">
                            <x-forms.hidden id="total_selling_price" :value="$d->total_selling_price" />
                        </td>
                        <td>
                            <input type="text" class="form-control col-sm-4 number-format" id="profit_loss"
                                name="profit_loss" value="{{ $d->profit_loss }}">
                        </td>
                        <td>
                            <input type="text" class="form-control col-sm-4 number-format" id="tax_refund"
                                name="tax_refund" value="{{ $d->tax_refund }}">
                        </td>
                        <td>
                            <input type="text" class="form-control col-sm-4 number-format" id="other_price"
                                name="other_price" value="{{ $d->other_price }}">
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
@push('scripts')
    <script>
        $("#selling_price").on("input", function() {
            price = $(this).val();
            price = parseFloat(price.replace(/,/g, ''));
            vat = 0;
            total = 0;
            if ((price)) {
                vat = parseFloat(parseFloat(price) * 7 / 107).toFixed(2);
                total = (parseFloat(parseFloat(price) + parseFloat(vat)).toFixed(2));
            }
            $('#vat_selling_price_num').val(numberWithCommas(vat));
            $('#vat_selling_price').val(numberWithCommas(vat));
            $('#total_selling_price_num').val(numberWithCommas(total));
            $('#total_selling_price').val(numberWithCommas(total));
        });

        function numberWithCommas(x) {
            return x.toString().replace(/\B(?<!\.\d*)(?=(\d{3})+(?!\d))/g, ",");
        }
    </script>
@endpush
