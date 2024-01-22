@section('block_options_notice')
    <div class="block-options-item">
        @if (Route::is('*.edit') || Route::is('*.create'))
            @if ($can_edit_notice)
                <button class="btn btn-primary btn-custom-size" type="button" onclick="openNoticeModal()">
                    <i class="fa fa-plus-circle me-1"></i> {{ __('compensations.add_notice') }}
                </button>
            @endif
        @endif
    </div>
@endsection

@include('admin.compensations.modals.notice-modal')
<div class="block {{ __('block.styles') }}">
    @include('admin.components.block-header', [
        'text' => __('compensations.notice_data'),
        'block_icon_class' => 'icon-document',
        'block_option_id' => '_notice',
    ])
    <div class="block-content">
        <div id="notice-list" v-cloak data-detail-uri="" data-title="">
            <div class="table-wrap mb-4">
                <table class="table table-striped">
                    <thead class="bg-body-dark">
                        <th style="width: 1px;">#</th>
                        <th style="width: 20%">{{ __('compensations.notice_delivery_date') }}</th>
                        <th style="width: 15%">{{ __('compensations.rp_no') }}</th>
                        {{-- <th style="width: 15%">{{ __('compensations.notive_date') }}</th> --}}
                        <th style="width: 20%">{{ __('compensations.notice_receive_date') }}</th>
                        <th style="width: 20%">{{ __('compensations.recipient_name') }}</th>
                        <th style="width: 10%" class="sticky-col text-center"></th>
                    </thead>
                    <tbody v-if="notice_list.length > 0">
                        <tr v-for="(item, index) in notice_list">
                            <td>@{{ index + 1 }}</td>
                            <td>@{{ formatDate(item.delivery_date) }}</td>
                            <td>@{{ item.rp_no }}</td>
                            <td>@{{ formatDate(item.receive_date) }}</td>
                            <td>@{{ item.recipient_name }}</td>
                            <td class="sticky-col text-center">
                                @includeWhen($can_edit_notice, 'admin.components.dropdown-action-vue')
                            </td>
                            <input type="hidden" v-bind:name="'notices['+ index +'][id]'" id="notice_id" v-bind:value="item.id">
                            <input type="hidden" v-bind:name="'notices['+ index +'][delivery_date]'" id="notice_delivery_date" v-bind:value="item.delivery_date">
                            <input type="hidden" v-bind:name="'notices['+ index +'][rp_no]'" id="notice_rp_no" v-bind:value="item.rp_no">
                            <input type="hidden" v-bind:name="'notices['+ index +'][receive_date]'" id="notice_receive_date" v-bind:value="item.receive_date">
                            <input type="hidden" v-bind:name="'notices['+ index +'][recipient_name]'" id="notice_recipient_name" v-bind:value="item.recipient_name">
                        </tr>
                    </tbody>
                    <tbody v-else>
                        <tr class="table-empty">
                            <td class="text-center" colspan="7">" {{ __('lang.no_list') }} "</td>
                        </tr>
                    </tbody>
                </table>
                <div v-if="pending_delete_notice_ids.length > 0">
                    <template v-for="item in pending_delete_notice_ids"> 
                        <input type="hidden" v-bind:name="'delete_notice_ids[]'" v-bind:value="item">
                    </template>
                </div>
            </div>
        </div>
    </div>
</div>