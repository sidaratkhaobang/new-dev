<div class="modal fade" id="modal-driver" data-bs-backdrop="static" tabindex="-1" aria-labelledby="modal-driver" aria-hidden="false">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-popout" style="width:1250px;">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="driver-modal-label">เพิ่มข้อมูล</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" @click="clearModalData" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="d-flex justify-content-end align-items-center">
                    <button type="button" class="btn btn-primary" onclick="addDriver()">{{ __('lang.add') }}</button>
                </div>
                <div id="" class="block-content">
                    <div class="justify-content-between mb-4">
                        <div class="mb-5" v-cloak data-detail-uri="" data-title="">
                            <div class="table-wrap">
                                <table class="table table-striped">
                                    <thead class="bg-body-dark">
                                    <th>{{ __('short_term_rentals.customer_name') }}</th>
                                    <th>{{ __('short_term_rentals.id_card_no') }}</th>
                                    <th>{{ __('short_term_rentals.tel') }}</th>
                                    <th>{{ __('short_term_rentals.driver_license') }}</th>
                                    <th>{{ __('short_term_rentals.id_card') }}</th>
                                    </thead>
                                    <tbody v-if="driver_list_modal.length > 0">
                                    <tr v-for="(item, index) in driver_list_modal">
                                        </td>
                                        <td class="align-middle">
                                            <input type="text" v-model="item.name"
                                                   v-bind:name="'drivers['+ index+ '][name]'" class="form-control"
                                                   maxlength="255">
                                        </td>
                                        <td class="align-middle">
                                            <input type="text" v-model="item.citizen_id"
                                                   v-bind:name="'drivers['+ index+ '][citizen_id]'" class="form-control"
                                                   maxlength="255">
                                        </td>
                                        <td class="align-middle">
                                            <input type="text" v-model="item.tel"
                                                   v-bind:name="'drivers['+ index+ '][tel]'" class="form-control"
                                                   maxlength="255">
                                        </td>
                                        <td>
                                            <div id="driving-license-file"
                                                 class="dropzone file-upload custom-file-image">
                                                <div class="file-select dropzone-area btn-upload"
                                                     :id="'driving-license-file-'+index" :data-id="item.index"
                                                     data-file-type="license_files">
                                                    <div
                                                        class="pe-none w-100 h-100 d-flex justify-content-center align-items-center">
                                                        <i class="icon-document-upload pe-none">

                                                        </i>
                                                        <p class="mb-0 pe-none">
                                                            อัปโหลด
                                                        </p>
                                                    </div>
                                                </div>
                                                <div class="test-previews"
                                                     :id="'driving-license-file-'+index+'-previews'"></div>
                                            </div>
                                        </td>
                                        <td>
                                            <div id="citizen-file" class="dropzone file-upload custom-file-image">
                                                <div class="file-select dropzone-area btn-upload"
                                                     :id="'citizen-file-'+index" :data-id="index"
                                                     data-file-type="citizen_files">
                                                    <div
                                                        class="pe-none w-100 h-100 d-flex justify-content-center align-items-center">
                                                        <i class="icon-document-upload pe-none">

                                                        </i>
                                                        <p class="mb-0 pe-none">
                                                            อัปโหลด
                                                        </p>
                                                    </div>
                                                </div>
                                                <div class="test-previews"
                                                     :id="'citizen-file-'+index+'-previews'"></div>
                                            </div>
                                        </td>
                                        {{--                            <td>@{{ index + 1 }}</td>--}}
                                        {{--                            <td>@{{ item.name }}</td>--}}
                                        {{--                            <td>@{{ item.citizen_id }}</td>--}}
                                        {{--                            <td>@{{ item.tel }}</td>--}}
                                        {{--                            <td>--}}
                                        {{--                                <div v-if="getFilesPendingCount(item.license_files) > 0">--}}
                                        {{--                                    <img :src="item.license_files[0].url" style="max-width: 116px;width:100%;max-height: 65px;height:100%">--}}

                                        {{--                                    --}}{{--                                    <p class="m-0">{{ __('customers.pending_file') }} : @{{ getFilesPendingCount(item.license_files) }}--}}
                                        {{--                                        {{ __('lang.file') }}</p>--}}
                                        {{--                                </div>--}}
                                        {{--                                <div v-if="item.license_files">--}}
                                        {{--                                    <div v-for="(license_file, index) in item.license_files">--}}
                                        {{--                                        <div v-if="license_file.saved">--}}
                                        {{--                                            <a target="_blank" v-bind:href="license_file.url"><i--}}
                                        {{--                                                    class="fa fa-download text-primary"></i>--}}
                                        {{--                                                {{ __('lang.view_file') }}</a>--}}
                                        {{--                                        </div>--}}
                                        {{--                                    </div>--}}
                                        {{--                                </div>--}}
                                        {{--                            </td>--}}
                                        {{--                            <td>--}}
                                        {{--                                <div v-if="getFilesPendingCount(item.citizen_files) > 0">--}}
                                        {{--                                    <img :src="item.citizen_files[0].url" style="max-width: 116px;width:100%;max-height: 65px;height:100%">--}}
                                        {{--                                    <p class="m-0">{{ __('customers.pending_file') }} : @{{  item.citizen_files[0].url }}--}}
                                        {{--                                        {{ __('lang.file') }}</p>--}}
                                        {{--                                </div>--}}
                                        {{--                                <div v-if="item.citizen_files">--}}
                                        {{--                                    <div v-for="(citizen_file, index) in item.citizen_files">--}}
                                        {{--                                        <div v-if="citizen_file.saved">--}}
                                        {{--                                            <a target="_blank" v-bind:href="citizen_file.url"><i--}}
                                        {{--                                                    class="fa fa-download text-primary"></i>--}}
                                        {{--                                                {{ __('lang.view_file') }}</a>--}}
                                        {{--                                        </div>--}}
                                        {{--                                    </div>--}}
                                        {{--                                </div>--}}
                                        {{--                            </td>--}}
                                        {{--                            <input type="text" v-bind:name="'drivers['+ index+ '][name]'" v-bind:value="item.name">--}}

                                        {{--                            <input type="hidden" v-bind:name="'drivers['+ index+ '][citizen_id]'" v-bind:value="item.citizen_id">--}}

                                    </tr>
                                    </tbody>
                                    <tbody v-else>
                                    <tr class="table-empty">
                                        <td class="text-center" colspan="7">" {{ __('lang.no_list') }} "</td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                            {{--                <div class="row">--}}
                            {{--                    <div class="col-md-12 text-end">--}}
                            {{--                    </div>--}}
                            {{--                </div>--}}

                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-clear-search"
                        data-bs-dismiss="modal" @click="clearModalData">{{ __('lang.cancel') }}</button>
                <button type="button" class="btn btn-primary" onclick="saveDriver()">{{ __('lang.save') }}</button>
            </div>
        </div>
    </div>
</div>
