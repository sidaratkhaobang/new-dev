<div class="block {{ __('block.styles') }}">
    @section('block_options_1')
        <div class="block-options">
        </div>
    @endsection
    @include('admin.components.block-header', [
        'text' => __('accident_orders.accident_detail_table'),
        'block_option_id' => '_1',
    ])
    <div class="block-content">
        <div class="row">
        </div>
        <div id="repair-vue" v-cloak data-detail-uri="" data-title="">
            <div class="table-wrap">
                <table class="table table-striped">
                    <thead class="bg-body-dark">
                        <tr>
                            <th class="th-topic" style="border-top-left-radius: 7px;">
                                <div class="vl vl-topic">
                                    @if ($accident_inform->repair_type == RepairClaimEnum::HARD_BUMP)
                                        <span class="badge badge-custom badge-bg-primary">
                                            <i class="icon-menu-accident me-1"></i> ชนหนัก
                                        </span>
                                    @elseif($accident_inform->repair_type == RepairClaimEnum::SOFT_BUMP)
                                        <span class="badge badge-custom badge-bg-success">
                                            <i class="icon-menu-replace-car me-1"></i> ชนเบา
                                        </span>
                                    @elseif($accident_inform->repair_type == RepairClaimEnum::TTL)
                                        <span class="badge badge-custom badge-bg-danger">
                                            <i class="icon-menu-document-normal me-1"></i> TTL
                                        </span>
                                    @endif
                                </div>
                            </th>
                            <th class="th-topic">
                                <div class="vl vl-topic">
                                    เลขที่ใบแจ้งอุบัติเหตุ <br> {{ $accident_inform->worksheet_no }}
                                </div>
                            </th>
                            <th class="th-topic">

                                <div class="vl vl-topic">
                                    เลขที่ใบเคลม <br> {{ $accident_inform->claim_no }}
                                </div>
                            </th>

                            <th class="th-topic">

                                <div class="vl vl-topic">
                                    วันที่เกิดเหตุ <br> {{ $accident_inform->accident_date }}
                                </div>
                            </th>

                            <th class="th-topic" style="width:100px;">

                                <div class="vl vl-topic">
                                    เคส <br> {{ $accident_inform->case }}
                                </div>
                            </th>
                            <th class="th-topic">

                                <div class="vl vl-topic">
                                    ลักษณะเกิดเหตุ <br> {{ $accident_inform->accident_description }}
                                </div>
                            </th>
                            <th class="th-topic">
                                <div>
                                    จำนวนเคลม <br> {{ $accident_inform->count_accident_line }}
                                </div>
                            </th>
                            <th class="th-topic text-center toggle-table" style="border-top-right-radius: 7px;">
                                <i class="icon-arrow-up text-muted"></i>
                            </th>
                        </tr>
                        <tr class="tr-block">
                            <th>{{ __('accident_informs.before_image') }}</th>
                            <th>{{ __('accident_informs.after_image') }}</th>
                            <th colspan="2">{{ __('accident_informs.repair_list') }}</th>
                            <th colspan="2">{{ __('accident_informs.wound_characteristics') }}</th>
                            <th colspan="2">{{ __('accident_informs.spare_parts_supplier') }}</th>
                        </tr>
                    </thead>
                    <tbody v-if="repair_list.length > 0" class="tr-block">
                        <tr v-for="(item, index) in repair_list">
                            <td>
                                <template v-if="item.before_files.length > 0">
                                    <div class="image-container">
                                        <img class="img-block img-fluid img-car" :src="item.before_files[0].url"
                                            alt="" style="width:200px; height:200px">
                                        <i class="fa fa-eye overlay-icon" aria-hidden="true"
                                            @click="openModalImage(item.before_files[0].url)"></i>
                                    </div>
                                </template>

                                <div class="modal fade" id="imageModal" tabindex="-1" aria-labelledby="modal-cost"
                                    aria-hidden="false">
                                    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
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
                                <template v-if="item.after_files.length > 0">
                                    <div class="image-container">
                                        <img class="img-block img-fluid img-car" :src="item.after_files[0].url"
                                            alt="" style="width:200px; height:200px">
                                        <i class="fa fa-eye overlay-icon" aria-hidden="true"
                                            @click="openModalImage(item.after_files[0].url)"></i>
                                    </div>
                                </template>

                                <div class="modal fade" id="imageModal" tabindex="-1" aria-labelledby="modal-cost"
                                    aria-hidden="false">
                                    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
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
                            <td class="align-middle" colspan="2">@{{ item.accident_claim_text }}</td>
                            <td class="align-middle" colspan="2">
                                @{{ item.wound_characteristics_text }}
                            </td>
                            <td class="align-middle" colspan="2">@{{ item.supplier_text }}</td>
                            <input type="hidden" v-bind:name="'repair['+ index+ '][id]'" v-bind:value="item.id">
                            <input type="hidden" v-bind:name="'repair['+ index+ '][tls_cost]'"
                                v-bind:value="item.tls_cost">
                            <input type="hidden" v-bind:name="'repair['+ index+ '][supplier]'"
                                v-bind:value="item.supplier">
                            <input type="hidden" v-bind:name="'repair['+ index+ '][is_withdraw_true]'"
                                v-bind:value="item.is_withdraw_true">
                            <input type="hidden" v-bind:name="'repair['+ index+ '][accident_claim_id]'"
                                v-bind:value="item.accident_claim_id">
                            <input type="hidden" v-bind:name="'repair['+ index+ '][supplier_id]'"
                                v-bind:value="item.supplier_id">
                            <input type="hidden" v-bind:name="'repair['+ index+ '][wound_characteristics_id]'"
                                v-bind:value="item.wound_characteristics_id">
                        </tr>
                    </tbody>
                    <tbody v-else>
                        <tr class="table-empty">
                            <td class="text-center" colspan="8">“
                                {{ __('lang.no_list') . __('accident_informs.repair') }} “</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <br>
    </div>
</div>
@include('admin.accident-informs.modals.repair-modal')
