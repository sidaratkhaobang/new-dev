<header>
    <div class="header-text-r">
        <img src="{{ base_path('storage/logo-pdf/true.jpg') }}" style="float: top;" alt="">
    </div>
    <p style="text-align: center; font-size: 24px; font-weight: bold;">แบบฟอร์มขอเช่ารถยนต์</p>

    <div style="float: right; width: 40%; margin-top:-30px;">
        <p>วันที่ <span class="p-10">{{ $today }}</span></p>
    </div>
    <div style="text-align: left; margin-top: 50px; line-height: 8px; font-size: 18px;">
        <p>ผู้ขอเช่า <span class="p-10">{{ ($lt_rental->customer_name) ? $lt_rental->customer_name : '-' }}</span></p>
        <p>ชื่อผู้ติดต่อ <span class="p-10"> {{ ($lt_rental->customer_name) ? $lt_rental->customer_name : '-' }} </span></p>
        <p style="margin-bottom: 0px;">เบอร์โทรสาร <span class="p-10">{{ ($lt_rental->customer_fax) ? $lt_rental->customer_fax : '-'}}</span>  &nbsp; เบอร์โทร <span> {{ ($lt_rental->customer_tel) ? $lt_rental->customer_tel : '-' }}</span></p>
    </div>
    <p style="text-align: left; margin-top: -10px;">มีความประสงค์ขอเช่ารถยนต์ดังรายการต่อไปนี้</p>
</header>

