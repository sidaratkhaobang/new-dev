<x-modal :id="'asset-car-excel'" :title="'เลือกรถที่ต้องการ'">
    <div class="form-group row mb-4">
        <div class="col-sm-3">
            <x-forms.select-option id="temp_status" :value="null" :list="$status_list" :label="__('lang.status')" />
        </div>
        <div class="col-sm-3">
            <x-forms.select-option id="temp_lot_id" :value="null" :list="null" :label="__('asset_cars.lot_no')"
                :optionals="[
                    'ajax' => true,
                ]" />
        </div>
        <div class="col-sm-3">
            <x-forms.select-option id="temp_car_class_id" :value="null" :list="null" :label="__('asset_cars.car_class')"
                :optionals="[
                    'ajax' => true,
                ]" />
        </div>
    </div>
    <div class="form-group row mb-4">
        <div class="col-sm-6">
            <x-forms.select-option id="temp_car_id" :value="null" :list="null" :label="__('asset_cars.car_detail')"
                :optionals="[
                    'ajax' => true,
                ]" />
        </div>
    </div>
    <div class="row mb-4">
        <div class="col-sm-12 text-end">
            <button onclick="clearFilter()" class="btn btn-outline-secondary btn-clear-search btn-custom-size me-2">
                <i class="fa fa-rotate-left me-1"></i> {{ __('lang.clear_search') }}</a>
                <button class="btn btn-primary" onclick="addAssetCarList()">
                    <i class="fa fa-plus-circle me-1"></i>เพิ่มรถ</button>
        </div>
    </div>
    <div id="asset-car-list" v-cloak data-detail-uri="" data-title="" style="height:250px; overflow-y:auto;">
        <h3 class="block-title mb-3">
            <span>จำนวนรถทั้งหมด @{{ asset_car_list.length }}คัน</span>
        </h3>
        <div class="table-wrap db-scroll">
            <table class="table table-striped table-vcenter">
                <thead class="bg-body-dark">
                    <tr>
                        <th>{{ __('asset_cars.lot_no') }} </th>
                        <th>{{ __('cars.brand') }} </th>
                        <th>{{ __('cars.engine_no') }} </th>
                        <th>{{ __('cars.chassis_no') }} </th>
                        <th>{{ __('cars.license_plate') }} </th>
                        <th style="width: 100px;" class="sticky-col"></th>
                    </tr>
                </thead>
                <tbody v-if="asset_car_list.length > 0">
                    <tr v-for="(item, index) in asset_car_list">
                        <td>@{{ item.lot_no }}</td>
                        <td>@{{ item.car_class_text }}</td>
                        <td>@{{ item.engine_no }}</td>
                        <td>@{{ item.chassis_no }}</td>
                        <td>@{{ item.license_plate }}</td>
                        <td>
                            <a class="btn btn-light" v-on:click="remove(index)"><i class="fa-solid fa-trash-can"
                                    style="color:red"></i></a>
                        </td>
                    </tr>
                </tbody>
                <tbody v-else>
                    <tr>
                        <td colspan="7" class="text-center">" {{ __('lang.no_list') }} "</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
    <x-slot name="footer">
        <input name="excel_type" type="hidden" id="excel_type">
        <button type="button" class="btn btn-secondary btn-clear-search"
            data-bs-dismiss="modal">{{ __('lang.cancel') }}</button>
        <button type="button" class="btn btn-primary" onclick="exportAssetCarList()">{{ __('lang.download') }}</button>
    </x-slot>
</x-modal>
