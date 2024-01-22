<div id="page-2-table-file-upload" v-cloak data-detail-uri="" data-title="">
    <div class="table-wrap mt-4 mb-4">
        <table class="table table-striped">
            <thead class="bg-body-dark">
            <th>{{ __('ลำดับ') }}</th>
            <th>{{ __('contract.contract_side') }}</th>
            <th>{{ __('ประเภทผู้เซ็น') }}</th>
            <th>{{ __('ชื่อผู้เซ็น') }}</th>
            <th class="text-center">{{ __('contract.is_attorney') }}</th>
            <th>{{ __('หนังสือมอบอำนาจ') }}</th>
            @if (isset($view))
                <th class="sticky-col text-center"></th>
            @endif
            </thead>
            <tbody v-if="data_list.length > 0">
            <tr v-for="(item, index) in data_list">
                <td>@{{ index + 1 }}</td>
                <td>
                    <p v-if="item.user_sign.contract_side === 'HOST'">{{ __('contract.singer_' . ContractSignerSideEnum::HOST) }}</p>
                    <p v-else-if="item.user_sign.contract_side === 'RENTER'">{{ __('contract.singer_' . ContractSignerSideEnum::RENTER) }}</p>
                    <p v-else>-</p>
                </td>
                <td>@{{ item.user_sign.signer_type }}</td>
                <td>@{{ item.user_sign.name }}</td>
                <td class="text-center">
                    <i v-bind:class="(item.user_sign.is_attorney) ? 'fa fa-circle-check text-primary' : 'fa fa-circle-xmark text-secondary'" ></i>
                </td>
                <td>
                    <div v-if="getFilesPendingCount(item.files) > 0">
                        <p class="m-0">{{ __('drivers.pending_file') }} : @{{ getFilesPendingCount(item.files)
                            }}{{ __('lang.file') }}</p>
                    </div>

                    <div v-if="item.files">
                        <div v-for="(file, index) in item.files">
                            <div v-if="file.saved">
                                <a target="_blank" v-bind:href="file.url"><i class="fa fa-download text-primary"></i>@{{
                                    file.name }}</a>
                            </div>
                        </div>
                    </div>
                </td>
                @if (isset($view))
                    <td class="sticky-col text-center">
                        <i class="far fa-trash-alt" v-on:click="removeRow(index)" style="cursor: pointer"></i>
                    </td>
                @endif
            </tr>
            </tbody>
            <tbody v-else>
            <tr class="table-empty">
                <td class="text-center" colspan="7">
                    " {{ __('lang.no_list') . __('ข้อมูลผู้เซ็น') }} "
                </td>
            </tr>
            </tbody>
        </table>
    </div>
</div>

@push('scripts')
    <script>
        window.tableFileAttorney = new window.Vue({
            el: '#page-2-table-file-upload',
            data: {
                data_list : @if(isset($arr_contract_signer)) @json($arr_contract_signer) @else [] @endif,
                pending_delete_contract_signers_ids: [],
                // data_list: [
                //     {
                //         user_sign: {
                //             signer_type: 'เจ้าของรถ',
                //             name: 'สมชาย'
                //         },
                //         files: [
                //             {
                //                 media_id: null,
                //                 url: '#',
                //                 url_thumb: '#',
                //                 file_name: 'ชื่อไฟล์ ทดสอบ 1',
                //                 name: 'ชื่อไฟล์ ทดสอบ',
                //                 size: 10,
                //                 mime_type: 'PDF',
                //                 raw_file: null,
                //                 saved: true,
                //                 formated: true,
                //             },
                //             {
                //                 media_id: null,
                //                 url: '#',
                //                 url_thumb: '#',
                //                 file_name: 'ชื่อไฟล์ ทดสอบ 2',
                //                 name: 'ชื่อไฟล์ ทดสอบ',
                //                 size: 10,
                //                 mime_type: 'PDF',
                //                 raw_file: null,
                //                 saved: true,
                //                 formated: true,
                //             },
                //         ],
                //     },
                // ],
            },
            methods: {
                addNewRow: function (files, sign_type, sign_name, sign_side, is_attorney) {
                    this.data_list.push({
                        saved : false,
                        user_sign : {
                            signer_type: sign_type,
                            name: sign_name,
                            contract_side: sign_side,
                            is_attorney: is_attorney,
                        },
                        files : files.map(file => this.formatFile(file)),
                    })
                },
                removeRow: function (index) {
                    const user_sign = this.data_list[index].user_sign;
                    if (user_sign.id) {
                        console.log(user_sign.id)
                        this.pending_delete_contract_signers_ids.push(user_sign.id);
                        console.log(this.pending_delete_contract_signers_ids)
                    }
                    this.data_list.splice(index, 1);
                },
                formatFile: function (file) {
                    if (file.formated) {
                        return file;
                    }
                    return {
                        media_id: null,
                        url: file.dataURL,
                        url_thumb: file.dataURL,
                        file_name: file.name,
                        name: file.name,
                        size: file.size,
                        mime_type: file.type,
                        raw_file: file,
                        saved: false,
                        formated: true,
                    }
                },
                getFilesPendingCount: function (files) {
                    return (files ? files.filter((file) => !file.saved).length : '---');
                },
            },
        });

        $('.btn-add-attorney-file').click(function () {
            const sign_type = $('#sign_type').val();
            const sign_name = $('#sign_name').val();
            const sign_side = $('#sign_side').val();
            const is_attorney = $('#is_attorney_check_0:checked').val() ?? false;

            const objDropzone = window.myDropzone[1]
            const files = objDropzone.getQueuedFiles();
            if (!sign_type || !sign_name) {
                warningAlert('{{__('กรุณากรอกข้อมูลให้ครบท่วน')}}');
            } else {
                window.tableFileAttorney.addNewRow(files, sign_type, sign_name, sign_side, is_attorney);
                $('#sign_type').val('');
                $('#sign_name').val('');
                $('#sign_side').val(null).trigger("change");
                $('#sign_name').val('');
                $("#is_attorney_check_0").prop("checked", false);
                objDropzone.removeAllFiles(true);
            }
        });
    </script>
@endpush

