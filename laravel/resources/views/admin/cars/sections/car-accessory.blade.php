<div id="car-accessories" v-cloak data-detail-uri="" data-title="">
    <div class="table-wrap mb-3">
        <table class="table table-striped">
            <thead class="bg-body-dark">
                <th>#</th>
                <th>{{ __('cars.accessory') }}</th>
                <th>{{ __('cars.amount') }}</th>
                <th>{{ __('lang.remark') }}</th>
                @if (!Route::is('*.show'))
                <th class="sticky-col text-center">{{ __('lang.tools') }}</th>
                @endif
            </thead>
            <tbody v-if="car_accessory_list.length > 0">
                <tr v-for="(item, index) in car_accessory_list">
                    <td>@{{ index + 1 }}</td>
                    <td>@{{ item.accessory_text }}</td>
                    <td>@{{ item.amount }}</td>
                    <td>@{{ item.remark }}</td>
                    @if (!Route::is('*.show'))
                    <td class="sticky-col text-center">
                        @include('admin.components.dropdown-action-vue')
                    </td>
                    @endif
                    <input type="hidden" v-bind:name="'car_accessory['+ index+ '][accessory_id]'" id="accessory_id" v-bind:value="item.accessory_id">
                    <input type="hidden" v-bind:name="'car_accessory['+ index+ '][amount]'" id="amount" v-bind:value="item.amount">
                    <input type="hidden" v-bind:name="'car_accessory['+ index+ '][remark]'" id="remark" v-bind:value="item.remark">
                </tr>
            </tbody>
            <tbody v-else>
                <tr class="table-empty">
                    <td class="text-center" colspan="5">" {{ __('lang.no_data') }} "</td>
                </tr>
            </tbody>
        </table>
    </div>
    @if (!Route::is('*.show'))
    <div class="row">
        <div class="col-md-12 text-end">
            <button type="button" class="btn btn-primary" onclick="openCarAccessoryModal()">{{ __('lang.add') }}</button>
        </div>
    </div>
    @endif
    @include('admin.cars.modals.car-accessory')
</div>
