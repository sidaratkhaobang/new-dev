<div class="block {{ __('block.styles') }}">
    @include('admin.components.block-header', [
        'text' => __('install_equipments.info'),
    ])
    <div class="block-content">
        <div class="row push mb-5">
            <div class="col-sm-4">
                <x-forms.input-new-line id="created_at" :value="$d->created_at ? get_thai_date_format($d->created_at, 'j F Y') : ''" :label="__('install_equipments.created_at')" />
            </div>
            <div class="col-sm-4">
                <x-forms.input-new-line id="created_by" :value="$d->createdBy ? $d->createdBy->name : get_user_name()" :label="__('install_equipments.created_by')" />
            </div>
            <div class="col-sm-4">
                <x-forms.select-option id="po_id" :value="$d->po_id" :list="null" :label="__('install_equipments.po')"
                    :optionals="[
                        'placeholder' => __('lang.search_placeholder'),
                        'ajax' => true,
                        'default_option_label' => $d->purchaseOrder?->po_no,
                    ]" />
            </div>
        </div>
    </div>
</div>
