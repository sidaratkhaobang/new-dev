<div class="row push mb-3 mt-5">
    <div class="col-4 mt-2">
        <h4>ข้อมูลการเช็กกำหนดส่งมอบรถยนต์ก่อนเสนอราคา</h4>
    </div>
    @if (!isset($view_only))
        @if (!isset($accessory_controller))
            <div class="col-8 text-end">
                <button type="button" class="btn btn-primary" onclick="addDealer()">เพิ่ม Dealer</button>
            </div>
        @endif
    @endif


</div>
<hr>

<div>
    <div class="table-wrap">
        <table class="table table-striped">
            <thead class="bg-body-dark">

                <th class="text-center">#</th>
                <th>{{ __('long_term_rentals.car_class') }}</th>
                <th>{{ __('long_term_rentals.car_color') }}</th>
                <th>{{ __('long_term_rentals.car_amount') }}</th>
                <th>{{ __('long_term_rentals.customer_need') }}</th>
                <th>{{ __('long_term_rentals.ready_to_delivery') }}</th>
                <th></th>
            </thead>
            @if (count($tor_line_list) > 0)
                <tbody>
                    @foreach ($tor_line_list as $index => $tor_line)
                        <tr>
                            <td class="text-center toggle-table" style="width: 30px">
                                <i class="fa fa-angle-right text-muted"></i>
                            </td>
                            <td>{{ $tor_line->car_class_text }} <br><span>ส่งมอบภายในวันที่
                                    {{ $tor_line->require_date_text }} / 60 วันนับจากลงนามในสัญญา</span></td>
                            <td>{{ $tor_line->car_color_text }}</td>
                            <td>{{ $tor_line->amount_car }}</td>
                            <td>{{ $tor_line->customer_require }}</td>
                            {{-- <td></td> --}}
                            <td>
                                <div class="custom-control custom-checkbox d-inline-block form-check">
                                    <input type="checkbox" class="form-check-input custom-control-input"
                                        name="ready_to_delivery[{{ $tor_line->id }}]" value="1"
                                        @if ($tor_line->check_delivery == STATUS_ACTIVE) checked @endif>

                                </div>
                            </td>
                            <td></td>


                        </tr>
                        <tr>
                            <td></td>
                            <td colspan="6">
                                @if (count($tor_line->dealer) > 0)
                                    <table class="table table-bordered" style="width: 100%; white-space: normal;">
                                        <thead class="bg-body-dark" style="border-color:white; ">
                                            <tr>
                                                <th rowspan="2" class="text-center" style="width: 100px">
                                                    {{ __('long_term_rentals.dealers') }}</th>
                                                <th colspan="4" class="text-center" style="width: 200px">
                                                    {{ __('long_term_rentals.stock_order') }}
                                                </th>
                                                <th rowspan="2" class="text-center" style="width: 100px">
                                                    {{ __('long_term_rentals.response_date') }}</th>
                                                @if (empty($view_only))
                                                    <th rowspan="2" class="sticky-col text-center"
                                                        style="width: 200px">
                                                        {{ __('lang.tools') }}
                                                    </th>
                                                @endif
                                            </tr>
                                            <tr style="border-bottom-width:2px;">
                                                <th class="text-center" style="width: 100px">
                                                    {{ __('long_term_rentals.is_ready_to_deliver') }}</th>
                                                <th class="text-center" style="width: 100px">
                                                    {{ __('long_term_rentals.car_amount') }}</th>
                                                <th class="text-center" style="width: 100px">
                                                    {{ __('long_term_rentals.delivery_month') }}</th>
                                                <th class="text-center"
                                                    style="width:400px; word-break: break-all; white-space: normal;">
                                                    {{ __('long_term_rentals.remark') }}</th>
                                            </tr>
                                        </thead>

                                        <tbody>
                                            @foreach ($tor_line->dealer as $index => $dealer_line)
                                                <tr>
                                                    <td>{{ $dealer_line['dealer'] }}</td>
                                                    <td class="text-center">
                                                        @if ($dealer_line['is_ready_to_deliver'])
                                                            <i class="fa fa-circle-check text-primary"></i>
                                                        @else
                                                            <i class="fa fa-circle-xmark text-secondary"></i>
                                                        @endif
                                                    </td>
                                                    <td class="text-center">{{ $dealer_line['amount'] }}</td>
                                                    <td class="text-center">{{ $dealer_line['delivery_month_year'] }}
                                                    </td>
                                                    <td class="text-center"
                                                        style="width:400px; word-break: break-all; white-space: normal;">
                                                        {{ $dealer_line['remark'] }}</td>
                                                    <td class="text-center">{{ $dealer_line['response_date'] }}</td>
                                                    @if (empty($view_only))
                                                        <td class="sticky-col text-center">
                                                            <div class="btn-group">
                                                                <div class="col-sm-12">
                                                                    <div class="dropdown dropleft">

                                                                        <button type="button"
                                                                            class="btn btn-sm btn-alt-secondary dropdown-toggle"
                                                                            id="dropdown-dropleft-dark"
                                                                            data-bs-toggle="dropdown"
                                                                            aria-haspopup="true" aria-expanded="false">
                                                                            <i class="fa fa-ellipsis-vertical"></i>
                                                                        </button>

                                                                        <div class="dropdown-menu"
                                                                            aria-labelledby="dropdown-dropleft-dark">
                                                                            <a class="dropdown-item"
                                                                                href="{{ route('long-term-rental-vendor.specs.edit', ['rental' => $d->id, 'dealer' => $dealer_line['dealer_id']]) }}"
                                                                                target="_blank"><i
                                                                                    class="far fa-edit me-1"></i>
                                                                                แก้ไข</a>

                                                                            <a class="dropdown-item btn-share-modal email"
                                                                                href="javascript:void(0)"
                                                                                data-email="{{ $dealer_line['dealer_email'] }}"
                                                                                data-dealer="{{ $dealer_line['dealer_id'] }}"><i
                                                                                    class="fa fa-arrow-up-from-bracket me-1"></i>
                                                                                แชร์ให้
                                                                                Dealer</a>
                                                                        </div>

                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </td>
                                                    @endif
                                                </tr>
                                            @endforeach
                                        </tbody>

                                    </table>
                                @else
                                    <table class="table table-bordered table-striped">
                                        <thead class="bg-body-dark" style="border-color:white; ">
                                            <tr>
                                                <th rowspan="2" class="text-center" style="width: 100px">
                                                    {{ __('long_term_rentals.dealers') }}</th>
                                                <th colspan="3" class="text-center" style="width: 200px">
                                                    {{ __('long_term_rentals.stock_order') }}
                                                </th>
                                                <th rowspan="2" class="text-center" style="width: 100px">
                                                    {{ __('long_term_rentals.response_date') }}</th>
                                                @if (empty($view_only))
                                                    <th rowspan="2" class="sticky-col text-center"
                                                        style="width: 200px">
                                                        {{ __('lang.tools') }}
                                                    </th>
                                                @endif
                                            </tr>
                                            <tr style="border-bottom-width:2px;">
                                                <th class="text-center" style="width: 100px">
                                                    {{ __('long_term_rentals.car_amount') }}</th>
                                                <th class="text-center" style="width: 100px">
                                                    {{ __('long_term_rentals.delivery_month') }}</th>
                                                <th class="text-center"
                                                    style="width:400px; word-break: break-all; white-space: normal;">
                                                    {{ __('long_term_rentals.remark') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr class="table-empty">
                                                <td class="text-center td-table table-wrap" colspan="6">"
                                                    {{ __('lang.no_list') . __('long_term_rentals.car_table') }} "</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            @else
                <tbody>
                    <tr class="tr-last-item">
                        <td class="text-center td-table table-wrap" colspan="7">
                            " {{ __('lang.no_list') }} "
                        </td>
                    </tr>
                </tbody>
            @endif
        </table>
    </div>
</div>


<br>
<div class="col-auto mt-2">
    <h4>{{ __('long_term_rentals.confirm_check_car') }}</h4>
    <p>หากมีรถพร้อมส่งมอบ กรุณาเลือกรถที่พร้อมส่งมอบ เพื่อไปขั้นตอนอนุมัติรถและอุปกรณ์ต่อไป</p>
    <input type="radio" id="check_delivery_ready" class="form-check-input radio" name="check_delivery"
        value="{{ STATUS_ACTIVE }}" @if ($d->check_delivery == STATUS_ACTIVE && !is_null($d->check_delivery)) checked @endif> <span>&nbsp;
        มีรถพร้อมส่งมอบ</span> &emsp;
    <input type="radio" id="check_delivery_no_ready" class="form-check-input radio" name="check_delivery"
        value="{{ STATUS_DEFAULT }}" @if ($d->check_delivery == STATUS_DEFAULT && !is_null($d->check_delivery)) checked @endif> <span>&nbsp;
        ไม่มีรถพร้อมส่งมอบ</span> &emsp;
    <input type="radio" id="check_delivery_change" class="form-check-input radio" name="check_delivery"
        value="{{ STATUS_INACTIVE }}" @if ($d->check_delivery == STATUS_INACTIVE && !is_null($d->check_delivery)) checked @endif> <span>&nbsp; เปลี่ยนรถ</span>


</div>
<div class="col-sm-6 mt-2">
    <x-forms.text-area-new-line id="reason_delivery" :value="$d->reason_delivery" :label="__('long_term_rentals.remark')" />
</div>

@include('admin.long-term-rental-specs.modals.share-dealer')
