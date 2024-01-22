@if (sizeof($tor_list) > 0)
    @foreach ($tor_list as $key => $item)
        <x-forms.hidden id="tor_id" :value="null" />
        <div class="block {{ __('block.styles') }}">
            <div class="block-content">
                <div class="row">
                    <div class="col-1">
                        <h1>{{ $key + 1 }}</h1>
                    </div>
                    <div class="col-8">
                        รายละเอียด TOR
                        <p>{{ $item->remark_tor }}</p>
                    </div>
                    @if (!isset($view_only))
                        @if (strcmp($d->spec_status, SpecStatusEnum::DRAFT) == 0)
                            <div class="col-3 text-end">
                                <div class="btn-group" role="group">
                                    <a class="btn btn-outline-danger" onclick="deleteCarTor('{{ $item->tor_id }}')"><i
                                            class="fa fa-trash-alt me-1"></i></a>
                                    <a class="btn btn-outline-primary" type="button"
                                        onclick="editCarTor('{{ $item->tor_id }}')"><i class="fa fa-edit me-1"></i></a>
                                </div>
                            </div>
                        @endif
                    @endif
                    {{-- @if (strcmp($d->spec_status, SpecStatusEnum::PENDING_CHECK) == 0)
                        <div class="col-3 text-end">
                            <button type="button" class="btn btn-primary"
                                onclick="addBomAccessory('{{ $item->tor_id }}')"><i
                                    class="fa fa-plus-circle me-1"></i>{{ __('long_term_rentals.bom') }}</button>
                            <button type="button" class="btn btn-primary" onclick="addManualAccessory()"><i
                                    class="fa fa-plus-circle me-1"></i>{{ __('long_term_rentals.add_manually') }}</button>
                        </div>
                    @endif --}}
                </div>
                <div class="table-wrap">
                    <table class="table table-striped">
                        <thead class="bg-body-dark">
                            <th style="width: 70%;">{{ __('long_term_rentals.car_class') }}</th>
                            <th style="width: 10%;">{{ __('long_term_rentals.car_color') }}</th>
                            <th style="width: 10%;">{{ __('long_term_rentals.need_accessory') }}</th>
                            <th style="width: 10%;">{{ __('long_term_rentals.car_amount') }}</th>
                            {{-- @if (strcmp($d->spec_status, SpecStatusEnum::PENDING_CHECK) == 0)
                                <th class="sticky-col text-center"></th>
                            @endif --}}
                        </thead>
                        <tbody>
                            @if (sizeof($item->tor_line_list) > 0)
                                @foreach ($item->tor_line_list as $item_line)
                                    <tr>
                                        <td>{{ $item_line->car_class_text }}</td>
                                        <td>{{ $item_line->car_color_text }}</td>
                                        <td>
                                            @if (strcmp($item_line->have_accessories, BOOL_TRUE) == 0)
                                                <i class="far fa-check-circle" aria-hidden="true"
                                                    style="color: green;"></i>
                                                ต้องซื้อ
                                            @else
                                                <i class="far fa-check-circle" aria-hidden="true"
                                                    style="color: red;"></i>
                                                ไม่ต้องซื้อ
                                            @endif
                                        </td>
                                        <td>{{ $item_line->amount_car }}</td>
                                        {{-- @if (strcmp($d->spec_status, SpecStatusEnum::PENDING_CHECK) == 0)
                                            <td class="text-center toggle-table" style="width: 30px">
                                                <i class="fa fa-angle-right text-muted"></i>
                                            </td>
                                        @endif --}}
                                    </tr>
                                    {{-- @if (strcmp($d->spec_status, SpecStatusEnum::PENDING_CHECK) == 0)
                                        <tr style="display: none;">
                                            <td class="td-table" colspan="5">
                                                <div class="row push">
                                                    <div class="col-md-6 text-left">
                                                        <span>{{ __('long_term_rentals.accessory_list') }}</span>
                                                    </div>
                                                    <div class="col-md-6 text-end">
                                                        <button type="button" class="btn btn-primary" onclick=""><i
                                                                class="fa fa-plus-circle me-1"></i>แก้ไข/เพิ่ม</button>
                                                    </div>
                                                </div>
                                                <table class="table table-striped">
                                                    <thead class="bg-body-dark">
                                                        <th>{{ __('long_term_rentals.accessories') }}</th>
                                                        <th>{{ __('long_term_rentals.car_amount_per') }}</th>
                                                        <th>{{ __('long_term_rentals.total_amount') }}</th>
                                                        <th>{{ __('long_term_rentals.remark') }}</th>
                                                    <tbody>
                                                        @if (sizeof($item_line->tor_line_accessory) > 0)
                                                            @foreach ($item_line->tor_line_accessory as $item_line_accessory)
                                                                <tr>
                                                                    <td>{{ $item_line_accessory->accessory_text }}
                                                                    </td>
                                                                    <td>{{ $item_line_accessory->amount_per_car_accessory }}
                                                                    </td>
                                                                    <td>{{ $item_line_accessory->amount_accessory }}
                                                                    </td>
                                                                    <td>{{ $item_line_accessory->remark }}</td>
                                                                </tr>
                                                            @endforeach
                                                        @else
                                                            <tr>
                                                                <td class="text-center" colspan="5">"
                                                                    {{ __('lang.no_list') }} "
                                                                </td>
                                                            </tr>
                                                        @endif
                                                    </tbody>
                                                </table>
                                            </td>
                                        </tr>
                                    @endif --}}
                                @endforeach
                            @else
                                <tr class="table-empty">
                                    <td class="text-center" colspan="5">"
                                        {{ __('lang.no_list') . __('long_term_rentals.car_table') }} "</td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endforeach
@else
    <div class="table-wrap">
        <table class="table table-striped">
            <thead class="bg-body-dark">
                <th style="width: 70%;">{{ __('long_term_rentals.car_class') }}</th>
                <th style="width: 10%;">{{ __('long_term_rentals.car_color') }}</th>
                <th style="width: 10%;">{{ __('long_term_rentals.need_accessory') }}</th>
                <th style="width: 10%;">{{ __('long_term_rentals.car_amount') }}</th>
            </thead>
            <tbody>
                <tr class="table-empty">
                    <td class="text-center" colspan="5">"
                        {{ __('lang.no_list') . __('long_term_rentals.car_table') }} "</td>
                </tr>
            </tbody>
        </table>
    </div>
@endif
