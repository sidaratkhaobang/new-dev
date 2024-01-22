<div class="modal fade" id="modal-repair-accident" tabindex="-1" aria-labelledby="modal-repair-accident"
    aria-hidden="false">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-popout" style="max-width: 80%;">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="repair-accident-modal-label">สร้างใบสั่งซ่อม</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="block-content">
                    <div class="container-section" style="min-height:250px;">
                        <div class="left-section">
                            <div class="table-wrap db-scroll">
                                <table class="table table-striped table-vcenter">
                                    <thead class="bg-body-dark">
                                        <tr>
                                            @include('admin.components.block-header', [
                                                'text' => __('accident_orders.accident_sheet'),
                                            ])
                                        </tr>
                                        <tr class="mb-3">
                                            <div class="block-header">
                                                <div class="col-sm-7 mb-3 ">
                                                    <x-forms.select-option id="report_id" :list="null"
                                                        :value="$d->car_id" :label="__('accident_orders.report_no')" :optionals="[
                                                            'ajax' => true,
                                                            'default_option_label' => $car_license,
                                                            'required' => true,
                                                        ]" />
                                                </div>
                                                <div class="col-sm-4">
                                                    <label></label><br>
                                                    <button type="button" class="btn btn-primary" id="search"><i
                                                            class="icon-search"></i>
                                                        {{ __('lang.search') }}</button>
                                                </div>
                                            </div>
                                        </tr>
                                        <tr>
                                            <th class="text-center" style="width: 70px;">
                                                <div class="form-check d-inline-block">
                                                    <input class="form-check-input" type="checkbox" value=""
                                                        id="selectAll" name="selectAll">
                                                    <label class="form-check-label" for="selectAll"></label>
                                                </div>
                                            </th>
                                            <th style="width: 25%;">@sortablelink('name', __('accident_informs.before_image'))</th>
                                            <th style="width: 25%;">@sortablelink('name_th', __('accident_informs.repair_characteristics'))</th>
                                            <th style="width: 25%;">@sortablelink('name_th', __('accident_informs.wound_characteristics'))</th>

                                        </tr>
                                    </thead>
                                    <tbody v-if="accident_list_unselected.length > 0">
                                        <template v-for="(item, index) in accident_list_unselected">
                                            <tr>
                                                <td class="text-center">
                                                    <div class="form-check d-inline-block">
                                                        <input class="form-check-input form-check-input-each"
                                                            type="checkbox" v-model="item.is_check">

                                                    </div>
                                                </td>
                                                <td>
                                                    <img class="img-block " :src="item.before_files.url" alt=""
                                                        style="width:130px; height:130px">
                                                </td>
                                                <td>@{{ item.accident_claim }}</td>
                                                <td>@{{ item.wound_characteristics }}</td>
                                            </tr>
                                        </template>
                                    </tbody>
                                    <tbody v-else>
                                        <td class="text-center" colspan="4">" {{ __('lang.no_list') }} "</td>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="center-section">
                            <div class="vertical-center text-center">
                                <div class="svg-container" id="to_right">
                                    <svg width="44" height="44" viewBox="0 0 44 44" fill="none"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <path
                                            d="M24.4274 28.8172C24.2374 28.8172 24.0474 28.7472 23.8974 28.5972C23.7579 28.456 23.6797 28.2656 23.6797 28.0672C23.6797 27.8688 23.7579 27.6783 23.8974 27.5372L29.4374 21.9972L23.8974 16.4572C23.7579 16.316 23.6797 16.1256 23.6797 15.9272C23.6797 15.7288 23.7579 15.5383 23.8974 15.3972C24.1874 15.1072 24.6674 15.1072 24.9574 15.3972L31.0274 21.4672C31.3174 21.7572 31.3174 22.2372 31.0274 22.5272L24.9574 28.5972C24.8074 28.7472 24.6174 28.8172 24.4274 28.8172Z"
                                            fill="#4D82F3" />
                                        <path
                                            d="M30.33 22.75H13.5C13.09 22.75 12.75 22.41 12.75 22C12.75 21.59 13.09 21.25 13.5 21.25H30.33C30.74 21.25 31.08 21.59 31.08 22C31.08 22.41 30.74 22.75 30.33 22.75Z"
                                            fill="#4D82F3" />
                                        <rect x="0.5" y="0.5" width="43" height="43" rx="21.5"
                                            stroke="#94A3B8" />
                                    </svg>
                                </div>

                                <br>

                                <div class="svg-container" id="to_left">
                                    <svg width="44" height="44" viewBox="0 0 44 44" fill="none"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <path
                                            d="M19.5726 15.1828C19.7626 15.1828 19.9526 15.2528 20.1026 15.4028C20.2421 15.544 20.3203 15.7344 20.3203 15.9328C20.3203 16.1312 20.2421 16.3217 20.1026 16.4628L14.5626 22.0028L20.1026 27.5428C20.2421 27.684 20.3203 27.8744 20.3203 28.0728C20.3203 28.2712 20.2421 28.4617 20.1026 28.6028C19.8126 28.8928 19.3326 28.8928 19.0426 28.6028L12.9726 22.5328C12.6826 22.2428 12.6826 21.7628 12.9726 21.4728L19.0426 15.4028C19.1926 15.2528 19.3826 15.1828 19.5726 15.1828Z"
                                            fill="#4D82F3" />
                                        <path
                                            d="M13.67 21.25L30.5 21.25C30.91 21.25 31.25 21.59 31.25 22C31.25 22.41 30.91 22.75 30.5 22.75L13.67 22.75C13.26 22.75 12.92 22.41 12.92 22C12.92 21.59 13.26 21.25 13.67 21.25Z"
                                            fill="#4D82F3" />
                                        <rect x="0.5" y="0.5" width="43" height="43" rx="21.5"
                                            stroke="#94A3B8" />
                                    </svg>
                                </div>

                            </div>
                        </div>
                        <div class="right-section">
                            <div class="table-wrap db-scroll">
                                <table class="table table-striped table-vcenter">
                                    <thead class="bg-body-dark">
                                        <tr>
                                            @include('admin.components.block-header', [
                                                'text' => __('accident_orders.repair_selected'),
                                            ])
                                        </tr>
                                        <tr class="mb-3">
                                            <div class="block-header">
                                                <div class="col-sm-3 mb-3">
                                                    <x-forms.select-option id="garage_id" :list="null"
                                                        :value="null" :label="__('accident_informs.garage')" :optionals="[
                                                            'ajax' => true,
                                                            'default_option_label' => $car_license,
                                                            'required' => true,
                                                            'input_class' => 'vl',
                                                        ]" />
                                                </div>
                                                <div class="col-sm-3 mb-3">
                                                    <x-forms.date-input id="send_repair_date" name="send_repair_date"
                                                        :value="$d->send_repair_date" :label="__('accident_orders.send_repair_date')" :optionals="['required' => true]" />
                                                </div>

                                                <div class="col-sm-2 mb-3">
                                                    <x-forms.input-new-line id="due_date" :value="$d->code"
                                                        :label="__('accident_orders.due_date')" :optionals="[
                                                            'input_class' => 'number-format',
                                                            'required' => true,
                                                        ]" />
                                                </div>

                                                <div class="col-sm-3">
                                                    <label></label><br>
                                                    <button type="button" class="btn btn-primary"
                                                        onclick="useAll()" id="use-all">
                                                        {{ __('accident_orders.use_all') }}</button>
                                                </div>
                                            </div>
                                        </tr>
                                        <tr>
                                            <th class="text-center" style="width: 70px;">
                                                <div class="form-check d-inline-block">
                                                    <input class="form-check-input" type="checkbox" value=""
                                                        id="selectAll2" name="selectAll2">
                                                    <label class="form-check-label" for="selectAll"></label>
                                                </div>
                                            </th>
                                            <th style="width: 25%;">@sortablelink('name', __('accident_informs.before_image'))</th>
                                            <th style="width: 25%;">@sortablelink('name_th', __('accident_informs.repair_characteristics'))</th>
                                            <th style="width: 25%;">@sortablelink('name_th', __('accident_informs.wound_characteristics'))</th>
                                            <th style="width: 25%;">@sortablelink('name_th', __('accident_informs.garage'))</th>
                                            <th style="width: 25%;">@sortablelink('name_th', __('accident_orders.send_repair_date'))</th>
                                            <th style="width: 25%;">@sortablelink('name_th', __('accident_orders.completed'))</th>

                                        </tr>
                                    </thead>
                                    <tbody v-if="accident_list_selected.length > 0">
                                        <template v-for="(item2, index2) in accident_list_selected">
                                            <tr>
                                                <td class="text-center">
                                                    <div class="form-check d-inline-block">
                                                        <input class="form-check-input form-check-input-each2"
                                                            type="checkbox" v-model="item2.is_check">

                                                    </div>
                                                </td>
                                                <td>
                                                    <img class="img-block " :src="item2.before_files.url"
                                                        alt="" style="width:130px; height:130px">
                                                </td>
                                                <td>@{{ item2.accident_claim }}</td>
                                                <td>@{{ item2.wound_characteristics }}</td>
                                                <td>@{{ item2.garage ? item2.garage.text : '' }}</td>
                                                <td>@{{ item2.garage ? item2.send_repair_date : '' }}</td>
                                                <td>@{{ item2.garage ? item2.due_date : '' }}</td>
                                            </tr>
                                        </template>
                                    </tbody>
                                    <tbody v-else>
                                        <td class="text-center" colspan="7">" {{ __('lang.no_list') }} "</td>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-clear-search"
                    data-bs-dismiss="modal">{{ __('lang.cancel') }}</button>
                <button type="button" class="btn btn-primary" onclick="save()" id="save-modal">{{ __('lang.save') }}</button>
            </div>
        </div>
    </div>
</div>
