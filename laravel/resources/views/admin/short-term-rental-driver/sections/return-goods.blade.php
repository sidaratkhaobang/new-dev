<h4>{{ __('short_term_rentals.return_good_detail') }}</h4>
<hr>
<div id="return-goods" v-cloak data-detail-uri="" data-title="">
    <div class="table-wrap db-scroll">
        <table class="table table-striped">
            <thead class="bg-body-dark">
                <th style="width: 2px;">#</th>
                <th>{{ __('short_term_rentals.deliver_good_brand_text') }}</th>
                <th>{{ __('short_term_rentals.deliver_good_class_text') }}</th>
                <th>{{ __('short_term_rentals.deliver_good_color_text') }}</th>
                <th>{{ __('short_term_rentals.deliver_good_license_plate') }}</th>
                <th>{{ __('short_term_rentals.deliver_good_chassis_no') }}</th>
                <th>{{ __('short_term_rentals.good_image') }}</th>
                <th class="sticky-col text-center">{{ __('lang.tools') }}</th>
            </thead>
            <tbody v-if="return_good_list.length > 0">
                <tr v-for="(item, index) in return_good_list">
                    <td>@{{ index + 1 }}</td>
                    <td>@{{ item.return_good_brand_text }}</td>
                    <td>@{{ item.return_good_class_text }}</td>
                    <td>@{{ item.return_good_color_text }}</td>
                    <td>@{{ item.return_good_license_plate }}</td>
                    <td>@{{ item.return_good_chassis_no }}</td>
                    <td>
                        <div v-if="getFilesPendingCount(item.return_good_files) > 0">
                            <p class="m-0">{{ __('customers.pending_file') }} : @{{ getFilesPendingCount(item.return_good_files) }}
                                {{ __('lang.file') }}</p>
                        </div>
                        <div v-if="item.return_good_files">
                            <div v-for="(return_good_file, index) in item.return_good_files">
                                <div v-if="return_good_file.saved">
                                    <a target="_blank" v-bind:href="return_good_file.url"><i
                                        class="fa fa-download text-primary"></i>
                                        @{{ return_good_file.name }}</a>
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
                                        <a class="dropdown-item" v-on:click="edit(index)"><i
                                                class="far fa-edit me-1"></i> แก้ไข</a>
                                        <a class="dropdown-item btn-delete-row" v-on:click="remove(index)"><i
                                                class="fa fa-trash-alt me-1"></i> ลบ</a>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </td>
                    <input type="hidden" v-bind:name="'return_goods['+ index+ '][return_good_brand_id]'" v-bind:value="item.return_good_brand_id">
                    <input type="hidden" v-bind:name="'return_goods['+ index+ '][return_good_class_id]'"
                        v-bind:value="item.return_good_class_id">
                    <input type="hidden" v-bind:name="'return_goods['+ index+ '][return_good_color_id]'"
                        v-bind:value="item.return_good_color_id">
                    <input type="hidden" v-bind:name="'return_goods['+ index+ '][return_good_license_plate]'"
                        v-bind:value="item.return_good_license_plate">
                    <input type="hidden" v-bind:name="'return_goods['+ index+ '][return_good_chassis_no]'"
                        v-bind:value="item.return_good_chassis_no">
                </tr>
            </tbody>
            <tbody v-else>
                <tr class="table-empty">
                    <td class="text-center" colspan="8">" {{ __('lang.no_list') }} "</td>
                </tr>
            </tbody>
        </table>
    </div>
    <div class="row">
        <div class="col-md-12 text-end">
            <button type="button" class="btn btn-primary" onclick="addReturnGood()"
                id="openModal">{{ __('lang.add') }}</button>
        </div>
    </div>
</div>
<br>
@include('admin.short-term-rental-driver.modals.return-good')
