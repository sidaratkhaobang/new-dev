<table>
    <thead>
        <tr>
            <th>{{ __('lang.order') }}</th>
            <th>{{ __('cars.chassis_no') }}</th>
            <th>{{ __('cmi_cars.insurance_company') }}</th>
            <th>{{ __('cmi_cars.license_plate') }}</th>
            <th>{{ __('cmi_cars.sum_insured_total') }}</th>
            <th>{{ __('vmi_cars.repair_type') }}</th>
            <th>{{ __('lang.remark') }}</th>
            <th>TPBI (per person)</th>
            <th>TPBI (per aggregate)</th>
            <th>TPPD (per aggregate)</th>
            <th>PA (Driver)</th>
            <th>PA (Passenger)</th>
            <th>Medical Exp</th>
            <th>Bailbond</th>
            <th>Deductible</th>
            <th>Policy Reference Child VMI (เลขกรมธรรม์เดี่ยว)</th>
            <th>Term Start Date VMI</th>
            <th>Term End Date VMI</th>
            <th>Gross</th>
            <th>Stamp VMI</th>
            <th>VAT VMI</th>
            <th>Total VMI</th>
            <th>Tax withholding 1% VMI</th>
            <th>{{ __('cmi_cars.statement_no') }}</th>
            <th>{{ __('cmi_cars.tax_invoice_no') }}</th>
            <th>{{ __('cmi_cars.statement_date') }}</th>
            <th>วันที่ส่งบัญชี (ประเภท 1)</th>
            <th>Date Operated VMI (วันที่ดำเนินการจ่าย)</th>
            {{-- <th>Payment Voucher VMI (เลขที่ใบสำคัญจ่าย)</th> --}}
        </tr>
    </thead>
    <tbody>
        @foreach ($vmi_list as $index => $d)
        <tr>
            <td>{{ $index + 1 }}</td>
            <td>{{ $d->car?->chassis_no }}</td>
            <td>{{ $d->insurer?->insurance_name_th }}</td>
            <td>{{ $d->car?->license_plate }}</td>
            <td>{{ number_format(($d->sum_insured_car + $d->sum_insured_accessory), 2, '.', ',') }}</td>
            <td></td>
            <td>{{ $d->remark }}</td>
            <td>{{ number_format($d->tpbi_person, 2, '.', ',') }}</td>
            <td>{{ number_format($d->tpbi_aggregate, 2, '.', ',') }}</td>
            <td>{{ number_format($d->tppd_aggregate, 2, '.', ',') }}</td>
            <td>{{ number_format($d->pa_driver, 2, '.', ',') }}</td>
            <td>{{ number_format($d->pa_passenger, 2, '.', ',') }}</td>
            <td>{{ number_format($d->medical_exp, 2, '.', ',') }}</td>
            <td>{{ number_format($d->bail_bond, 2, '.', ',') }}</td>
            <td>{{ number_format($d->deductible, 2, '.', ',') }}</td>
            <td>{{ $d->policy_reference_child_vmi }}</td>
            <td>{{ $d->term_start_date }}</td>
            <td>{{ $d->term_end_date }}</td>
            <td>{{ number_format($d->gross, 2, '.', ',') }}</td>
            <td>{{ number_format($d->stamp_duty, 2, '.', ',') }}</td>
            <td>{{ number_format($d->tax, 2, '.', ',') }}</td>
            {{-- TODO --}}
            <td>{{ number_format($d->premium_total, 2, '.', ',') }}</td>
            <td>{{ number_format($d->withholding_tax, 2, '.', ',') }}</td>
            <td>{{ $d->statement_no }}</td>
            <td>{{ $d->statement_date }}</td>
            <td>{{ $d->account_submission_date }}</td>
            <td>{{ $d->operated_date }}</td>
        </tr>
        @endforeach
    </tbody>
</table>