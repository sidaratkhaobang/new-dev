<div class="block {{ __('block.styles') }} withdraw-block">
    @include('admin.components.block-header', [
        'text' => __('compensations.withdraw_detail'),
    ])
    <div class="block-content">
        <div class="row push">
            <div class="col-sm-3">
                <x-forms.input-new-line id="recipient_document" :value="$d->recipient_document" :label="__('compensations.recipient_document')" 
                    :optionals="['required' => true]"/>
            </div>
            <div class="col-sm-3">
                <x-forms.select-option id="sending_channel" :value="$d->sending_channel" :list="$sending_channel_list" :label="__('compensations.sending_channel')" 
                :optionals="['required' => true]" />
            </div>
            <div class="col-sm-6">
                <x-forms.input-new-line id="sending_channel_detail" :value="$d->sending_channel_detail" :label="__('compensations.sending_channel_detail')"  />
            </div>
        </div>
        <div class="row push">
            <div class="col-sm-3">
                <x-forms.date-input id="receive_date" :value="$d->receive_date" :label="__('compensations.receive_date')" />
            </div>
            <div class="col-sm-3">
                <x-forms.upload-image :id="'receive_files'" :label="__('compensations.receive_files')" :optionals="['required' => true]" />
            </div>
        </div>
    </div>
</div>
