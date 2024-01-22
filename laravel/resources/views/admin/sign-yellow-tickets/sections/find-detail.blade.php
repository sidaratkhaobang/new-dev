<div class="block {{ __('block.styles') }}">

    @include('admin.components.block-header', [
        'text' => __('sign_yellow_tickets.find_detail'),
        'unique_identifier' => '_list',
        'is_toggle' => true,
    ])
    <div class="block-content">
        <div class="row mb-4">
            <div class="col-sm-3">
                <x-forms.date-input id="receive_find_date" :value="$d->receive_document_date" :label="__('sign_yellow_tickets.receive_find_date')" :optionals="['required' => true]" />
            </div>
        </div>
    </div>
</div>
