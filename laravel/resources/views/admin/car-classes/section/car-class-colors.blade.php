<h5>{{ __('car_classes.color_table_name') }}</h5>
<hr>
<div id="car-class-colors" v-cloak data-detail-uri="" data-title="">
    <div class="table-wrap">
        <table class="table table-striped">
            <thead class="bg-body-dark">
                <th>#</th>
                <th>{{ __('car_classes.color') }}</th>
                <th>{{ __('car_classes.standard_price') }}</th>
                <th>{{ __('car_classes.color_price') }}</th>
                <th>{{ __('car_classes.total_price') }}</th>
                <th>{{ __('car_classes.remark') }}</th>
                <th class="sticky-col text-center">{{ __('lang.tools') }}</th>
            </thead>
            <tbody v-if="class_color_list.length > 0">
                <tr v-for="(item, index) in class_color_list">
                    <td>@{{ index + 1 }}</td>
                    <td>@{{ item.color_text }}</td>
                    <td>@{{ item.standard_price }}</td>
                    <td>@{{ item.color_price }}</td>
                    <td>@{{ item.total_price }}</td>
                    <td>@{{ item.remark }}</td>
                    <td class="sticky-col text-center">
                        @include('admin.components.dropdown-action-vue')
                    </td>
                    <input type="hidden" v-bind:name="'car_class_color['+ index+ '][id]'" v-bind:value="item.id">
                    <input type="hidden" v-bind:name="'car_class_color['+ index+ '][car_color_id]'" id="car_color_id" v-bind:value="item.car_color_id">
                    <input type="hidden" v-bind:name="'car_class_color['+ index+ '][standard_price]'" id="standard_price" v-bind:value="item.standard_price">
                    <input type="hidden" v-bind:name="'car_class_color['+ index+ '][color_price]'" id="color_price" v-bind:value="item.color_price">
                    <input type="hidden" v-bind:name="'car_class_color['+ index+ '][remark]'" id="remark" v-bind:value="item.remark">
                </tr>
            </tbody>
            <tbody v-else>
                <tr class="table-empty">
                    <td class="text-center" colspan="7">“ {{ __('lang.no_list').__('car_classes.color_table_name') }} “</td>
                </tr>
            </tbody>
        </table>
    </div>
    <div class="row">
        <div class="col-md-12 text-end">
            <button type="button" class="btn btn-primary" onclick="openCarColorModal()">{{ __('lang.add') }}</button>
        </div>
    </div>
    @include('admin.car-classes.modals.car-class-color')

</div>
