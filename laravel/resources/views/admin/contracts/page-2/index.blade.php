@include('admin.contracts.sections.btn-tap-group')
@if(isset($data->contract_type))
<div class="block block-rounded">
    <div class="block-content">
        @include('admin.contracts.page-2.condition-detail')
    </div>
</div>
@endif

<div class="block block-rounded">
    @include('admin.components.block-header', [
        'text' => __('ข้อมูลผู้เซ็น'),
        'block_icon_class' => 'icon-document',
    ])
    <div class="block-content">
        @include('admin.contracts.page-2.contract-detail')
        @include('admin.contracts.page-2.table-file-upload')
    </div>
</div>
@include('admin.contracts.sections.submit')
