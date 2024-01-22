@if (in_array($d->status, [TransferCarEnum::IN_PROCESS, TransferCarEnum::SUCCESS]))
    <div class="block {{ __('block.styles') }}">
        <div class="block-content">
            <h4>{{ __('transfer_cars.confirm_transfer_detail') }}</h4>
            <hr>
            <div class="row push mb-4">
                <div class="col-sm-3">
                    <x-forms.label id="confirmation_date" name="confirmation_date" :value="$d->confirmation_date ? get_thai_date_format($d->confirmation_date, 'd/m/Y H:i') : null" :label="__('transfer_cars.confirmation_date')" />
                </div>
                <div class="col-sm-3">
                    <x-forms.label id="confirmation_user_id" :value="$d->userConfirm ? $d->userConfirm->name : null" :label="__('transfer_cars.confirmation_user')" />
                </div>
                <div class="col-sm-3">
                    @if (Route::is('*.edit') || Route::is('*.create'))
                        <x-forms.date-input id="pick_up_date" name="pick_up_date" :value="$d->pick_up_date" :label="__('transfer_cars.pick_up_date')" />
                    @else
                        <x-forms.input-new-line id="pick_up_date" name="pick_up_date" :value="$d->pick_up_date ? get_thai_date_format($d->pick_up_date, 'd/m/Y') : null"
                            :label="__('transfer_cars.pick_up_date')" />
                    @endif
                </div>
                <div class="col-sm-3">
                    <x-forms.label id="confirmation_user_id" :value="$d->userConfirmPickup ? $d->userConfirmPickup->name : get_user_name()" :label="__('transfer_cars.user_confirm_pickup')" />
                </div>
            </div>
        </div>
    </div>
@endif
@if (in_array($d->status, [TransferCarEnum::IN_PROCESS, TransferCarEnum::SUCCESS]))
    <div class="block {{ __('block.styles') }}">
        <div class="block-content">
            @include('admin.transfer-cars.submit')
        </div>
    </div>
@endif
