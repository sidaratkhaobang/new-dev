<div class="block {{ __('block.styles') }}">
    @section('block_options_1')
        <div class="block-options">
            <div class="block-options-item">
                @if (!isset($view))
                <div class="col-md-8 text-end">
                    <button type="button" class="btn btn-primary add-repair" onclick="addRepair()"
                        id="openModal">{{ __('lang.add') }}</button>
                </div>
            @endif
            </div>
        </div>
    @endsection
     @include('admin.components.block-header', [
        'text' => __('accident_informs.repair_table'),
        'block_option_id' => '_1',
    ])
    <div class="block-content">
        <div class="row">
        </div>
        <div id="repair-vue" v-cloak data-detail-uri="" data-title="">
            <div class="table-wrap">
                <table class="table table-striped">
                    <thead class="bg-body-dark">
                        <th>{{ __('accident_informs.before_image') }}</th>
                        {{-- <th>{{ __('accident_informs.after_image') }}</th> --}}
                        <th>{{ __('accident_informs.repair_list') }}</th>
                        <th>{{ __('accident_informs.wound_characteristics') }}</th>
                        <th>{{ __('accident_informs.spare_parts_supplier') }}</th>
                        @if (!isset($view))
                            <th class="sticky-col text-center">{{ __('lang.tools') }}</th>
                        @endif
                    </thead>
                    <tbody v-if="repair_list.length > 0">
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
                            {{-- <td>
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
                            </td> --}}
                            <td class="align-middle">@{{ item.accident_claim_text }}</td>
                            <td class="align-middle">@{{ item.wound_characteristics_text }}</td>
                            <td class="align-middle">@{{ item.supplier_text }}</td>
                            @if (!isset($view))
                                <td class="sticky-col text-center align-middle">
                                    <div class="btn-group">
                                        <div class="col-sm-12">
                                            <div class="dropdown dropleft">
                                                <button type="button"
                                                    class="btn btn-sm btn-alt-secondary dropdown-toggle"
                                                    id="dropdown-dropleft-dark" data-bs-toggle="dropdown"
                                                    aria-haspopup="true" aria-expanded="false">
                                                    <i class="fa fa-ellipsis-vertical"></i>
                                                </button>
                                                <div class="dropdown-menu" aria-labelledby="dropdown-dropleft-dark">
                                                    <a class="dropdown-item" v-on:click="editRepair(index)"><i
                                                            class="far fa-edit me-1"></i> แก้ไข</a>
                                                    <a class="dropdown-item btn-delete-row"
                                                        v-on:click="removeRepair(index)"><i
                                                            class="fa fa-trash-alt me-1"></i> ลบ</a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            @endif
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
