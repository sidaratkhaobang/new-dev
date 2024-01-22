<div class="block {{ __('block.styles') }}">
    <div class="block-header">
        <div class="block-title" >
            <div class="d-flex" >
                <div style="flex-shrink: 0; flex-basis: 200px;" >
                    <i class=" me-2 icon-document"></i> เพิ่ม Voucher
                </div>
                <div class="flex-grow-1" >
                    <input type="text" class="form-control" id="voucher_code" name="voucher_code" placeholder="กรอกเลข Voucher" >
                </div>
            </div>
        </div>
        <div class="block-options ps-0">
            <button type="button" class="btn btn-primary btn-add-voucher" style="margin-left: 12px;" >
                <i class="fa fa-plus-circle me-2"></i>เพิ่ม
            </button>
        </div>
    </div>
    <div class="block-content pt-0">
        <div class="voucher-wrap" >
            @foreach($voucher_list as $voucher)
                @include('admin.short-term-rental-promotion.components.voucher-item', [
                    'voucher_code' => $voucher->voucher_code,
                    'promotion_name' => $voucher->promotion_name,
                    'id' => $voucher->id,
                    'selected_id' => $voucher->selected_id
                ])
            @endforeach
        </div>
    </div>
</div>