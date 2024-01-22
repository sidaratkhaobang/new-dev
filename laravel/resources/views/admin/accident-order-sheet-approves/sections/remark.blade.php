<div class="block {{ __('block.styles') }}">
    @include('admin.components.block-header', [
        'text' => __('lang.remark'),
    ])
    <div class="block-content">
        <label class="text-start col-form-label">{{ __('lang.remark') }}</label>
        <textarea class="form-control" id="remark" name="remark" placeholder="" :value="$d->remark"
            :label="__('lang.remark')"></textarea>

        <br>
    </div>
</div>
@include('admin.accident-informs.modals.repair-modal')
