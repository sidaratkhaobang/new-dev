<div class="block {{ __('block.styles') }}">
    <div class="block-content">
        <div class="table-wrap db-scroll">
            <table class="table table-striped table-vcenter">
                <thead class="bg-body-dark">
                    <tr>
                        {{-- <th style="width: 1px;">#</th> --}}
                        <th>@sortablelink('worksheet_no', __('borrow_cars.worksheet'))</th>
                        <th>@sortablelink('borrow_type', __('borrow_cars.borrow_type'))</th>
                        <th>@sortablelink('start_date', __('borrow_cars.start_date'))</th>
                        <th>@sortablelink('end_date', __('borrow_cars.end_date'))</th>
                        {{-- <th>@sortablelink('car.license_plate', __('borrow_cars.license_plate'))</th> --}}
                        <th>@sortablelink('borrower', __('borrow_cars.borrower'))</th>
                        {{-- <th>@sortablelink('status', __('borrow_cars.status'))</th> --}}
                        {{-- <th style="width: 100px;" class="sticky-col"></th> --}}
                    </tr>
                </thead>
                <tbody>
                    @foreach ($list as $index => $d)
                        <tr>
                            {{-- <td>{{ $list->firstItem() + $index }}</td> --}}
                            <td>{{ $d->worksheet_no }}</td>
                            <td>{{ __('borrow_cars.type_' . $d->borrow_type ) }}</td>
                            <td>{{ get_thai_date_format($d->start_date, 'd/m/Y H:i') }}</td>
                            <td>{{ get_thai_date_format($d->end_date, 'd/m/Y H:i') }}</td>
                            {{-- <td>{{ $d->car ? $d->car->license_plate : null }}</td> --}}
                            <td>{{ $d->contact }}</td>
                            
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        </div>

</div>
