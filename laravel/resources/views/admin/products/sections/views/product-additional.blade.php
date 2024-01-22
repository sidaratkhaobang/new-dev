<div class="mb-5" id="product-additionals">
    <div class="table-wrap">
        <table class="table table-striped">
            <thead class="bg-body-dark">
            <th>#</th>
            <th>{{ __('product_additionals.name') }}</th>
            <th>{{ __('products.price') }}</th>
            <th>{{ __('products.amount') }}</th>
            <th class="text-center">{{ __('products.free') }}</th>
            </thead>
            @if (sizeof($product_additional_list) > 0)
                <tbody>
                @foreach ($product_additional_list as $index => $item)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $item->product_additional_text }}</td>
                        <td>{{ number_format($item->price,2) }}</td>
                        <td>{{ $item->amount }}</td>
                        <td class="text-center">
                            {{ $errors->has('email') ? 'has-error' : '' }}
                            <i
                                class="{{ $item->is_free == 1 ? 'fa fa-circle-check text-primary' : 'fa fa-circle-xmark text-secondary' }}"></i>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            @else
                <tbody>
                <tr class="table-empty">
                    <td class="text-center" colspan="6">" {{ __('lang.no_list') }} "</td>
                </tr>
                </tbody>
            @endif
        </table>
    </div>
</div>
