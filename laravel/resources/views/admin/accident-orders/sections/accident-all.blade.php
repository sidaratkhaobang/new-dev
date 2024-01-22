<div class="block {{ __('block.styles') }}" id="accident-all">
    @include('admin.components.block-header', [
        'text' => __('accident_orders.accident_detail'),
    ])
    <div class="block-content">
        <div id="accident-list-vue" v-cloak data-detail-uri="" data-title="">
            <div class="table-wrap db-scroll" v-if="accident_list.length > 0" v-for="(item, index) in accident_list">
                <table class="table table-striped table-vcenter">
                    <thead class="bg-body-dark">
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
                                    เลขที่ใบเคลม <br> @{{ item.claim_no }}
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
                            <th class="th-topic text-center toggle-table" style="border-top-right-radius: 7px;">
                                <i class="icon-arrow-up text-muted"></i>
                            </th>
                        </tr>
                        <tr class="tr-block">
                            <th style="width: 15%;">{{ __('accident_informs.before_image') }}</th>
                            <th style="width: 15%;">{{ __('accident_informs.after_image') }}</th>
                            <th style="width: 25%;" colspan="2">{{ __('accident_informs.repair_characteristics') }}
                            </th>
                            <th style="width: 25%;" colspan="2">{{ __('accident_informs.wound_characteristics') }}
                            </th>
                            <th style="width: 25%;" colspan="2">{{ __('accident_informs.spare_parts_supplier') }}
                            </th>
                        </tr>
                    </thead>
                    <tbody v-if="item.accident_line.length > 0" class="tr-block">
                        <template v-for="acc_line in item.accident_line">
                            <tr>
                                <td>
                                    <template v-if="acc_line.before_files && acc_line.before_files.url">
                                        <div class="image-container">
                                            <img class="img-block img-fluid img-car" :src="acc_line.before_files.url"
                                                alt="" style="width:200px; height:200px">
                                            <i class="fa fa-eye overlay-icon test" aria-hidden="true"
                                                @click="openModalImage(acc_line.before_files.url)"></i>
                                        </div>
                                    </template>

                                    <div class="modal fade" id="imageModal" tabindex="-1" aria-labelledby="modal-cost"
                                        aria-hidden="false">
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
                                <td>
                                    <template v-if="acc_line.after_files && acc_line.after_files.url">
                                        <div class="image-container">
                                            <img class="img-block img-fluid img-car" :src="acc_line.after_files.url"
                                                alt="" style="width:200px; height:200px">
                                            <i class="fa fa-eye overlay-icon test" aria-hidden="true"
                                                @click="openModalImage(acc_line.after_files.url)"></i>
                                        </div>
                                    </template>

                                    <div class="modal fade" id="imageModal" tabindex="-1" aria-labelledby="modal-cost"
                                        aria-hidden="false">
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
                                <td colspan="2">@{{ acc_line.supplier }}</td>
                            </tr>
                        </template>
                    </tbody>
                    <tbody v-else>
                        <tr class="table-empty">
                            <td class="text-center" colspan="8">“
                                {{ __('lang.no_list') . __('accident_informs.repair') }} “</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="table-wrap db-scroll" v-if="accident_list.length == 0">
                <table class="table table-striped table-vcenter">
                    <thead class="bg-body-dark">
                        <tr>
                            <th class="th-topic" style="border-top-left-radius: 7px;">
                                <div class="vl vl-topic">
                                </div>
                            </th>
                            <th class="th-topic">
                                <div class="vl vl-topic">
                                    เลขที่ใบแจ้งอุบัติเหตุ <br> -
                                </div>
                            </th>
                            <th class="th-topic">

                                <div class="vl vl-topic">
                                    เลขที่ใบเคลม <br> -
                                </div>
                            </th>

                            <th class="th-topic">

                                <div class="vl vl-topic">
                                    วันที่เกิดเหตุ <br> -
                                </div>
                            </th>

                            <th class="th-topic" style="width:100px;">

                                <div class="vl vl-topic">
                                    เคส <br> -
                                </div>
                            </th>
                            <th class="th-topic">

                                <div class="vl vl-topic">
                                    ลักษณะเกิดเหตุ <br> -
                                </div>
                            </th>
                            <th class="th-topic">
                                <div>
                                    จำนวนเคลม <br> -
                                </div>
                            </th>
                            <th class="th-topic text-center toggle-table" style="border-top-right-radius: 7px;">
                                <i class="icon-arrow-up text-muted"></i>
                            </th>
                        </tr>
                        <tr class="tr-block">
                            <th style="width: 15%;">{{ __('accident_informs.before_image') }}</th>
                            <th style="width: 15%;">{{ __('accident_informs.after_image') }}</th>
                            <th style="width: 25%;" colspan="2">{{ __('accident_informs.repair_characteristics') }}
                            </th>
                            <th style="width: 25%;" colspan="2">{{ __('accident_informs.wound_characteristics') }}
                            </th>
                            <th style="width: 25%;" colspan="2">{{ __('accident_informs.spare_parts_supplier') }}
                            </th>
                        </tr>
                    </thead>

                    <tbody>
                        <tr class="table-empty">
                            <td class="text-center" colspan="8">“
                                {{ __('lang.no_list') . __('accident_informs.repair') }} “</td>
                        </tr>
                    </tbody>
                </table>
            </div>

        </div>
    </div>
</div>
