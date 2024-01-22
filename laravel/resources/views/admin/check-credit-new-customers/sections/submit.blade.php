<div class="row">
    <div class="text-end">
        <a class="btn btn-outline-secondary btn-custom-size me-1" href="{{ route('admin.check-credit-new-customers.index') }}">{{ __('lang.back') }}</a>
        @if (Route::is('*.edit') || Route::is('*.create'))
            @if(!isset($d->status) || $d->status == \App\Enums\CheckCreditStatusEnum::DRAFT)
                <button type="button" class="btn btn-alt-secondary btn-save-form-draft btn-custom-size me-1">{{ __('lang.save_draft') }}</button>
            @endif

            @if(isset($d->status) && $d->status == \App\Enums\CheckCreditStatusEnum::CONFIRM && !$d->is_create_customer)
                <button type="button" class="btn btn-primary btn-save-create-customer btn-custom-size me-1">{{ __('check_credit.form.btn-save-create-customer') }}</button>
                @elseif(!$d->is_create_customer)
                <button type="button" class="btn btn-primary btn-save-pending-approve btn-custom-size">{{ __('check_credit.form.btn-save-pending-approve') }}</button>
            @endif
        @endif
        {{--        @if (is_null($d->status))--}}
        {{--            <button type="button" class="btn btn-primary btn-save-draft" >{{ __('lang.save_draft') }}</button>--}}
        {{--            <a class="btn btn-info btn-save-form" >{{ __('lang.save') }}</a>--}}
        {{--        @elseif ($d->status == \App\Enums\PRStatusEnum::DRAFT)--}}
        {{--            <button type="button" class="btn btn-danger btn-delete-row" data-route-delete="{{ route('admin.purchase-requisitions.destroy', ['purchase_requisition' => $d->id])  }}" >{{ __('lang.delete') }}</button>--}}
        {{--            <button type="button" class="btn btn-primary btn-save-draft">{{ __('lang.save_draft') }}</button>--}}
        {{--            <a class="btn btn-info btn-save-form" >{{ __('lang.save') }}</a>--}}
        {{--        @elseif (in_array($d->status, [\App\Enums\PRStatusEnum::PENDING_REVIEW, \App\Enums\PRStatusEnum::CONFIRM,  \App\Enums\PRStatusEnum::REJECT]))--}}
        {{--            <a class="btn btn-danger btn-cancel-status"--}}
        {{--                data-id="{{ $d->id }}" data-status="{{ \App\Enums\PRStatusEnum::CANCEL }}">{{ __('lang.cancel') }}</a>--}}
        {{--            <a class="btn btn-info btn-save-form" >{{ __('lang.save') }}</a>--}}
        {{--        @else--}}
        {{--        @endif--}}
    </div>
</div>
