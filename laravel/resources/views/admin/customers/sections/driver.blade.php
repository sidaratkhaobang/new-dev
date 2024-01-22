<div id="customer-driver" v-cloak data-detail-uri="" data-title="">
    <div class="table-wrap">
        <table class="table table-striped">
            <thead class="bg-body-dark">
                <th style="width: 2px;">#</th>
                <th>{{ __('customers.full_name') }}</th>
                <th>{{ __('customers.tel_driver') }}</th>
                <th>{{ __('customers.citizen') }}</th>
                <th>{{ __('customers.email') }}</th>
                <th>{{ __('customers.driving_license') }}</th>
                <th>{{ __('customers.citizen') }}</th>
                <th class="sticky-col text-center">{{ __('lang.tools') }}</th>
            </thead>
            <tbody v-if="customer_driver_list.length > 0">
                <tr v-for="(item, index) in customer_driver_list">
                    <td>@{{ index + 1 }}</td>
                    <td>@{{ item.name }}</td>
                    <td>@{{ item.tel }}</td>
                    <td>@{{ item.citizen_id }}</td>
                    <td>@{{ item.email }}</td>
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
                                            @{{ license_file.name }}</a>
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
                                            @{{ citizen_file.name }}</a>
                                </div>
                            </div>
                        </div>
                    </td>
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
                                        <a class="dropdown-item" v-on:click="editDriver(index)"><i
                                                class="far fa-edit me-1"></i> แก้ไข</a>
                                        <a class="dropdown-item btn-delete-row" v-on:click="removeDriver(index)"><i
                                                class="fa fa-trash-alt me-1"></i> ลบ</a>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </td>
                    <input type="hidden" v-bind:name="'customer_driver['+ index+ '][id]'" v-bind:value="item.id">
                    <input type="hidden" v-bind:name="'customer_driver['+ index+ '][name]'" id="name"
                        v-bind:value="item.name">
                    <input type="hidden" v-bind:name="'customer_driver['+ index+ '][tel]'" id="tel"
                        v-bind:value="item.tel">
                    <input type="hidden" v-bind:name="'customer_driver['+ index+ '][citizen_id]'" id="citizen_id"
                        v-bind:value="item.citizen_id">
                    <input type="hidden" v-bind:name="'customer_driver['+ index+ '][email]'" id="email"
                        v-bind:value="item.email">
                </tr>
            </tbody>
            <tbody v-else>
                <tr class="table-empty">
                    <td class="text-center" colspan="8">“
                        {{ __('lang.no_list') . __('customers.driver_table') }} “</td>
                </tr>
            </tbody>
        </table>
    </div>
    <div class="row">
        <div class="col-md-12 text-end">
            <button type="button" class="btn btn-primary" onclick="addDriver()"
                id="openModal">{{ __('lang.add') }}</button>
        </div>
    </div>
</div>
@include('admin.customers.modals.driver-modal')
