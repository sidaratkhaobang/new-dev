<div class="block {{ __('block.styles') }}">
    @section('block_options_btn_donload_pdf')
    @if(!empty($d?->id))
    <a target="_blank" href="{{route('admin.repair-bills.print-pdf',['repair_bill_id' => $d])}}" class="btn btn-primary">
        ดาวน์โหลด PDF
    </a>
    @endif
    @endsection
    @include('admin.components.block-header', [
    'text' => __('repair_bills.bill_detail'),
    'block_icon_class' => 'icon-document',
    'block_option_id' => '_btn_donload_pdf'
    ])
    <div class="block-content">
        <div class="form-group row push mb-4">
            <div class="col-sm-3">
                <x-forms.select-option id="center_id" :value="$d?->center_id ?? null" :list="[]" :optionals="[
                                'placeholder' => __('lang.search_placeholder'),
                                'ajax' => true,
                                'default_option_label' => $center_name,
                            ]" :label="__('repair_bills.search_center')" />
                {{-- <x-forms.input-new-line id="center_id" :value="$d?->center_id ?? null"--}}
                {{-- :label="__('repair_bills.search_center')"--}}
                {{-- :optionals="['required' => true]"/>--}}

            </div>
            <div class="col-sm-3">
                <x-forms.select-option id="bill_recipient" :value="$d?->bill_recipient ?? null" :list="[]" :optionals="[
                                'placeholder' => __('lang.search_placeholder'),
                                'ajax' => true,
                                'default_option_label' => $bill_recipient_name,
                            ]" :label="__('repair_bills.search_bill_recipient')" />
                {{-- <x-forms.input-new-line id="bill_recipient" :value="$d?->bill_recipient ?? null"--}}
                {{-- :label="__('repair_bills.search_bill_recipient')"--}}
                {{-- :optionals="['required' => true]"/>--}}
            </div>
            <div class="col-sm-3">
                <x-forms.date-input id="billing_date" :value="$d?->billing_date ?? null" :label="__('repair_bills.billing_date')" :optionals="['required' => true]" />
            </div>
            <div class="col-sm-3">
                <x-forms.date-input id="receive_money_date" :value="$d?->receive_money_date ?? null" :label="__('repair_bills.receive_money_date')" :optionals="['required' => true]" />
            </div>
        </div>
    </div>
</div>