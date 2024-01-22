<x-blocks.block :title="__('traffic_tickets.fine_data')" id="fine_block">
    <div class="row push">
        <div class="col-sm-3">
            <x-forms.date-input id="notification_date" :value="$d->notification_date" :label="__('traffic_tickets.notification_date')" :optionals="['required' => true]" />
        </div>
        <div class="col-sm-3">
            <x-forms.select-option id="notice_channel" :value="$d->notice_channel" :list="$notice_channel_list" :label="__('traffic_tickets.notice_channel')"
                :optionals="['required' => true]" />
        </div>
        <div class="col-sm-3">
            <x-forms.input-new-line id="notice_fee" :value="$d->notice_fee" :label="__('traffic_tickets.notice_fee')" :optionals="['input_class' => 'number-format', 'required' => true]" />
        </div>
        <div class="col-sm-3">
            <x-forms.date-input id="due_date" :value="$d->due_date" :label="__('traffic_tickets.due_date')" />
        </div>
    </div>
</x-blocks.block>
