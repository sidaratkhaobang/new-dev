<header>
    <div class="header-text-l">
        <img src="{{ base_path('storage/logo-pdf/true.jpg') }}" style="float: top;" alt="">
        <p style="margin-top: 80px;" class="left-p">บริษัท ทรู ลีสซิ่ง จํากัด </p>
        <p style="font-size: 15px;">18 อาคารทรูทาวเวอร์ ถนนรัชดาภิเษก แขวงห้วยขวาง กรุงเทพฯ 10310</p>
        <p>โทร 0-2859-7878 &nbsp; Fax 0-2859-7979</p>

        <p style="font-weight: bold;">เรียน</p>
        <p class="left-p">{{ $customer_name }}</p>
        <p style="font-size: 14px; line-height: 14px; margin-top: 5px;">{{ $customer_address }}</p>
        <p style="font-size: 14px;">โทร {{ $customer_tel }}</p>

    </div>
    <div class="header-text-r">
        <p style="font-weight: bold; font-size: 30px;">ใบเสนอราคา</p>
        <hr>
        <div class="display-left">
            <p class="ml-50">เลขที่ <span class="ml-50">{{ $qt_no }}@if(isset($d->edit_count))Rev.{{strval(sprintf('%02d', $d->edit_count))}}@endif</span></p>
        </div>
        <div class="display-left">
            <p class="ml-50">วันที่ <span
                    class="ml-50">{{ $d->created_at ? get_thai_date_format($created_at, 'd F Y') : '-' }}</span></p>
        </div>
        <div class="display-left">
            <p class="ml-50">ฝ่าย <span class="ml-50">จัดซื้อและจำหน่ายรถยนต์</span></p>
        </div>
        <hr>
        <div class="display-left">
            <p class="ml-50">เรื่อง <span class="ml-50">เสนอราคาค่าเช่ารถยนต์
                    {{ $service_type_name ? $service_type_name : null }}</span></p>
        </div>
    </div>
</header>
