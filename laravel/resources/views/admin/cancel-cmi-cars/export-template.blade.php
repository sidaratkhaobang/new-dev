<table>
    <thead>
        <tr>
            <th>{{ __('lang.order') }}</th>
            <th>{{ __('cmi_cars.remission_type') }}</th>
            <th>{{ __('cmi_cars.insurance_company') }}</th>
            <th>{{ __('cars.chassis_no') }}</th>
            <th>{{ __('cmi_cars.license_plate') }}</th>
            <th>{{ __('lang.remark') }}</th>
            <th>หมายเลขกรมธรรม์เดี่ยว</th>
            <th>{{ __('cmi_cars.policy_start_date') . $type }}</th>
            <th>{{ __('cmi_cars.policy_end_date') . $type }}</th>
            <th>วันที่ให้มีผล {{ $type }}</th>
            <th>คืนสุทธิ {{ $type }}</th>
            <th>คืน (อากร) {{ $type }}</th>
            <th>คืน (ภาษีมูลค่าเพิ่ม) {{ $type }}</th>
            <th>คืนทั้งสิ้น {{ $type }}</th>
            <th>ภาษีหัก ณ ที่จ่าย 1 % ({{ $type }})</th>
            <th>เลขที่ใบลดหนี้ ({{ $type }})</th>
            <th>วันที่ใบลดหนี้ ({{ $type }})</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($list as $index => $d)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $d->reason }}</td>
                <td>{{ $d->insurer?->insurance_name_th }}</td>
                <td>{{ $d->ref?->car?->chassis_no }}</td>
                <td>{{ $d->ref?->car?->license_plate }}</td>
                <td>{{ $d->remark }}</td>
                <td>
                    @if ($type == 'CMI')
                        {{ $d->ref?->policy_reference_cmi }}
                    @endif
                    @if ($type == 'VMI')
                        {{ $d->ref?->policy_reference_child_vmi }}
                    @endif
                </td>
                <td>{{ $d->ref?->term_start_date ? get_date_time_by_format($d->ref?->term_start_date, 'd/m/Y H:i:s') : null  }}</td>
                <td>{{ $d->ref?->term_end_date ? get_date_time_by_format($d->ref?->term_end_date, 'd/m/Y H:i:s') : null }}</td>
                <td>{{ $d->actual_cancel_date ? get_date_time_by_format($d->actual_cancel_date, 'd/m/Y H:i:s') : null  }}</td>
                <td>{{ number_format($d->refund, 2, '.', ',') }}</td>
                <td>{{ number_format($d->refund_stamp, 2, '.', ',') }}</td>
                <td>{{ number_format($d->refund_vat, 2, '.', ',') }}</td>
                <td>{{ number_format($d->refund_total, 2, '.', ',') }}</td>
                <td>{{ number_format($d->withholding_tax, 2, '.', ',') }}</td>
                <td>{{ $d->credit_note }}</td>
                <td>{{ $d->credit_note_date ? get_date_time_by_format($d->credit_note_date, 'd/m/Y H:i:s') : null  }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
