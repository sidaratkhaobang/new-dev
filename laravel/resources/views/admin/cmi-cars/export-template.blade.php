<table>
    <thead>
    <tr>
        <th>{{ __('lang.order') }}</th>
        <th>{{ __('cars.chassis_no') }}</th>
        <th>{{ __('cmi_cars.insurance_company') }}</th>
        <th>{{ __('cmi_cars.license_plate') }}</th>
        <th>{{ __('cmi_cars.cmi_no') }}</th>
        <th>{{ __('cmi_cars.policy_start_date') }}</th>
        <th>{{ __('cmi_cars.policy_end_date') }}</th>
        <th>{{ __('cmi_cars.premium_net') . ' (' . __('cmi_cars.cmi') . ')'}}</th>
        <th>{{ __('cmi_cars.discount') . ' (' . __('cmi_cars.cmi') . ')'}}</th>
        <th>{{ __('cmi_cars.stamp_duty') . ' (' . __('cmi_cars.cmi') . ')'}}</th>
        <th>{{ __('cmi_cars.tax') . ' (' . __('cmi_cars.cmi') . ')'}}</th>
        <th>{{ __('cmi_cars.premium_total') . ' (' . __('cmi_cars.cmi') . ')'}}</th>
        <th>{{ __('cmi_cars.withholding_tax_1') }}</th>
        <th>{{ __('cmi_cars.cmi_bar_no') }}</th>
        <th>{{ __('cmi_cars.statement_date') }}</th>
        <th>{{ __('cmi_cars.account_submission_date') }}</th>
        <th>{{ __('cmi_cars.operated_date') }}</th>
    </tr>
    </thead>
    <tbody>
    @foreach($cmi_list as $index => $d)
        <tr>
            <td>{{ $index + 1 }}</td>
            <td>{{ $d->car?->chassis_no }}</td>
            <td>{{ $d->insurer?->insurance_name_th }}</td>
            <td>{{ $d->car?->license_plate }}</td>
            <td>{{ $d->worksheet_no }}</td>
            <td>{{ $d->term_start_date }}</td>
            <td>{{ $d->term_end_date }}</td>
            <td>{{ number_format($d->premium, 2, '.', ',') }}</td>
            <td>{{ number_format($d->discount, 2, '.', ',') }}</td>
            <td>{{ number_format($d->stamp_duty, 2, '.', ',') }}</td>
            <td>{{ number_format($d->tax, 2, '.', ',') }}</td>
            <td>{{ number_format($d->premium_total, 2, '.', ',') }}</td>
            <td>{{ number_format($d->withholding_tax, 2, '.', ',') }}</td>
            <td>{{ $d->number_bar_cmi }}</td>
            <td>{{ $d->statement_date }}</td>
            <td>{{ $d->account_submission_date }}</td>
            <td>{{ $d->operated_date }}</td>
        </tr>
    @endforeach
    </tbody>
</table>