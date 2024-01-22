<tr>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td style="border-bottom: 1px double black;">{{number_format($table_summary_car_price_data['po_price']) ?? 0}}</td>
    @if(!empty($header_suplier))
        @foreach($header_suplier as $key_header => $value_header)
            @if(array_key_exists($value_header->supplier_id,$table_summary_data))
                <td></td>
                <td style="border-bottom: 1px double black;">{{number_format($table_summary_data[$value_header->supplier_id] ?? 0)}}</td>
                <td></td>
            @else
                <td></td>
                <td></td>
                <td></td>
            @endif
        @endforeach
    @else
        <td></td>
        <td></td>
        <td></td>
    @endif
    <td style="border-bottom: 1px double black;">{{number_format($table_summary_car_price_data['accessory_total_with_vat'] ?? 0) }}</td>
    <td style="border-bottom: 1px double black;">{{number_format($table_summary_car_price_data['price_car_with_accessory'] ?? 0) }}</td>
    <td></td>
    <td></td>
    <td></td>
    <td style="border-bottom: 1px double black;">{{number_format($table_summary_car_price_data['rental_price'] ?? 0) }}</td>
    <td></td>
    <td style="border-bottom: 1px double black;">{{number_format($table_summary_car_price_data['sum_insured_car'] ?? 0) }}</td>
    <td style="border-bottom: 1px double black;">
        {{number_format($table_summary_car_price_data['sum_insured_accessory'] ?? 0) }}
    </td>
    <td style="border-bottom: 1px double black;">
        {{number_format($table_summary_car_price_data['insurance_total'] ?? 0) }}
    </td>
    <td style="border-bottom: 1px double black;">
        {{number_format($table_summary_car_price_data['premium'] ?? 0) }}
    </td>
    <td style="border-bottom: 1px double black;">
        {{number_format($table_summary_car_price_data['tax'] ?? 0) }}
    </td>
    <td style="border-bottom: 1px double black;">
        {{number_format($table_summary_car_price_data['cmi_total'] ?? 0) }}
    </td>
    <td style="border-bottom: 1px double black;">
        {{number_format($table_summary_car_price_data['premium_total'] ?? 0) }}
    </td>
    <td style="border-bottom: 1px double black;">
        0
    </td>
</tr>
