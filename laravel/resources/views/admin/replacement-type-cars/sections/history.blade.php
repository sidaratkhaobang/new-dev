<h4>{{ __('replacement_cars.replacement_history') }}</h4>
<div class="table-wrap db-scroll">
    <table class="table table-striped table-vcenter">
        <thead class="bg-body-dark">
            <tr>
                <th>{{ __('replacement_cars.worksheet_no') }}</th>
                <th>{{ __('replacement_cars.replacement_type') }}</th>
                <th>{{ __('replacement_cars.job_date_time') }}</th>
                <th>{{ __('replacement_cars.place') }}</th>
                <th>{{ __('replacement_cars.customer_name') }}</th>
                <th>{{ __('replacement_cars.main_license_plate') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($list as $index => $d)
                <tr>
                    <td>{{ $d->worksheet_no }}</td>
                    <td>{{ __('replacement_cars.type_' . $d->replacement_type) }}</td>
                    <td>{{ $d->replacement_date ? get_thai_date_format($d->replacement_date, 'd/m/Y H:i') : '-' }}</td>
                    <td>{{ $d->replacement_place }}</td>
                    <td>{{ $d->customer_name }}</td>
                    <td>{{ $d->mainCar ? $d->mainCar->license_plate : '' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>