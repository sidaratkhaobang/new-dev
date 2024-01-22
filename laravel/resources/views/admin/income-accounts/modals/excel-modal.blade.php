<div class="modal fade" id="modal-income-excel" aria-labelledby="modal-income-excel" aria-hidden="false">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-popout">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="car-accessory-modal-label">เลือก Transaction ที่ต้องการ</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="form-group row mb-4">
                    <div class="col-sm-3">
                        <x-forms.select-option id="temp_doc_type_id" :value="null" :list="$document_type_list"
                            :label="__('sap_interfaces.doc_type')" />
                    </div>
                    <div class="col-sm-3">
                        <x-forms.select-option id="temp_status" :value="$status" :list="$status_list" :label="__('lang.status')" />
                    </div>
                    <div class="col-sm-6">
                        <label class="text-start col-form-label"
                            for="temp_from_date">{{ __('sap_interfaces.range_date') }}</label>
                        <div class="form-group">
                            <div class="input-daterange input-group">
                                <input type="text" class="js-flatpickr form-control flatpickr-input"
                                    id="temp_from_date" name="temp_from_date" value=""
                                    placeholder="{{ __('lang.select_date') }}" data-week-start="1" data-autoclose="true"
                                    data-today-highlight="true">
                                <div class="input-group-prepend input-group-append">
                                    <span class="input-group-text font-w600">
                                        <i class="fa fa-fw fa-arrow-right"></i>
                                    </span>
                                </div>
                                <input type="text" class="js-flatpickr form-control flatpickr-input"
                                    id="temp_to_date" name="temp_to_date" value=""
                                    placeholder="{{ __('lang.select_date') }}" data-week-start="1" data-autoclose="true"
                                    data-today-highlight="true">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row mb-4">
                    <div class="col-sm-12 text-end">
                        <button onclick="clearFilter()"
                            class="btn btn-outline-secondary btn-clear-search btn-custom-size me-2">
                            <i class="fa fa-rotate-left me-1"></i> {{ __('lang.clear_search') }}</a>
                            <button class="btn btn-primary" onclick="addIncomeList()">
                                <i class="fa fa-plus-circle me-1"></i> {{ __('sap_interfaces.add_list') }}</button>
                    </div>
                </div>
                <div id="income-list" v-cloak data-detail-uri="" data-title="">
                    <h3 class="block-title mb-3">
                        <span>จำนวน transaction ทั้งหมด @{{ income_list.length }} transactions</span>
                    </h3>
                    <div class="table-wrap db-scroll">
                        <table class="table table-striped table-vcenter">
                            <thead class="bg-body-dark">
                                <tr>
                                    <th>#</th>
                                    <th>{{ __('sap_interfaces.transfer_type') }} </th>
                                    <th>{{ __('sap_interfaces.transfer_sub_type') }} </th>
                                    <th>{{ __('sap_interfaces.doc_type') }} </th>
                                    <th>{{ __('sap_interfaces.save_date') }} </th>
                                    <th class="text-center">{{ __('lang.status') }} </th>
                                    <th style="width: 100px;" class="sticky-col"></th>
                                </tr>
                            </thead>
                            <tbody v-if="income_list.length > 0">
                                <tr v-for="(item, index) in income_list.slice(pageStart, pageStart + countOfPage)">
                                    <td>@{{ index + pageStart + 1 }}</td>
                                    <td>@{{ item.transfer_type }}</td>
                                    <td>@{{ item.transfer_sub_type }}</td>
                                    <td>@{{ item.document_type }}</td>
                                    <td>@{{ item.created_date }}</td>
                                    <td>@{{ item.status }}</td>
                                    <td>
                                        <a class="btn btn-light" v-on:click="remove(index + pageStart)"><i
                                                class="fa-solid fa-trash-can" style="color:red"></i></a>
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
                    <template v-if="totalPage > 1">
                        <ul class="pagination">
                            <li class="page-item" v-bind:class="{'disabled': (currPage === 1)}"
                                @click.prevent="setPage(currPage-1)"><a class="page-link" href="">
                                    <i class="icon-arrow-left" style="vertical-align: middle;"></i>
                                </a></li>
                            <li class="page-item" v-if="currPage-1 > 0" @click.prevent="setPage(currPage-1)"><a
                                    class="page-link" href="">@{{ currPage - 1 }}</a></li>
                            <li class="page-item" v-bind:class="'active'" @click.prevent="setPage(currPage)"><a
                                    class="page-link" href="">@{{ currPage }}</a></li>
                            <li class="page-item" v-if="currPage+1 < totalPage" @click.prevent="setPage(currPage+1)"><a
                                    class="page-link" href="">@{{ currPage + 1 }}</a></li>
                            <li class="page-item" v-bind:class="{'disabled': (currPage === totalPage)}"
                                @click.prevent="setPage(currPage+1)"><a class="page-link" href=""><i
                                        class="icon-arrow-right-1" style="vertical-align: middle;"></i>
                                </a>
                            </li>
                        </ul>
                    </template>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-clear-search"
                    data-bs-dismiss="modal">{{ __('lang.cancel') }}</button>
                <button type="button" class="btn btn-primary"
                    onclick="exportIncomeList()">{{ __('lang.download') }}</button>
            </div>
        </div>
    </div>
</div>
