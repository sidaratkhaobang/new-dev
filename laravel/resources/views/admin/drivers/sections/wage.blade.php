<h4>{{ __('drivers.driver_wage_table') }}</h4>
<hr>
<div id="driver-wage" v-cloak data-detail-uri="" data-title="">
    <div class="table-wrap">
        <table class="table table-striped">
            <thead class="bg-body-dark">
                <th style="width: 2px;">#</th>
                <th>{{ __('drivers.wage_name') }}</th>
                <th>{{ __('drivers.service_type') }}</th>
                <th>{{ __('drivers.wage_category') }}</th>
                <th>{{ __('drivers.wage_cal_type') }}</th>
                <th>{{ __('drivers.amount') }}</th>
                @if (!isset($view))
                    <th class="sticky-col text-center">{{ __('lang.tools') }}</th>
                @endif
            </thead>
            <tbody v-if="driver_wage_list.length > 0">
                <tr v-for="(item, index) in driver_wage_list">
                    <td>@{{ index + 1 }}</td>
                    <td>@{{ item.driver_wage_text}}</td>
                    <td>@{{ item.service_type_text}}</td>
                    <td>@{{ item.driver_wage_category_text}}</td>
                    <td>@{{ item.wage_cal_type_text}}</td>
                    <td class="text-end">@{{ convertNumberToFloat(item.amount)}} @{{convertTextAmountType(item.amount_type)}}</td>
                    @if (!isset($view))
                        <td class="sticky-col text-center">
                            <div class="btn-group">
                                <div class="col-sm-12">
                                    <div class="dropdown dropleft">
                                        <button type="button" class="btn btn-sm btn-alt-secondary dropdown-toggle"
                                            id="dropdown-dropleft-dark" data-bs-toggle="dropdown" aria-haspopup="true"
                                            aria-expanded="false">
                                            <i class="fa fa-ellipsis-vertical"></i>
                                        </button>
                                        <div class="dropdown-menu" aria-labelledby="dropdown-dropleft-dark">
                                            <a class="dropdown-item" v-on:click="editDriverWage(index)"><i
                                                    class="far fa-edit me-1"></i> แก้ไข</a>
                                            <a class="dropdown-item btn-delete-row"
                                                v-on:click="removeDriverWage(index)"><i
                                                    class="fa fa-trash-alt me-1"></i> ลบ</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </td>
                    @endif
                    <input type="hidden" v-bind:name="'driver_wage['+ index+ '][id]'" v-bind:value="item.id">
                    <input type="hidden" v-bind:name="'driver_wage['+ index+ '][driver_wage_id]'" id="driver_wage_id" v-bind:value="item.driver_wage_id">
                    <input type="hidden" v-bind:name="'driver_wage['+ index+ '][amount]'" id="amount" v-bind:value="item.amount">
                    <input type="hidden" v-bind:name="'driver_wage['+ index+ '][amount_type]'" id="amount_type" v-bind:value="item.amount_type">
                </tr>
            </tbody>
            <tbody v-else>
                <tr class="table-empty">
                    <td class="text-center" colspan="8">“
                        {{ __('lang.no_list') . __('drivers.driver_wage_table') }} “</td>
                </tr>
            </tbody>
        </table>
    </div>
    @if (!isset($view))
        <div class="row">
            <div class="col-md-12 text-end">
                <button type="button" class="btn btn-primary" onclick="addDriverWage()"
                    id="openModal">{{ __('lang.add') }}</button>
            </div>
        </div>
    @endif
</div>
<br>
@include('admin.drivers.modals.driver-wage-modal')
