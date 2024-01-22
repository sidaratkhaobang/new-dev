<div class="voucher-item mb-3" >
    <x-forms.checkbox-block id="promotion_code_id_{{ $id }}" name="promotion_code_ids[]" value="{{ $id }}" selected="{{ $selected_id }}" >
        <div>
            <p class="mb-1 font-size-sm">เลขที่ Voucher : {{ $voucher_code }}</p>
            <p class="mb-0 font-size-sm"><b>{{ $promotion_name }}</b></p>
        </div>
    </x-forms.checkbox-block>
</div>