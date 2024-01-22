@if (!isset($view))
@if (in_array($d->status, [SignYellowTicketStatusEnum::WAITING_WRONG]))
    <button type="button"
        class="btn btn-primary btn-custom-size btn-save-form btn-save-form-sign-yellow-ticket-mistake">{{ __('tax_renewals.save_draft') }}</button>
    <button type="button"
        class="btn btn-primary btn-custom-size btn-save-form-sign-yellow-ticket-mistake "
        data-status="{{ SignYellowTicketStatusEnum::WAITING_PAY_DLT }}">{{ __('sign_yellow_tickets.save_inspec') }}</button>
@elseif (in_array($d->status, [SignYellowTicketStatusEnum::WAITING_PAY_DLT]))
    <button type="button"
        class="btn btn-primary btn-custom-size btn-save-form-sign-yellow-ticket-paid ">{{ __('tax_renewals.save_draft') }}</button>
    <button type="button"
        class="btn btn-primary btn-custom-size btn-save-form-sign-yellow-ticket-paid "
        data-status="{{ SignYellowTicketStatusEnum::WAITING_PAY_FINE }}">{{ __('sign_yellow_tickets.save_paid') }}</button>
@elseif (in_array($d->status, [SignYellowTicketStatusEnum::WAITING_PAY_FINE]))
    <button type="button"
        class="btn btn-primary btn-custom-size btn-save-form-sign-yellow-ticket-paid-fine ">{{ __('tax_renewals.save_draft') }}</button>
    <button type="button"
        class="btn btn-primary btn-custom-size btn-save-form-sign-yellow-ticket-paid-fine "
        data-status="{{ SignYellowTicketStatusEnum::SUCCESS }}">{{ __('sign_yellow_tickets.save_paid_fine') }}</button>
@else
    <button type="button"
        class="btn btn-primary btn-custom-size btn-save-form-sign-yellow-ticket ">{{ __('tax_renewals.save_draft') }}</button>
    <button type="button" class="btn btn-primary btn-custom-size btn-save-form-sign-yellow-ticket "
        data-status="{{ SignYellowTicketStatusEnum::WAITING_WRONG }}">{{ __('sign_yellow_tickets.save_info_find') }}</button>
@endif
@endif