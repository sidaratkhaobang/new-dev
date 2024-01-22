@if (!in_array($rental_bill->status, [RentalStatusEnum::DRAFT]))
<div class="row">
    <div class="col-sm-4">
        <p class="fs-4">ใบเสนอราคา :
            <a href="{{ route('admin.quotations.short-term-rental-pdf', ['rental_bill_id' => $rental_bill->id]) }}"
                target="_blank">
                {{ $rental_bill->quotation ? $rental_bill->quotation->qt_no : '' }}
            </a>
        </p>
        <p class="fs-4">ใบนำฝากชำระเงิน :
            <a class="fs-5" target="_blank" href="{{ route('admin.quotations.short-term-rental-payment-pdf', ['rental_bill_id' => $rental_bill->id]) }}" >
                <i class="icon-document-download text-primary"></i>
            </a>
        </p>
    </div>
</div>
@endif

@if (!in_array($rental_bill->status, [RentalStatusEnum::DRAFT]))
    <div class="row">
        <div class="col-sm-12">
            <p class="fs-4">ลิงก์ 2c2p :
                <span class="fs-6 2c2p-payment-span">
                    <a id="2c2p-payment-link" href="{{ $rental_bill->payment_url }}" target="_blank">
                        {{ $rental_bill->payment_url ?? '' }}
                    </a>
                </span>
                @if (in_array($rental_bill->status, [RentalStatusEnum::PENDING]))
                    <span class="m-2">
                        <a class="btn btn-alt-primary btn-sm payment-link-btn"
                            onclick="gen2c2pPaymentLink('{{ $rental_bill->id }}')" href="javascript:;"
                            class="dropdown-item">
                            <i class="fa fa-arrow-rotate-right"></i>
                            Generate Link
                        </a>
                        <input type="hidden" id="link" value="{{ $rental_bill->payment_url }}">
                        @if (in_array($rental_bill->status, [RentalStatusEnum::PENDING]))
                            {{-- <p class="fs-4"> --}}
                            <span class="">
                                <a class="btn btn-alt-primary btn-sm copy_link" href=""
                                    class="dropdown-item">
                                    <i class="far fa-clone" aria-hidden="true"></i>
                                    คัดลอกลิงก์
                                </a>
                            </span>
                            <span class="fs-sm" id="toasts">
                            </span>
                            {{-- </p> --}}
                        @endif
                    </span>
                    <span class="fs-sm" id="toasts">
                    </span>
                @endif
            </p>
        </div>
    </div>
@endif

<div class="row">
    {{-- <h4>{{ __('short_term_rentals.payment') }}</h4> --}}
    <div class="col-sm-12">
        @if (in_array($rental_bill->status, [RentalStatusEnum::PENDING]))
            {{-- <x-forms.radio-inline id="order_channel_disabled" :value="$order_channel" :list="$order_channel_list" :label="null" /> --}}
            <x-forms.hidden id="order_channel" :value="$order_channel" />
        @else
            {{-- <x-forms.radio-inline id="order_channel" :value="$order_channel" :list="$order_channel_list" :label="null" /> --}}
            <x-forms.hidden id="order_channel" :value="$order_channel" />
        @endif
    </div>
    <div class="col-sm-4" id="payment_gateway_id">
        <x-forms.select-option id="payment_gateway" :value="$rental_bill->payment_gateway" :list="null" :optionals="[
            'select_class' => 'js-select2-custom',
            'ajax' => true,
            'default_option_label' => $payment_gateway_name,
        ]"
            :label="__('short_term_rentals.payment_method')" />
    </div>
    <div class="col-sm-4">
        <x-forms.date-input id="payment_date" :value="$rental_bill->payment_date" :label="__('short_term_rentals.paid_date')" :optionals="['placeholder' => __('lang.select_date')]" />
    </div>
    <div class="col-sm-4">
        @if (isset($view))
            <x-forms.view-image :id="'ref_sheet_image'" :label="__('short_term_rentals.paid_file')" :list="null" />
        @else
            <x-forms.upload-image :id="'ref_sheet_image'" :label="__('short_term_rentals.paid_file')" />
        @endif
    </div>
</div>

<div class="row">
    <div class="col-sm-12" id="payment_remark_id">
        <x-forms.input-new-line id="payment_remark" :value="$rental_bill->payment_remark" :label="__('lang.remark')" />
    </div>
</div>