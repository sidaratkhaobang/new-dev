<table class="table-border">
    <tbody>
        <tr>
            <td style="font-size:18px;font-weight:bold;">Type of Customer / ประเภทของลูกค้า</td>
        </tr>
        {{-- <tr class="border-tr-bottom">
            <td></td>
         </tr> --}}
    </tbody>
</table>

<table class="table-border mb-1">
    <tbody>
        @foreach ($customer_type_list as $customer_type)
            @if ($loop->iteration % 4 == 1)
                <tr>
            @endif
            <td style="width:10%;" class="text-left">
                <img src="{{ (isset($customer) && ($customer->customer_type == $customer_type->id)) ? base_path('storage/logo-pdf/checkbox.png') : base_path('storage/logo-pdf/no-check.png') }}" 
                    style="width:10px; height:10px;"
                    alt=""> {{ $customer_type->name }}</td>
            @if ($loop->iteration % 4 == 0 || $loop->last)
                </tr>
            @endif
        @endforeach
    </tbody>
</table>