@if (!in_array($d->status, [RentalStatusEnum::DRAFT]))
    <div class="row">
        <div class="col-sm-12">
            <p class="fs-4">ใบเสนอราคา :
                <a href="{{ route('admin.quotations.short-term-rental-pdf', ['rental_bill_id' => $d->id]) }}"
                    target="_blank">
                    {{ $d->quotation ? $d->quotation->qt_no : '' }}
                </a>
            </p>
            <p class="fs-4">ใบนำฝากชำระเงิน :
                <a class="fs-5" target="_blank" href="{{ route('admin.quotations.short-term-rental-payment-pdf', ['rental_bill_id' => $d->id]) }}" >
                    <i class="icon-document-download text-primary"></i>
                        {{-- <i class="fa fa-arrow-up-right-from-square"></i> Link --}}
                </a>
            </p>
        </div>
        {{-- <div class="col-sm-4">
            <p class="fs-4">ใบนำฝากชำระเงิน :
                <a target="_blank" href="{{ route('admin.quotations.short-term-rental-payment-pdf', ['rental_bill_id' => $d->id]) }}" >
                    <i class="icon-document-download text-primary"></i>
                </a>
            </p>
        </div> --}}
    </div>
@endif
@if (!in_array($d->status,  [RentalStatusEnum::DRAFT]))
<div class="row">
    <div class="col-sm-12">
        <p class="fs-4">ลิงก์ 2c2p :
            @if (!in_array($d->status,  [RentalStatusEnum::PAID]))
            <span class="fs-6 2c2p-payment-span">
                <a id="2c2p-payment-link" href="{{ $d->payment_url }}" target="_blank">
                    {{ $d->payment_url ?? '' }}
                </a>
            </span>
            @if (in_array($d->status, [RentalStatusEnum::PENDING]))
                <span class="m-2">
                    <a class="btn btn-alt-primary btn-sm payment-link-btn" onclick="gen2c2pPaymentLink('{{ $d->id }}')" href="#"
                        class="dropdown-item">
                        <i class="fa fa-arrow-rotate-right"></i>
                        Generate Link
                    </a>
                </span>
                <span class="fs-sm" id="toasts">
                </span>
            @endif
            @else
            -
            @endif
        </p>
    </div>
</div>
@endif
<div class="row">
    <div class="col-sm-12">
        <p class="fs-4">สถานะการชำระเงิน
            <span
                class="text-{{ __('short_term_rentals.class_' . $d->status) }}">{{ __('short_term_rentals.status_' . $d->status) }}</span>
        </p>
    </div>
</div>
{{-- @if() --}}
@if($d->bill_type != RentalBillTypeEnum::OTHER && $d->bill_type != RentalBillTypeEnum::SECONDARY)
<div class="row mb-4">
    <div class="col-sm-5">
        <x-forms.select-option id="payment_gateway" :value="$d->payment_gateway" :list="null"
        :optionals="[
            'select_class' => 'js-select2-custom',
            'ajax' => true,
            'default_option_label' => $payment_gateway_name,
        ]"
        :label="__('short_term_rentals.payment_method')" />
    </div>
    <div class="col-sm-5">
        <x-forms.input-new-line id="payment_remark" :value="$d->payment_remark" :label="__('lang.remark')" />
    </div>
</div>
<div class="row mb-4">
    @if(in_array($d->status, [RentalStatusEnum::PENDING]))
    <div class="col-sm-5">
        <x-forms.select-option id="payment_status" :value="$d->status"
            :list="null" :label="__('short_term_rentals.status')"
            :optionals="[
                'select_class' => 'js-select2-custom',
                'ajax' => true,
                'default_option_label' => $payment_status_name,
            ]" />
    </div>
    @endif
</div>
<div class="row mb-4">
    <div class="col-sm-5" id="date_payment">
        <div class="col-sm-12">
            <x-forms.date-input id="payment_date" :value="$d->payment_date ? get_date_time_by_format($d->payment_date, 'd-m-Y') : null" :label="__('short_term_rentals.payment_date')" :optionals="['placeholder' => __('lang.select_date')]" />
            </div>
    </div>


    <div class="col-sm-5" id="ref_file">
        @if (isset($view))
        @if(!empty($ref_sheet_image))
            <x-forms.view-image :id="'ref_sheet_image'" :label="__('short_term_rentals.ref_sheet')" :list="$ref_sheet_image" />
            @endif
        @else
            <x-forms.upload-image :id="'ref_sheet_image'" :label="__('short_term_rentals.ref_sheet')" />
        @endif
    </div>
</div>
@endif
