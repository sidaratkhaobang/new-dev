<div class="block {{ __('block.styles') }}">
    <div class="block-content box-padding-bottom">
        @include('admin.components.block-header',[
        'text' =>   __('request_premium.remark')   ,
        'block_icon_class' => 'icon-document',
        'block_option_id' => '_remark'
        ])
        <div class="row mt-2 mb-2">
            <div class="col-sm-12">
                <label>1. หากมีการปรับเปลี่ยนราคารถหรือมีการติดตั้งอุปกรณ์เพิ่มเติม มีผลทำให้ค่าเบี้ยประกันภัยภาคสมัครใจ
                    (ประเภท 1) เปลี่ยนแปลงจากเดิม</label>
            </div>
        </div>
        <div class="row mt-2 mb-2">
            <div class="col-sm-12">
                <label>2. ทุนประกันเฉพาะตัวรถไม่รวมอุปกรณ์ ในกรณีอุปกรณ์ไม่เกิน 100,000 บาท
                    ได้รับความคุ้มครองตามปกติ</label>
            </div>
        </div>
        <div class="row mt-2 mb-2">
            <div class="col-sm-12">
                <label>3. ใบเสนอราคานี้มีผลบังคับใช้ภายใน 90 วัน</label>
            </div>
        </div>
    </div>
</div>
