<div class="row push">
    <div class="col-sm-3">
        <x-forms.date-input id="overdue_date" :value="null" :label="__('m_flows.overdue_date')" :optionals="['required' => true]" />
    </div>
    <div class="col-sm-3">
        <x-forms.date-input id="document_date" :value="null" :label="__('m_flows.document_date')" :optionals="['required' => true]" />
    </div>
</div>
@section('block_options_add')
    @if (empty($view))
        <button type="button" class="btn btn-primary" onclick="addOffenseLine()">เพิ่มข้อมูล</button>
    @endif
@endsection
@include('admin.components.block-header', [
    'text' => __('m_flows.offense_list'),
    'block_header_class' => 'ps-0',
    'block_option_id' => '_add',
])
<div class="mb-3 mt-3" id="offense-line" v-cloak>
    <div class="table-wrap db-scroll">
        <table class="table table-striped table-vcenter">
            <thead class="bg-body-dark">
                <th></th>
                <th>{{ __('m_flows.offense_time') }}</th>
                <th>{{ __('m_flows.station_place') }}</th>
                <th>{{ __('m_flows.fee') }}</th>
                <th>{{ __('m_flows.fine') }}</th>
                <th style="width: 5%;"></th>
            </thead>
            <tbody v-if="offense_list.length > 0">
                <tr v-for="(item,k) in offense_list">
                    <td>@{{ k + 1 }}</td>
                    <td>
                        <div class="input-group">
                            <input-time-vue id="offense_time" v-model="offense_list[k].offense_time"
                                :id="'offense_time' + k" v-bind:name="'offense_list[k][offense_time]'"
                                data-enable-time="true" data-no-calendar="true" data-date-format="H:i"
                                data-time_24hr="true" @input="handle(k)">
                            </input-time-vue>
                            <span class="input-group-text">
                                <i class="far fa-clock"></i>
                            </span>
                            <input type="hidden" v-bind:name="'offense_data['+ k+ '][offense_time]'" id="offense_time"
                                v-model="offense_list[k].offense_time">
                        </div>
                    </td>
                    <td>
                        <select-2-default :id="'expressway_id' + k" class="form-control list" style="width: 100%;"
                            v-model="item.expressway_id" :list="express_way_list" :defaultname="item.expressway_id" />
                        </select-2-default>
                        <input type="hidden" v-bind:name="'offense_data['+ k+ '][expressway_id]'" id="expressway_id"
                            v-model="offense_list[k].expressway_id">
                    </td>
                    <td>
                        <input-number-format-vue v-model="offense_list.fee" :id="'fee-' + k" class="form-control"
                            :value="offense_list.feel" :name="'offense_data[' + k + '][fee]'" />
                    </td>
                    <td>
                        <input-number-format-vue v-model="offense_list.fine" :id="'fine-' + k" class="form-control"
                            :value="offense_list.finel" :name="'offense_data[' + k + '][fine]'" />
                    </td>
                    @if (!isset($view))
                        <td class="sticky-col text-center">
                            <div class="btn-group">
                                <div class="col-sm-12">
                                    <div class="dropdown dropleft">
                                        <button type="button" class="btn btn-sm btn-alt-secondary dropdown-toggle"
                                            id="dropdown-dropleft-dark" data-bs-toggle="dropdown" aria-haspopup="true"
                                            aria-expanded="false">
                                            <i class="fa fa-ellipsis-vertical"></i>
                                        </button>
                                        <div class="dropdown-menu" aria-labelledby="dropdown-dropleft-dark">
                                            <a class="dropdown-item" v-on:click="view(k)">
                                                <i class="far fa-eye me-1"></i> ดูข้อมูลรถ</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </td>
                    @endif
                    <input type="hidden" v-bind:name="'offense_data['+ k+ '][id]'" id="id"
                        v-model="offense_list[k].id">
                    <input type="hidden" v-bind:name="'offense_data['+ k+ '][job_id]'" id="job_id"
                        v-model="offense_list[k].job_id">
                    <input type="hidden" v-bind:name="'offense_data['+ k+ '][job_type]'" id="job_type"
                        v-model="offense_list[k].job_type">
                </tr>
            </tbody>
            <tbody v-else>
                <tr class="table-empty">
                    <td class="text-center" colspan="6">" {{ __('lang.no_list') }} "</td>
                </tr>
            </tbody>
            <template v-for="(input,k) in del_input_id">
                <input type="hidden" v-bind:name="'del_section[]'" id="del_input_id" v-bind:value="input">
            </template>
        </table>
    </div>
</div>
<div class="row push">
    <div class="col-sm-3">
        <x-forms.input-new-line id="maximum_fine" :value="null" :label="__('m_flows.maximum_fine')" :optionals="['input_class' => 'number-format']" />
    </div>
    <div class="col-sm-3">
        <x-forms.radio-inline id="is_payment" :value="$d->is_payment" :list="$payment_list" :label="__('m_flows.is_payment')" />
    </div>
    <div class="col-sm-3">
        <x-forms.upload-image :id="'overdue_file'" :label="__('m_flows.overdue_file')" />
    </div>
</div>
@include('admin.m-flows.modals.car-modal')
