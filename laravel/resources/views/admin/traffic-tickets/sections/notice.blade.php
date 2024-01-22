<x-blocks.block :title="__('traffic_tickets.notice')" id="notice_block">
    <div class="row push">
        <div class="col-sm-3">
            <x-forms.date-input id="deadline_date" :value="$d->deadline_date" :label="__('traffic_tickets.deadline_date')" :optionals="['required' => true]" />
        </div>
        <div class="col-sm-3">
            <x-forms.select-option id="status_send_po" :value="$d->status_send_po" :list="$send_po_status_list" :label="__('traffic_tickets.status_send_po')"
                :optionals="['required' => true]" />
        </div>
        <div class="col-sm-3">
            <x-forms.date-input id="send_po_date" :value="$d->send_po_date" :label="__('traffic_tickets.send_po_date')" :optionals="['required' => true]" />
        </div>
        <div class="col-sm-3">
            <x-forms.input-new-line id="police_fee" :value="$d->police_fee" :label="__('traffic_tickets.notice_fee')" :optionals="['input_class' => 'number-format', 'required' => true]" />
        </div>
    </div>
    <div class="row push">
        <div class="col-sm-3">
            <x-forms.radio-inline id="is_respond" :value="$d->is_respond" :list="$yes_no_list" :label="__('traffic_tickets.is_respond')"
                :optionals="['required' => true]" />
        </div>
        <div class="col-sm-3">
            <x-forms.date-input id="respond_date" :value="$d->respond_date" :label="__('traffic_tickets.respond_date')" />
        </div>
        <div class="col-sm-3">
            <x-forms.date-input id="expiration_date" :value="$d->expiration_date" :label="__('traffic_tickets.expiration_date')" />
        </div>
    </div>
</x-blocks.block>
