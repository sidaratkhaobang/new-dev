<template v-for="(line,j) in rental_line">
    <div class="col-sm-6">
        <label for="Dealer">{{ __('long_term_rentals.car_class') }}</label><br>
        <span>@{{ line.car_class_text }}</span><br>
        <span>ส่งมอบภายในวันที่ @{{ line.require_date_text }} / 60 วันนับจากลงนามในสัญญา</span>
    </div>
    <div class="col-sm-2">
        <label for="date">{{ __('long_term_rentals.car_color') }}</label><br>
        <span> @{{ line.car_color_text }}</span>
    </div>
    <div class="col-sm-2">
        <label for="date">{{ __('long_term_rentals.car_amount') }}</label><br>
        <span>@{{ line.amount }}</span>
    </div>
    <div class="col-sm-2">
        <label for="date">{{ __('long_term_rentals.customer_need') }}</label><br>
        <span>@{{ line.customer_require }}</span>
    </div>
    <div class="col-sm-2 mt-4 align-self-center">
        <input type="checkbox" class="col-form-label form-check-input me-2" :id="'checkbox-inline-' + line.id"
            :name="'no_car_dealer[' + line.id + ']'" :data-value="line.id" v-model="line.no_cars_checked">
        {{ __('long_term_rentals.no_car_dealer') }}
    </div>
    <div class="col-sm-4 mt-4">
        <div class="row">
            <label class="col-sm-2 col-form-label"
                for="example-hf-email">{{ __('long_term_rentals.no_car_reason') }}</label>
            <div class="col-sm-10">
                <input type="text" class="form-control" :id="'no_car_reason-' + line.id"
                    :name="'no_car_reason[' + line.id + ']'" :data-value="line.id" v-model="line.no_car_reason">
            </div>
        </div>
    </div>
    <span class="mt-4 mb-4">{{ __('long_term_rentals.stock_order') }}</span>
    <div class="table-wrap mb-2">
        <table class="table table-striped">
            <thead class="bg-body-dark">
                <th style="width: 10%"></th>
                <th style="width: 10%">จำนวน</th>
                <th style="width: 4%"></th>
                <th style="width: 10%">{{ __('long_term_rentals.delivery_month_year') }}</th>
                <th style="width: 4%"></th>
                <th style="width: 15%">{{ __('long_term_rentals.remark') }}</th>
                <th style="width: 5%"></th>
            </thead>
            <tbody v-if="line.dealer_check_cars.length > 0">
                <tr v-for="(input,k) in line.dealer_check_cars">
                    <td></td>
                    <td>
                        <input type="number" :id="'amount' + j + k" class="form-control" v-model="input.amount"
                            v-bind:name="'data2['+ line.id + ']['+ k+ '][amount]'" min="0"
                            v-on:keyup="checkKeyup(j,k)" :max="line.amount_car">
                        <input type="hidden" v-bind:name="'data2['+ line.id+ ']['+ k+ '][amount]'" id="amount"
                            v-model="input.amount">
                    </td>
                    <td></td>
                    <td>
                        <div class="input-group">
                            <flatpickr :id="'delivery_month_year' + j + k" v-model="input.delivery_month_year"
                                v-bind:name="'data2['+ line.id+ ']['+ k+ '][delivery_month_year]'"
                                :options="{
                                
                                }">
                            </flatpickr> <span class="input-group-text">
                                <i class="far fa-calendar-check"></i>
                            </span>
                        </div>
                        <input type="hidden" v-bind:name="'data2['+ line.id+ ']['+ k+ '][delivery_month_year]'"
                            id="delivery_month_year" v-model="input.delivery_month_year">

                    </td>
                    <td></td>
                    <td>
                        <input type="text" :id="'remark' + j + k" class="form-control" v-model="input.remark"
                            v-bind:name="'data2['+ line.id + ']['+ k+ '][remark]'">
                        <input type="hidden" v-bind:name="'data2['+ line.id+ ']['+ k+ '][remark]'" id="remark"
                            v-model="input.remark">
                    </td>
                    <td>
                        @if (empty($view))
                            <a class="btn btn-light" v-on:click="remove(j,k)"><i class="fa-solid fa-trash-can"
                                    style="color:red"></i></a>
                        @endif
                    </td>
                </tr>
            </tbody>

            <tbody v-else>
                <tr class="table-empty">
                    <td class="text-center" colspan="9">" {{ __('lang.no_list') }} "</td>
                </tr>
            </tbody>
            <template v-for="(input,k) in del_input_id">
                <input type="hidden" v-bind:name="'del_section[]'" id="del_input_id" v-bind:value="input">
            </template>
        </table>
        @if (empty($view))
            <div class="col-md-12 text-end">
                <button v-if="!line.no_cars_checked" type="button" class="btn btn-primary"
                    v-on:click="addSelect(j)">{{ __('lang.add') }}</button>
            </div>
        @endif
    </div>
</template>
