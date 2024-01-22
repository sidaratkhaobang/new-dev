@section('block_options_status')
    <div class="block-options-item">
        @if (Route::is('*.edit') || Route::is('*.create'))
            <button class="btn btn-primary btn-custom-size" type="button" onclick="openStatusModal()">
                <i class="fa fa-plus-circle me-1"></i> {{ __('litigations.add_status') }}
            </button>
        @endif
    </div>
@endsection

@include('admin.litigations.modals.status-modal')
<div class="block {{ __('block.styles') }}">
    @include('admin.components.block-header', [
        'text' => __('litigations.status_data'),
        'block_icon_class' => 'icon-document',
        'block_option_id' => '_status',
    ])
    <div class="block-content">
        <div id="status-list" v-cloak data-detail-uri="" data-title="">
            <div class="table-wrap mb-4">
                <table class="table table-striped">
                    <thead class="bg-body-dark">
                        <th style="width: 1px;">#</th>
                        <th style="width: 20%">{{ __('litigations.save_date') }}</th>
                        <th style="width: 40%">{{ __('litigations.description') }}</th>
                        <th style="width: 15%">{{ __('litigations.appointment_date') }}</th>
                        <th style="width: 15%">{{ __('lang.status') }}</th>
                        <th style="width: 10%" class="sticky-col text-center"></th>
                    </thead>
                    <tbody v-if="status_list.length > 0">
                        <tr v-for="(item, index) in status_list">
                            <td>@{{ index + 1 }}</td>
                            <td>@{{ item.date }}</td>
                            <td>@{{ item.description }}</td>
                            <td>@{{ formatDate(item.appointment_date) }}</td>
                            <td>@{{ item.status_text }}</td>
                            <td v-if="editable(item)" class="sticky-col text-center">
                                @include('admin.components.dropdown-action-vue')
                            </td>
                            <td v-else></td>
                            <input type="hidden" v-bind:name="'statuses['+ index +'][id]'" id="status_id" v-bind:value="item.id">
                            <input type="hidden" v-bind:name="'statuses['+ index +'][date]'" id="status_date" v-bind:value="item.date">
                            <input type="hidden" v-bind:name="'statuses['+ index +'][description]'" id="status_description" v-bind:value="item.description">
                            <input type="hidden" v-bind:name="'statuses['+ index +'][appointment_date]'" id="status_appointment_date" v-bind:value="item.appointment_date">
                            <input type="hidden" v-bind:name="'statuses['+ index +'][status]'" id="status_status" v-bind:value="item.status">
                        </tr>
                    </tbody>
                    <tbody v-else>
                        <tr class="table-empty">
                            <td class="text-center" colspan="6">" {{ __('lang.no_list') }} "</td>
                        </tr>
                    </tbody>
                </table>
                <div v-if="pending_delete_status_ids.length > 0">
                    <template v-for="item in pending_delete_status_ids"> 
                        <input type="hidden" v-bind:name="'delete_status_ids[]'" v-bind:value="item">
                    </template>
                </div>
            </div>
        </div>
    </div>
</div>