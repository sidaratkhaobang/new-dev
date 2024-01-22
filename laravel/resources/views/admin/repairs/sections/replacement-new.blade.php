@section('block_options_replacement')
    <div class="block-options">
        <div class="block-options-item">
            @if ($mode != 'MODE_VIEW')
            @can(Actions::Manage . '_' . Resources::ReplacementCar)
                <button type="button" class="btn btn-primary" onclick="openReplacementModal()">
                    <i class="icon-add-circle me-1"></i>
                    {{ __('repairs.replacement_modal_header') }}
                </button>
            @endcan
            @endif
        </div>
    </div>
@endsection
@include('admin.repairs.modals.replacement-modal')
<div class="block {{ __('block.styles') }}" id="replacment_section" v-cloak>
    @include('admin.components.block-header', [
        'text' => __('repairs.replacement_table'),
        'block_icon_class' => 'icon-document',
        'block_option_id' => '_replacement',
    ])
    <div class="block-content">
        <div class="table-wrap">
            <table class="table table-striped">
                <thead class="bg-body-dark">
                    <th>{{ __('repairs.round') }}</th>
                    <th>{{ __('repairs.type_job') }}</th>
                    <th>{{ __('repairs.send_and_pickup_date') }}</th>
                    <th>{{ __('repairs.main_license_plate') }}</th>
                    <th>{{ __('repairs.replacement_license_plate') }}</th>
                    <th>{{ __('repairs.send_pickup_method') }}</th>
                    <th>{{ __('repairs.send_pickup_place') }}</th>
                    <th>{{ __('repairs.replacement_worksheet') }}</th>
                    @if (strcmp($mode, MODE_VIEW) !== 0)
                        <th class="sticky-col text-center">{{ __('lang.tools') }}</th>
                    @endif
                </thead>
                <tbody v-if="replacement_list.length > 0">
                    <template v-for="(item, index) in replacement_list">
                        <tr>
                            <td>@{{ index + 1 }}</td>
                            <td>@{{ item.job_type_text }}</td>
                            <td>@{{ formatDate(item.send_pickup_date) }}</td>
                            <td>@{{ item.main_license_plate }}</td>
                            <td>@{{ item.replacement_license_plate }}</td>
                            <td>
                                <p v-if="item.is_pickup_at_tls">ลูกค้ารับ/ส่งเอง</p>
                                <p v-else>
                                    @{{ item.slide_id ? 'รถสไลด์: ' + item.slide_text : 'พนักงานขับรถ' }}
                                </p>
                            <td>@{{ item.send_pickup_place }}</td>
                            <td>
                                <button v-if="item.id" class="btn btn-sm btn-primary" @click="openReplacementTab(item.id)">
                                    <i class="fa fa-link me-1"></i>@{{ item.worksheet_no }}
                                </button>
                            </td>
                            @if (strcmp($mode, MODE_VIEW) !== 0)
                                <td class="sticky-col text-center">
                                    <div v-if="!item.id">
                                        @include('admin.components.dropdown-action-vue')
                                    </div>
                                </td>
                            @endif
                        </tr>
                        <input type="hidden" v-bind:name="'replacements['+ index +'][id]'" v-bind:value="item.id">
                        <input type="hidden" v-bind:name="'replacements['+ index +'][job_type_id]'" id="_job_type_id" v-bind:value="item.job_type_id">
                        <input type="hidden" v-bind:name="'replacements['+ index +'][send_pickup_date]'"id="_end_pickup_date" v-bind:value="item.send_pickup_date">
                        <input type="hidden" v-bind:name="'replacements['+ index +'][main_car_id]'"id="_main_car_id" v-bind:value="item.main_car_id">
                        <input type="hidden" v-bind:name="'replacements['+ index +'][is_at_tls]'"id="_is_at_tls" v-bind:value="item.is_at_tls">
                        <input type="hidden" v-bind:name="'replacements['+ index +'][slide_id]'"id="_slide_id" v-bind:value="item.slide_id">
                        <input type="hidden" v-bind:name="'replacements['+ index +'][send_pickup_place]'"id="_send_pickup_place" v-bind:value="item.send_pickup_place">
                    </template>
                </tbody>
                <tbody v-else>
                    <tr class="table-empty">
                        <td class="text-center" colspan="9">"
                            {{ __('lang.no_list') . __('repairs.replacement_table') }} "</td>
                    </tr>
                </tbody>
                {{-- <template v-for="(input,k) in pending_check_repair_ids">
                    <input type="hidden" v-bind:name="'del_check_repair[]'" id="del_input_id" v-bind:value="input">
                </template> --}}
            </table>
        </div>
    </div>
</div>
