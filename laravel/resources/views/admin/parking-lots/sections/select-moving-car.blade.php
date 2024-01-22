<h4>{{ __('parking_lots.car_list') }}</h4>
<hr>
<p class="mt-3 mb-3">จำนวนช่องจอดที่สามารถย้ายไปโซนจอดรถที่เลือกได้คือ <span id="free_slot">0</span> ช่องจอด</p>
<div class="mb-5">
    <div class="table-wrap">
        <table class="js-table-checkable table table-hover table-vcenter">
            <thead class="bg-body-dark">
                <tr>
                    <th class="text-center" style="width: 70px">
                        <div class="custom-control custom-checkbox d-inline-block">
                            <input type="checkbox" class="custom-control-input" id="check-all" name="check-all" />
                            <label class="custom-control-label" for="check-all"></label>
                        </div>
                    </th>
                    <th>#</th>
                    <th>{{ __('parking_lots.slot_number') }}</th>
                    <th>{{ __('parking_lots.car_type') }}</th>
                    <th>{{ __('parking_lots.car_category') }}</th>
                    <th>{{ __('parking_lots.slot_per_one') }}</th>
                    <th>{{ __('parking_lots.license_plate') }}</th>
                    <th>{{ __('parking_lots.engine_no') }}</th>
                    <th>{{ __('parking_lots.chassis_no') }}</th>
                    {{-- <th>{{ __('parking_lots.booking_period') }}</th> --}}
                </tr>
            </thead>
            <tbody>
                @foreach ($car_list as $index => $car)
                    <tr>
                        <td class="text-center">
                            <div class="custom-control custom-checkbox d-inline-block">
                                <input type="checkbox" class="custom-control-input" id="{{ $car->car_id }}"
                                    name="selected_cars[]" value="{{ $car->car_park_id }}" />
                                <label class="custom-control-label" for="{{ $car->car_id }}"></label>
                            </div>
                        </td>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $car->zone_code }}{{ $car->car_park_number }}
                        </td>
                        <td>{{ $car->car_status }}</td>
                        <td>{{ $car->group_name }}</td>
                        <td class="reserve-size" id="sizeof-{{ $car->id }}">
                            @if ($car->area_size === \App\Enums\CarParkSlotSizeEnum::SMALL)
                                {{ $car->reserve_small_size }}
                            @elseif ($car->area_size === \App\Enums\CarParkSlotSizeEnum::BIG)
                                {{ $car->reserve_big_size }}
                            @else               
                            @endif
                        </td>
                        <td>{{ $car->license_plate }}</td>
                        <td>{{ $car->engine_no }}</td>
                        <td>{{ $car->chassis_no }}</td>
                        {{-- <td>
                            @if (!empty($d->start_date) && !empty($d->end_date))
                                {{ get_thai_date_format($d->start_date, 'd/m/Y') }} -
                                {{ get_thai_date_format($d->end_date, 'd/m/Y') }}
                            @elseif (!empty($d->start_date))
                                {{ get_thai_date_format($d->start_date, 'd/m/Y') }}
                            @elseif (!empty($d->end_date))
                                {{ get_thai_date_format($d->staend_datert_date, 'd/m/Y') }}
                            @else
                            @endif
                        </td> --}}
                    </tr>       
                @endforeach
                <tr>
                    <td colspan="5" class="text-end">รวมจำนวนช่องจอดที่เลือก</td>
                    <td colspan="5" class="text-start total-reserve-size">0</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
