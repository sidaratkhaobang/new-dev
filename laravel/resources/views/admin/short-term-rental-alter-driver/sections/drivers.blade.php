<h4>{{ __('short_term_rentals.driver_detail') }}</h4>
<hr>
<div class="mb-5" id="drivers" v-cloak data-detail-uri="" data-title="">
    <div class="table-wrap">
        <table class="table table-striped">
            <thead class="bg-body-dark">
                <th>#</th>
                <th>{{ __('short_term_rentals.customer_name') }}</th>
                <th>{{ __('short_term_rentals.id_card_no') }}</th>
                <th>{{ __('short_term_rentals.tel') }}</th>
                <th>{{ __('short_term_rentals.driver_license') }}</th>
                <th>{{ __('short_term_rentals.id_card') }}</th>
                @if (!isset($view))
                <th class="sticky-col text-center">{{ __('lang.tools') }}</th>
                @endif
            </thead>
            <tbody v-if="driver_list.length > 0">
                <tr v-for="(item, index) in driver_list">
                    <td>@{{ index + 1 }}</td>
                    <td>@{{ item.name }}</td>
                    <td>@{{ item.citizen_id }}</td>
                    <td>@{{ item.tel }}</td>
                    <td>
                        <div v-if="getFilesPendingCount(item.license_files) > 0">
                            <p class="m-0">{{ __('customers.pending_file') }} : @{{ getFilesPendingCount(item.license_files) }}
                                {{ __('lang.file') }}</p>
                        </div>
                        <div v-if="item.license_files">
                            <div v-for="(license_file, index) in item.license_files">
                                <div v-if="license_file.saved">
                                    <a target="_blank" v-bind:href="license_file.url"><i
                                            class="fa fa-download text-primary"></i>
                                            {{ __('lang.view_file') }}</a>
                                </div>
                            </div>
                        </div>
                    </td>
                    <td>
                        <div v-if="getFilesPendingCount(item.citizen_files) > 0">
                            <p class="m-0">{{ __('customers.pending_file') }} : @{{ getFilesPendingCount(item.citizen_files) }}
                                {{ __('lang.file') }}</p>
                        </div>
                        <div v-if="item.citizen_files">
                            <div v-for="(citizen_file, index) in item.citizen_files">
                                <div v-if="citizen_file.saved">
                                    <a target="_blank" v-bind:href="citizen_file.url"><i
                                            class="fa fa-download text-primary"></i>
                                            {{ __('lang.view_file') }}</a>
                                </div>
                            </div>
                        </div>
                    </td>
                    @if (!isset($view))
                    <td class="sticky-col text-center">
                        @include('admin.components.dropdown-action-vue')
                    </td>
                    @endif
                    <input type="hidden" v-bind:name="'drivers['+ index+ '][id]'" v-bind:value="item.id">
                    <input type="hidden" v-bind:name="'drivers['+ index+ '][name]'" v-bind:value="item.name">
                    <input type="hidden" v-bind:name="'drivers['+ index+ '][citizen_id]'" v-bind:value="item.citizen_id">
                    <input type="hidden" v-bind:name="'drivers['+ index+ '][email]'" v-bind:value="item.email">
                    <input type="hidden" v-bind:name="'drivers['+ index+ '][tel]'" v-bind:value="item.tel">
                    <input type="hidden" v-bind:name="'drivers['+ index+ '][is_check_dup]'" v-bind:value="item.is_check_dup">
                    <input type="hidden" v-bind:name="'drivers['+ index+ '][license_id]'" v-bind:value="item.license_id">
                    <input type="hidden" v-bind:name="'drivers['+ index+ '][license_exp_date]'" v-bind:value="item.license_exp_date">
                    {{-- <input type="hidden" v-bind:name="'drivers['+ index+ '][type]'" v-bind:value="item.type"> --}}
                </tr>
            </tbody>
            <tbody v-else>
                <tr class="table-empty">
                    <td class="text-center" colspan="7">" {{ __('lang.no_list') }} "</td>
                </tr>
            </tbody>
        </table>
    </div>
    @if (!isset($view))
    <div class="row">
        <div class="col-md-12 text-end">
            <button type="button" class="btn btn-primary" onclick="addDriver()">{{ __('lang.add') }}</button>
        </div>
    </div>
    @endif
    @include('admin.short-term-rental-driver.modals.driver')
</div>
