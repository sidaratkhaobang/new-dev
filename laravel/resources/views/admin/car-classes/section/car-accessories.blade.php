<h5>{{ __('car_classes.accessories_table_name') }}</h5>
<hr>
<div id="class-car-accessories" v-cloak data-detail-uri="" data-title="">
    <div class="table-wrap">
        <table class="table table-striped">
            <thead class="bg-body-dark">
                <th>#</th>
                <th>{{ __('car_classes.accessories') }}</th>
                {{-- <th>{{ __('car_classes.class') }}</th> --}}
                <th>{{ __('car_classes.remark') }}</th>
                <th class="sticky-col text-center">{{ __('lang.tools') }}</th>
            </thead>
            <tbody v-if="class_accessory_list.length > 0">
                <tr v-for="(item, index) in class_accessory_list">
                    <td>@{{ index + 1 }}</td>
                    <td>@{{ item.accessory_text }}</td>
                    {{-- <td>@{{ item.accessory_version_text }}</td> --}}
                    <td>@{{ item.remark }}</td>
                    <td class="sticky-col text-center">
                        @include('admin.components.dropdown-action-vue')
                    </td>
                    <input type="hidden" v-bind:name="'car_class_accessory['+ index+ '][id]'" v-bind:value="item.id">
                    <input type="hidden" v-bind:name="'car_class_accessory['+ index+ '][accessory_id]'" id="accessory_id" v-bind:value="item.accessory_id">
                    {{-- <input type="hidden" v-bind:name="'car_class_accessory['+ index+ '][accessory_version_id]'" id="accessory_version_id" v-bind:value="item.accessory_version_id"> --}}
                    <input type="hidden" v-bind:name="'car_class_accessory['+ index+ '][remark]'" id="remark" v-bind:value="item.remark">
                </tr>
            </tbody>
            <tbody v-else>
                <tr class="table-empty">
                    <td class="text-center" colspan="5">“ {{ __('lang.no_list').__('car_classes.accessories_table_name') }} “</td>
                </tr>
            </tbody>
        </table>
    </div>
    <div class="row">
        <div class="col-md-12 text-end">
            <button type="button" class="btn btn-primary" onclick="openCarAccessoryModal()">{{ __('lang.add') }}</button>
        </div>
    </div>
    @include('admin.car-classes.modals.class-car-accessory')

</div>
