<div class="modal fade" id="modal-bom" tabindex="-1" data-bs-keyboard="false" data-bs-backdrop="static" aria-labelledby="modal-car-accessory" aria-hidden="false">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-popout" style="width:1250px;">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="car-accessory-modal-label">เพิ่มข้อมูล</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row push">
                    <div class="col-sm-8">
                        <x-forms.select-option id="bom_field" :value="null" :list="null"
                            :label="__('long_term_rentals.name_and_no')" :optionals="['select_class' => 'js-select2-custom', 'ajax' => true]" />
                    </div>

                </div>
                <div id="accessory-new" v-cloak data-detail-uri="" data-title="">
                    <div class="table-wrap">
                        <table class="table table-striped">
                            <thead class="bg-body-dark">
                                <th>#</th>
                                <th>{{ __('long_term_rentals.accessories') }}</th>
                                <th>{{ __('long_term_rentals.amount_accessory') }}</th>
                                <th>{{ __('long_term_rentals.tor_section') }}</th>
                                <th>{{ __('long_term_rentals.remark') }}</th>
                            </thead>
                            <tbody id="list_table">
                            </tbody>
                            <tbody id="empty-list">
                                <tr class="table-empty">
                                    <td class="text-center" colspan="6">"
                                        {{ __('lang.no_list') . __('long_term_rentals.accessories_table') }} "</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-clear-search"
                    data-bs-dismiss="modal">{{ __('lang.back') }}</button>
                <button type="button" class="btn btn-primary"
                    onclick="saveAccessory()">{{ __('lang.save') }}</button>
            </div>
        </div>
    </div>
</div>
