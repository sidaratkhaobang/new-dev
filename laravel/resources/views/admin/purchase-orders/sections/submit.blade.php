<div class="row push">
    <div class="text-end">    
        <a class="btn btn-secondary" href="{{ $redirect_route }}" >{{ __('lang.back') }}</a>
        @if(!isset($view_detail))
            @if (is_null($d->status))
                <button type="button" class="btn btn-primary btn-save-form" >{{ __('lang.save_draft') }}</button>
                <a class="btn btn-info btn-update-status" >{{ __('lang.save') }}</a>
            @elseif ($d->status == \App\Enums\POStatusEnum::DRAFT)
                {{-- <button type="button" class="btn btn-danger btn-delete-row" data-route-delete="{{ route('admin.purchase-orders.destroy', ['purchase_order' => $d->id])  }}" >{{ __('lang.delete') }}</button>   --}}
                {{-- <button type="button" class="btn btn-primary btn-save-form" >{{ __('lang.save_draft') }}</button> --}}
                <a class="btn btn-danger btn-cancel-status" :id="\App\Enums\POStatusEnum::CANCEL" >{{ __('lang.cancel') }}</a>
                <a class="btn btn-info btn-update-status" >{{ __('lang.save') }}</a>
            @elseif (in_array($d->status, [\App\Enums\POStatusEnum::PENDING_REVIEW, \App\Enums\POStatusEnum::CONFIRM,  \App\Enums\POStatusEnum::REJECT]))
                <a class="btn btn-danger btn-cancel-status" :id="\App\Enums\POStatusEnum::CANCEL" >{{ __('lang.cancel') }}</a>
                <a class="btn btn-info btn-update-status" >{{ __('lang.save') }}</a>
            @else
            @endif         
        @endif   
    </div>
</div>