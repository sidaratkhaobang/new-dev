<div class="row">
    <div class="col-sm-6">
        <div><label class="text-start col-form-label">ลายเซ็นผู้ตรวจ</label><span style="color:red"> *</span></div>
        @if (!isset($view))
            <button type="button" onclick="signature()" id="sig_button"
                class="btn btn-primary ">{{ __('inspection_cars.signature') }}</button>
        @endif
        <span id="sig">
            @if (!empty($signature))
                <a href="{{ $signature_get_media['url'] }}" download style="line-height: 30px;"
                    id="signature">{{ $signature['file_name'] }} </a><span id="signature_date">{{ date_format($signature['created_at'],'d/m/Y, H:i') }}</span>     
            @else
            @if (isset($view))
            -     
            @endif          
            @endif
            <span id="date_detail">

            </span>
        </span>
    </div>
</div>
