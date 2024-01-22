<div class="table-wrap">
    <table class="table table-striped">
        <thead class="bg-body-dark">
            <th style="width: 2px;">#</th>
            <th style="width: 25%;">{{ __('customers.name_all') }}</th>
            <th style="width: 20%;">{{ __('customers.tax_no') }}</th>
            <th style="width: 30%;">{{ __('customers.address') }}</th>
            <th style="width: 10%;">{{ __('customers.province') }}</th>
            <th style="width: 10%;">{{ __('customers.email') }}</th>
            <th style="width: 10%;">{{ __('customers.tel_driver') }}</th>
        </thead>
        <tbody>
            @if (sizeof($customer_billing_address_list) > 0)
                @foreach ($customer_billing_address_list as $index => $customer_billing_address)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $customer_billing_address['name'] }}</td>
                        <td>{{ $customer_billing_address['tax_no'] }}</td>
                        <td class="td-break" style="white-space: pre-wrap;">
                            {{ $customer_billing_address['address'] }}
                        </td>
                        <td>{{ $customer_billing_address['province_text'] }}</td>
                        <td>{{ $customer_billing_address['email'] }}</td>
                        <td>{{ $customer_billing_address['tel'] }}</td>
                    </tr>
                @endforeach
            @else
                <tr class="table-empty">
                    <td class="text-center" colspan="8">“
                        {{ __('lang.no_list') . __('customers.billing_address_table') }} “</td>
                </tr>
            @endif
        </tbody>
    </table>
</div>