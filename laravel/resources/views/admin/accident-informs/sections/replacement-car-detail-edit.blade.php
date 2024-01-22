<div class="block {{ __('block.styles') }}">
    @include('admin.components.block-header', [
        'text' => __('accident_informs.replacement_car_detail'),
    ])
    <div class="block-content">
        <div id="replacement-inform" v-cloak data-detail-uri="" data-title="">
            <div class="table-wrap">
            <table class="table table-striped">
                    <thead class="bg-body-dark">
                        <th style="width: 2px;">ครั้งที่</th>
                        <th>{{ __('accident_informs.replacement_type') }}</th>
                        <th>{{ __('accident_informs.replacement_date') }}</th>
                        <th>{{ __('accident_informs.license_plate_main') }}</th>
                        <th>{{ __('accident_informs.license_plate_replacement') }}</th>
                        <th>{{ __('accident_informs.pickup_way') }}</th>
                        <th>{{ __('accident_informs.pickup_place') }}</th>
                        <th>{{ __('accident_informs.replacement_file') }}</th>
                        @if (!isset($view))
                            <th class="sticky-col text-center"></th>
                        @endif
                    </thead>
                    <tbody v-if="replacement_list.length > 0">
                        <tr v-for="(item, index) in replacement_list">
                            <td>@{{ index + 1 }}</td>
                            <td>@{{item.replacement_type_text}}</td>
                            <td>@{{ item.replacement_pickup_date ? format_date(item.replacement_pickup_date) : '' }}</td>
                            {{-- <td></td> --}}
                            <td>@{{ item.main_car }}</td>
                            <td></td>
                          <td>@{{ item.customer_receive_text }}</td>
                            <td>@{{ item.place }}</td>
                            {{-- <td>@{{ getNumberWithCommas(item.lift_price) }}</td> --}}
                            <td><a :href="item.replacement_url" target="_blank"> @{{ item.worksheet }} </a></td> 
                            {{-- <td>
                                <div v-if="getFilesPendingCount(item.replacement_files) > 0">
                                    <p class="m-0">{{ __('drivers.pending_file') }} : @{{ getFilesPendingCount(item.replacement_files) }}
                                        {{ __('lang.file') }}</p>
                                </div>
                                <div v-if="item.replacement_files">
                                    <div v-for="(replacement_file, index) in item.replacement_files">
                                        <div v-if="replacement_file.saved">
                                            <a target="_blank" v-bind:href="replacement_file.url"><i
                                                    class="fa fa-download text-primary"></i>
                                                @{{ replacement_file.name }}</a>
                                        </div>
                                    </div>
                                </div>
                            </td> --}}
                            @if (!isset($view))
                                <td class="sticky-col text-center">
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
                                                    <a class="dropdown-item" v-on:click="editAccident(index)"><i
                                                            class="far fa-edit me-1"></i> แก้ไข</a>
                                                    <a class="dropdown-item btn-delete-row"
                                                        v-on:click="removeAccident(index)"><i
                                                            class="fa fa-trash-alt me-1"></i> ลบ</a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            @endif
                            <input type="hidden" v-bind:name="'slide['+ index+ '][id]'" v-bind:value="item.id">
                            <input type="hidden" v-bind:name="'slide['+ index+ '][lift_date]'"
                                v-bind:value="item.lift_date">
                            <input type="hidden" v-bind:name="'slide['+ index+ '][slide_driver]'"
                                v-bind:value="item.slide_driver">
                            <input type="hidden" v-bind:name="'slide['+ index+ '][lift_from]'"
                                v-bind:value="item.lift_from">
                            <input type="hidden" v-bind:name="'slide['+ index+ '][lift_to]'"
                                v-bind:value="item.lift_to">
                            <input type="hidden" v-bind:name="'slide['+ index+ '][lift_price]'"
                                v-bind:value="item.lift_price">
                        </tr>
                    </tbody>
                    <tbody v-else>
                        <tr class="table-empty">
                            <td class="text-center" colspan="9">“
                                {{ __('lang.no_list') . __('accident_informs.folklift_detail') }} “</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            @if (!isset($view))
                <div class="row">
                    <div class="col-md-12 text-end">
                        <button type="button" class="btn btn-primary" onclick="addReplacement()"
                            id="openModal">{{ __('lang.add') }}</button>
                    </div>
                </div>
            @endif
        </div>
        <br>
    </div>
</div>
@include('admin.accident-informs.modals.replacement-modal')
