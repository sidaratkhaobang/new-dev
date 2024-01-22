<div class="row">
    <div class="col-sm-3">
        <label class="text-start col-form-label">ชื่อ-นามสกุลพนักงานผู้รับมอบรถ</label><span style="color:red"> *</span>
        <input class="form-control" type="text" name="recipient_staff_name" id="recipient_staff_name" value="{{$inspection_job->recipient_staff_name ? $inspection_job->recipient_staff_name : '' }}"/>
    </div>
    <div class="col-sm-3">
        <label class="text-start col-form-label">เบอร์โทรศัพท์ผู้รับมอบรถ</label><span style="color:red"> *</span>
        <input class="form-control" type="text" maxlength="10" minlength="10" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*?)\..*/g, '$1');" name="recipient_staff_tel" id="recipient_staff_tel" value="{{$inspection_job->recipient_staff_tel ? $inspection_job->recipient_staff_tel : '' }}" />
    </div>
    <div class="col-sm-6">
        <div><label class="text-start col-form-label">ลายเซ็นลูกค้า</label><span style="color:red"> *</span></div>
        @if (!isset($view))
            <button type="button" onclick="signature()" id="sig_button"
                class="btn btn-primary">{{ __('inspection_cars.signature_customer') }}</button>
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
