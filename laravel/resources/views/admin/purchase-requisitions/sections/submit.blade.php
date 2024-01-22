<div class="row push">
    <div class="text-end">     
        <a class="btn btn-secondary" href="{{ route('admin.purchase-requisitions.index') }}" >{{ __('lang.back') }}</a>
        @if (is_null($d->status))
            <button type="button" class="btn btn-primary btn-save-draft" >{{ __('lang.save_draft') }}</button>
            <a class="btn btn-info btn-save-form" >{{ __('lang.save') }}</a>
        @elseif ($d->status == \App\Enums\PRStatusEnum::DRAFT)
            <button type="button" class="btn btn-danger btn-delete-row" data-route-delete="{{ route('admin.purchase-requisitions.destroy', ['purchase_requisition' => $d->id])  }}" >{{ __('lang.delete') }}</button>
            <button type="button" class="btn btn-primary btn-save-draft">{{ __('lang.save_draft') }}</button> 
            <a class="btn btn-info btn-save-form" >{{ __('lang.save') }}</a>
        @elseif (in_array($d->status, [\App\Enums\PRStatusEnum::PENDING_REVIEW, \App\Enums\PRStatusEnum::CONFIRM,  \App\Enums\PRStatusEnum::REJECT]))
            <a class="btn btn-danger btn-cancel-status" 
                data-id="{{ $d->id }}" data-status="{{ \App\Enums\PRStatusEnum::CANCEL }}">{{ __('lang.cancel') }}</a>
            <a class="btn btn-info btn-save-form" >{{ __('lang.save') }}</a>
        @else
        @endif           
    </div>
</div>