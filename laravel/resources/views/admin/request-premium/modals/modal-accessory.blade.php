<div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel"> <i class="fa fa-file-lines ms-2 me-2"></i>ข้อมูลอุปกรณ์เสริม</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-4">
                    <div class="table-wrap">
                        <table id="table-accessory-car" class="table table-striped">
                            <thead class="bg-body-dark">
                            <th>อุปกรณ์เสริม</th>
                            <th>จำนวน</th>
                            <th>ราคา</th>
                            <th>หมายเหตุ</th>
                            </thead>
                            <tbody v-if="data_list.length > 0" id="table-accessory-data">
                            <tr v-for="(item, index) in data_list">
                                <td>
                                    @{{ item.name || '-'}}
                                </td>
                                <td>
                                    @{{ item.amount || '-' }}
                                </td>
                                <td>
                                    @{{ item.price || '-' }}
                                </td>
                                <td>
                                    @{{ item.remark || '-' }}
                                </td>
                            </tr>

                            </tbody>
                            <tbody v-else>
                            <tr>
                                <td class="text-center" colspan="12">"  ไม่มีรายการข้อมูลอุปกรณ์เสริม "</td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-clear-search"
                        data-bs-dismiss="modal">{{ __('lang.back') }}</button>
            </div>
        </div>
    </div>
</div>
