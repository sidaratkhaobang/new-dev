<div class="block {{ __('block.styles') }}" id="accident-open">
    @section('block_options_1')
        <div class="block-options">
            <div class="block-options-item">
                @can(Actions::Manage . '_' . Resources::AccidentOrder)
                    <button type="button" class="btn btn-primary" onclick="addOrder()" id="openModal"><i
                            class="icon-add-circle"></i> {{ __('accident_orders.add_order') }}</button>
                @endcan
            </div>
        </div>
    @endsection

    @include('admin.components.block-header', [
        'text' => __('accident_orders.accident_open'),
        'block_option_id' => '_1',
    ])
    <div class="block-content">
        <div id="accident-repair-open-vue" v-cloak data-detail-uri="" data-title="">
            <template v-if="accident_open_list.length > 0">
                <div class="table-wrap db-scroll" v-for="(item, index) in accident_open_list">
                    <table class="table table-striped table-vcenter">
                        <thead class="bg-body-dark">
                            <tr>
                                <th colspan="10"> <button type="button" class="btn btn-primary float-end"
                                        @click="editRepairOpen(index)"><i class="icon-menu-tools"></i>
                                        {{ __('lang.edit') }}</button></th>
                            </tr>
                            <tr>
                                <th class="th-topic" style="border-top-left-radius: 7px;">
                                    <div class="vl vl-topic">
                                        <span v-if="item.repair_type === RepairClaimEnum.HARD_BUMP"
                                            class="badge badge-custom badge-bg-primary">
                                            <i class="icon-menu-accident me-1"></i> ชนหนัก
                                        </span>
                                        <span v-else-if="item.repair_type === RepairClaimEnum.SOFT_BUMP"
                                            class="badge badge-custom badge-bg-success">
                                            <i class="icon-menu-replace-car me-1"></i> ชนเบา
                                        </span>
                                        <span v-else-if="item.repair_type === RepairClaimEnum.TTL"
                                            class="badge badge-custom badge-bg-danger">
                                            <i class="icon-menu-document-normal me-1"></i> TTL
                                        </span>
                                    </div>
                                </th>
                                <th class="th-topic">
                                    <div class="vl vl-topic">
                                        เลขที่ใบแจ้งอุบัติเหตุ <br> @{{ item.worksheet_no }}
                                    </div>
                                </th>
                                <th class="th-topic">

                                    <div class="vl vl-topic">
                                        เลขที่ใบเคลม <br>
                                    </div>
                                </th>

                                <th class="th-topic">

                                    <div class="vl vl-topic">
                                        วันที่เกิดเหตุ <br> @{{ item.accident_date }}
                                    </div>
                                </th>

                                <th class="th-topic" style="width:100px;">

                                    <div class="vl vl-topic">
                                        เคส <br> @{{ item.case }}
                                    </div>
                                </th>
                                <th class="th-topic">

                                    <div class="vl vl-topic">
                                        ลักษณะเกิดเหตุ <br> @{{ item.accident_description }}
                                    </div>
                                </th>
                                <th class="th-topic">
                                    <div>
                                        จำนวนเคลม <br> @{{ item.count_accident_line }}
                                    </div>
                                </th>
                                <th></th>
                                <th></th>
                                <th class="th-topic text-center toggle-table" style="border-top-right-radius: 7px;">
                                    <i class="icon-arrow-up text-muted"></i>
                                </th>
                            </tr>
                            <tr class="tr-block">
                                <th style="width: 15%;">{{ __('accident_informs.before_image') }}</th>
                                <th style="width: 25%;" colspan="2">
                                    {{ __('accident_informs.repair_characteristics') }}</th>
                                <th style="width: 25%;" colspan="2">
                                    {{ __('accident_informs.wound_characteristics') }}</th>
                                <th style="width: 25%;" colspan="2">{{ __('accident_informs.garage') }}
                                </th>
                                <th style="width: 25%;" colspan="2">{{ __('accident_orders.send_repair_date') }}
                                </th>
                                <th style="width: 25%;" colspan="2">{{ __('accident_orders.due_date') }}
                                </th>
                            </tr>
                        </thead>
                        <tbody v-if="item.line.length > 0" class="tr-block">
                            <template v-for="(acc_line, index2) in item.line">
                                <tr>
                                    <td>
                                        <template v-if="acc_line.before_files && acc_line.before_files.url">
                                            <div class="image-container">
                                                <img class="img-block img-fluid img-car"
                                                    :src="acc_line.before_files.url" alt=""
                                                    style="width:200px; height:200px">
                                                <i class="fa fa-eye overlay-icon test" aria-hidden="true"
                                                    @click="openModalImage(acc_line.before_files.url)"></i>
                                            </div>
                                        </template>

                                        <div class="modal fade" id="imageModalOpen" tabindex="-1"
                                            aria-labelledby="modal-cost" aria-hidden="false">
                                            <div
                                                class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                            aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <img class="img-fluid modal-image" :src="selectedImageUrl"
                                                            alt="" style="width:100%; height:100%">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td colspan="2">@{{ acc_line.accident_claim }}</td>
                                    <td colspan="2">@{{ acc_line.wound_characteristics }}</td>
                                    <td colspan="2">@{{ acc_line.garage.text }}</td>
                                    <td colspan="2">@{{ acc_line.send_repair_date }}</td>
                                    <td colspan="2">@{{ acc_line.due_date }}</td>
                                </tr>

                                <input type="hidden" v-bind:name="'order['+ index+ ']['+ index2+ '][id]'"
                                    v-bind:value="acc_line.id">


                            </template>
                            <input type="hidden" v-bind:name="'order['+ index+ '][send_repair_date]'"
                                v-bind:value="item.send_repair_date">
                            <input type="hidden" v-bind:name="'order['+ index+ '][due_date]'"
                                v-bind:value="item.due_date">
                            <input type="hidden" v-bind:name="'order['+ index+ '][garage_id]'"
                                v-bind:value="item.garage_id">
                            <input type="hidden" v-bind:name="'order['+ index+ '][accident_id]'"
                                v-bind:value="item.report_id">
                        </tbody>
                        <tbody v-else>
                            <tr class="table-empty">
                                <td class="text-center" colspan="8">“
                                    {{ __('lang.no_list') . __('accident_informs.repair') }} “</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </template>
            <template v-else>
                <div class="table-wrap db-scroll">
                    <table class="table table-striped table-vcenter">
                        <thead class="bg-body-dark">
                            <tr class="tr-block">
                                <th style="width: 15%;">{{ __('accident_informs.before_image') }}</th>
                                <th style="width: 25%;" colspan="2">
                                    {{ __('accident_informs.repair_characteristics') }}</th>
                                <th style="width: 25%;" colspan="2">
                                    {{ __('accident_informs.wound_characteristics') }}</th>
                                <th style="width: 25%;" colspan="2">{{ __('accident_informs.garage') }}
                                </th>
                                <th style="width: 25%;" colspan="2">{{ __('accident_orders.send_repair_date') }}
                                </th>
                                <th style="width: 25%;" colspan="2">{{ __('accident_orders.due_date') }}
                                </th>
                            </tr>
                        </thead>

                        <tbody>
                            <tr class="table-empty">
                                <td class="text-center" colspan="10">“
                                    {{ __('lang.no_list') . __('accident_orders.accident_open') }} “</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </template>
            @include('admin.accident-orders.modals.repair-accident-modal')
        </div>
    </div>
</div>

@push('scripts')
@endpush
