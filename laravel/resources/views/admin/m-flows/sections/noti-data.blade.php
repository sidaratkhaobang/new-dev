<x-blocks.block :title="__('m_flows.notifine_data')">
    <div class="row push">
        <div class="col-sm-3">
            <x-forms.date-input id="notification_date" :value="$d->notification_date" :label="__('m_flows.notification_date')" />
        </div>
        <div class="col-sm-9">
            <x-forms.input-new-line id="remark" :value="$d->remark" :label="__('m_flows.remark')" />
        </div>
    </div>
</x-blocks.block>
