<div class="block {{ __('block.styles') }}">
    @include('admin.components.block-header', [
        'text' => 'การเปิดงานเช่า',
        'block_icon_class' => 'icon-document',
    ])
    <div class="block-content">
        <x-forms.radio-inline id="order_channel" :value="$d->order_channel" :list="$order_channel_list ?? []" :label="'ช่องทางการจอง'"/>
    </div>
</div>