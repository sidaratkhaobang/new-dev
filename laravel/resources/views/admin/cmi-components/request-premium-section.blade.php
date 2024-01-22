


{{--
  ดึงข้อมูลเดือน จาก Model LongTermRentalMonth
  แล้วส่งมาเป็น
  $premium_month
 --}}

@push('styles')
    <style>
        .request-premium-table th > .push {
            margin: 0px !important;
        }

        .request-premium-table th {
            border: 1px solid #E2E8F0 !important;
            padding: 8px !important;
        }

        #table-car-premium-header {
            border-radius: 6px 0px 0px 0px;
            border-right: 1px solid #F6F8FC;
            background: #E2E8F0;
            height: 55px;
        }

        #table-car-premium-sub-header {
            height: 66px;
            border-right: 1px solid #E2E8F0;
            background: #F6F8FC;
        }
    </style>
@endpush

<table class="table table-bordered table-striped request-premium-table">
    <thead class="bg-body-dark" style="border:1px solid #CBD4E1 ">
    <tr>
        <th rowspan="2" class="text-center" style="width: 100px">
            {{__('request_premium.year_rental')}}</th>
        <th colspan="2" class="text-center" style="width: 200px">
            {{__('request_premium.insurance_voluntary')}}
        </th>
        <th colspan="1" class="text-center" style="width: 100px">
            {{__('request_premium.insurance_compulsory')}}</th>
        <th colspan="2" class="text-center">
            {{__('request_premium.sum')}}
        </th>
    </tr>
    <tr id="table-car-premium-sub-header">
        <th colspan="1" style="padding:2.5px;"
            class="text-center">{{__('request_premium.insurance_premium_first_year')}}</th>
        <th colspan="1" style="padding:2.5px;"
            class="text-center">{{__('request_premium.premium_for_lifetime_coverage')}}
            <br>
            {{__('request_premium.average_annual_lease_agreement')}}
        </th>
        <th colspan="1" style="padding:2.5px;"
            class="text-center">{{__('request_premium.compulsory_motor_insurance_premium')}}</th>
        <th colspan="1" style="padding:2.5px;"
            class="text-center">{{__('request_premium.insurance_premium_first_year')}}
            <br>
            + {{__('request_premium.compulsory_motor_insurance_premium')}}
        </th>
        <th colspan="1" style="padding:2.5px;"
            class="text-center">{{__('request_premium.insurance_type_first')}}
            <br>
            + {{__('request_premium.insurance_per_year')}}
        </th>
    </tr>
    </thead>
    <tbody>
    <tr class="table-empty">
    </tr>
    @if(!empty($premium_month))
        @foreach($premium_month as $key_premium => $value_premium)
            <x-forms.hidden id="car[data][{{$key_car_list}}][premium][{{$key_premium}}][id]"
                            :value="$value_car_list['car_premium'][$key_premium]['id'] ?? null"/>
            <x-forms.hidden id="car[data][{{$key_car_list}}][premium][{{$key_premium}}][lt_rental_month_id]"
                            :value="$value_premium?->id"/>
            <tr class="premium_data premium_data{{$key_premium}} " data-premium="premium_data{{$key_premium}}">
                <th class="text-center align-middle" style="margin: auto;">
                    {{$value_premium?->month}} เดือน
                </th>

                <th colspan="1" class="d-flex justify-content-center align-items-centere ">
                    <input type="text"
                           name="car[data][{{$key_car_list}}][premium][{{$key_premium}}][premium_year_one]"
                           value="{{$value_car_list['car_premium'][$key_premium]['premium_year_one'] ?? null }}"
                           class="w-100 form-control input-premium-first-year number-format"
                           placeholder="กรุณาใส่ข้อมูล">
                </th>
                <th colspan="1" class="">
                    <input type="text"
                           name="car[data][{{$key_car_list}}][premium][{{$key_premium}}][premium_all_year]"
                           value="{{$value_car_list['car_premium'][$key_premium]['premium_all_year'] ?? null }}"
                           class="w-100 form-control input-premium-per-year number-format"
                           placeholder="กรุณาใส่ข้อมูล">
                </th>
                <th colspan="1" class="">
                    <input type="text"
                           name="car[data][{{$key_car_list}}][premium][{{$key_premium}}][premium_cmi]"
                           value="{{$value_car_list['car_premium'][$key_premium]['premium_cmi'] ?? null }}"
                           class="w-100 form-control input-compulsory-motor-insurance-premium number-format"
                           placeholder="กรุณาใส่ข้อมูล">
                </th>

                <th colspan="1" class="">
                    <input type="text" id="insurance_premium_sum"
                           name="car[data][{{$key_car_list}}][premium][{{$key_premium}}][premium_year_one_plus_cmi]"
                           readonly
                           value="{{$value_car_list['car_premium'][$key_premium]['premium_year_one_plus_cmi'] ?? null }}"
                           class="w-100 form-control insurance_premium_sum number-format" placeholder="">
                </th>
                <th colspan="1" class="">
                    <input type="text" id="insurance_premium_year_sum"
                           name="car[data][{{$key_car_list}}][premium][{{$key_premium}}][premium_cmi_plus_all_year]"
                           readonly
                           value="{{$value_car_list['car_premium'][$key_premium]['premium_cmi_plus_all_year'] ?? null  }}"
                           class="w-100 form-control insurance_premium_year_sum number-format" placeholder="">
                </th>
            </tr>
        @endforeach
    @else

    @endif
    </tbody>
</table>
