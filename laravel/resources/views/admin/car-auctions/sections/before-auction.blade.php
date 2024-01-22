<div class="block {{ __('block.styles') }}">
    @include('admin.components.block-header', [
        'text' => __('car_auctions.title_before'),
        'block_icon_class' => 'icon-document',
    ])
    <div class="block-content">
        <div class="row push">
            <div class="col-sm-3">
                <x-forms.input-new-line id="auto_grate" :value="$d->auto_grate" :label="__('car_auctions.auto_grate')" :optionals="['required' => true]" />
            </div>
            <div class="col-sm-3">
                <x-forms.input-new-line id="nature" :value="$d->nature" :label="__('car_auctions.nature')" :optionals="['required' => true]" />
            </div>
            <div class="col-sm-6">
                <x-forms.input-new-line id="remark" :value="$d->remark" :label="__('car_auctions.remark')" />
            </div>
        </div>
        <div class="table-wrap">
            <table class="table table-striped">
                <thead class="bg-body-dark text-center">
                    <th>{{ __('car_auctions.sale_price') }}</th>
                    <th>{{ __('car_auctions.redbook') }} <span class="text-danger">*</span></th>
                    <th>{{ __('car_auctions.auction_price') }} <span class="text-danger">*</span></th>
                    <th>{{ __('car_auctions.tls_price') }} <span class="text-danger">*</span></th>
                </thead>
                <tbody>
                    <tr>
                        <td>
                            <input type="text" class="form-control col-sm-4" id="sale_price" name="sale_price"
                                value="{{ number_format($d->sale_price, 2) }}" disabled>
                        </td>
                        <td>
                            <input type="text" class="form-control col-sm-4 number-format" id="redbook"
                                name="redbook" value="{{ $d->redbook }}">
                        </td>
                        <td>
                            <input type="text" class="form-control col-sm-4 number-format" id="auction_price"
                                name="auction_price" value="{{ $d->auction_price }}">
                        </td>
                        <td>
                            <input type="text" class="form-control col-sm-4 number-format" id="tls_price"
                                name="tls_price" value="{{ $d->tls_price }}">
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="row">
            <div class="col-sm-12">
                <x-forms.input-new-line id="reason" :value="$d->reason" :label="__('car_auctions.reason')" />
            </div>
        </div>
    </div>
</div>

@push('scripts')
    <script>
        // $(document).ready(function() {
        //     $("#auction_price").keyup(function() {
        //         var sale_price = document.getElementById("sale_price").value;
        //         var auction_price = $(this).val();

        //         if (auction_price >= sale_price) {
        //             $(this).css("border-color", "red");
        //             $(this).css('background-color', '#D83232');
        //         } else {
        //             $(this).css("border-color", "");
        //         }
        //     });
        // });
    </script>
@endpush
