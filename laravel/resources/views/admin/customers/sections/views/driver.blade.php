<div class="table-wrap">
    <table class="table table-striped">
        <thead class="bg-body-dark">
            <th style="width: 2px;">#</th>
            <th>{{ __('customers.full_name') }}</th>
            <th>{{ __('customers.tel_driver') }}</th>
            <th>{{ __('customers.citizen') }}</th>
            <th>{{ __('customers.email') }}</th>
            <th>{{ __('customers.driving_license_file') }}</th>
            <th>{{ __('customers.citizen_file') }}</th>
        </thead>
        <tbody>
            @if (sizeof($customer_driver_list) > 0)
                @foreach ($customer_driver_list as $index => $customer_driver)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $customer_driver['name'] }}</td>
                        <td>{{ $customer_driver['tel'] }}</td>
                        <td>{{ $customer_driver['citizen_id'] }}</td>
                        <td>{{ $customer_driver['email'] }}</td>
                        <td>
                            @if (!empty($customer_driver['license_files']))
                                @foreach ($customer_driver['license_files'] as $item)
                                    @if (!empty($item['url']))
                                        <a href="{{ $item['url'] }}" target="_blank"><i
                                                class="fa fa-download text-primary"></i>
                                            {{ $item['name'] }}
                                        </a>
                                    @endif
                                @endforeach
                            @endif
                        </td>
                        <td>
                            @if (!empty($customer_driver['citizen_files']))
                                @foreach ($customer_driver['citizen_files'] as $item)
                                    @if (!empty($item['url']))
                                        <a href="{{ $item['url'] }}" target="_blank"><i
                                                class="fa fa-download text-primary"></i>
                                            {{ $item['name'] }}
                                        </a>
                                    @endif
                                @endforeach
                            @endif
                        </td>
                    </tr>
                @endforeach
            @else
                <tr class="table-empty">
                    <td class="text-center" colspan="8">“
                        {{ __('lang.no_list') . __('customers.driver_table') }} “</td>
                </tr>
            @endif
        </tbody>
    </table>
</div>