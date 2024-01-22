<x-blocks.block :title="'ข้อมูลผู้ขับขี่'" :optionals="['is_toggle' => false]">
    <div id="drivers">
        <x-slot name="options">
            <button type="button" class="btn btn-primary" onclick="addDriver()">
                <i class="icon-add-circle"></i> {{__('lang.add') }}</button>
        </x-slot>
        <div id="car-options">
            <div v-cloak data-detail-uri="" data-title="">
                <div id="drivers" class="table-wrap">
                    <table class="table table-striped">
                        <thead class="bg-body-dark">
                            <th>#</th>
                            <th>{{ __('short_term_rentals.customer_name') }}</th>
                            <th>{{ __('short_term_rentals.id_card_no') }}</th>
                            <th>{{ __('short_term_rentals.tel') }}</th>
                            <th>{{ __('short_term_rentals.driver_license') }}</th>
                            <th>{{ __('short_term_rentals.id_card') }}</th>
                            <th></th>
                        </thead>
                        <tbody v-if="driver_list.length > 0">
                            <tr v-for="(item, index) in driver_list">
                                <input type="hidden" v-bind:name="'drivers['+ index+ '][id]'" v-bind:value="item.id">
                                <input type="hidden" v-bind:name="'drivers['+ index+ '][email]'" v-bind:value="null">
                                <input type="hidden" v-bind:name="'drivers['+ index+ '][tel]'" v-bind:value="item.tel">
                                <input type="hidden" v-bind:name="'drivers['+ index+ '][is_check_dup]'" v-bind:value="item.is_check_dup">
                                <input type="hidden" v-bind:name="'drivers['+ index+ '][license_id]'" v-bind:value="item.license_id">
                                <input type="hidden" v-bind:name="'drivers['+ index+ '][license_exp_date]'" v-bind:value="item.license_exp_date">
                                <input type="hidden" v-bind:name="'drivers['+ index+ '][type]'" v-bind:value="item.type">
                                <td class="align-middle">
                                    @{{ index+1 }}
                                </td>
                                <td class="align-middle">
                                    <input type="text" v-model="item.name" v-bind:name="'drivers['+ index+ '][name]'" class="form-control" maxlength="255">
                                </td>
                                <td class="align-middle">
                                    <input-citizen-format-vue type="text" v-model="item.citizen_id" :name="'drivers['+ index+ '][citizen_id]'" class="form-control" maxlength="255" />
                                </td>
                                <td class="align-middle">
                                    <input-tel-format-vue :id="'input_phone' + index" class="form-control" v-model="item.tel" :value="item.tel" :name="'drivers[' + index + '][tel]'" />
                                </td>
                                <td>
                                    <drop-zone-vue name="name_test" :id="'driving-license-file-'+index+'drop-zone'" :file="item.license_files" :index="index" filetype="license_files">
                                    </drop-zone-vue>
                                </td>
                                <td>
                                    <drop-zone-vue name="name_test" :id="'citizen-file-'+index+'-drop-zone'" :file="item.citizen_files" :index="index" filetype="citizen_files">
                                    </drop-zone-vue>
                                </td>
                                <td class="align-middle">
                                    <a type="btn btn-mini" href="javascript:void(0)" class="border-0 bg-transparent" @click="remove(index)">
                                        <i class="fa-solid fa-trash-can pe-none" style="color: red;"></i>
                                    </a>
                                </td>
                            </tr>

                        </tbody>
                        <tbody v-else>
                            <tr class="table-empty">
                                <td class="text-center" colspan="7">" {{ __('lang.no_list') }} "</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-blocks.block>