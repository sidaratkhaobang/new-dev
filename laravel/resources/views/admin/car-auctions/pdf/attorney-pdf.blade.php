@extends('admin.layouts.pdf.pdf-layout')
@push('custom_styles')
    <style>
        @page {
            margin: 210px 50px 5px 50px;
        }

        header {
            top: -190px;
            height: 100px;
            bottom: 0px;
        }

        table {
            width: 100% !important;
        }

        thead {
            display: table-header-group;
        }

        .text-left {
            text-align: left;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .height-4 {
            height: 30px;
        }

        .height-max {
            height: 200px;
        }

        .font-xl {
            font-size: 20px;
            font-weight: bold;
        }

        footer {
            bottom: 20px;
        }

        .border-none {
            border: 0px !important;
        }

        .first-half {
            float: left;
            width: 70%;
        }

        .second-half {
            float: right;
            width: 70%;
            line-height: 16px;
        }

        .indent {
            text-indent: 100px;
        }

        .text-content {
            margin-left: 10%;
            color: #333333;
        }

        .separated {
            border-bottom: 1px solid black;
            padding-top: 10px;
            /* margin: 10px; */
        }

        .padding-50 {
            padding-left: 50px;
            padding-right: 50px;
        }

        .padding-25 {
            padding-left: 25px;
            padding-right: 25px;
        }

        .padding-75 {
            padding-left: 75px;
            padding-right: 75px;
        }

        .padding-100 {
            padding-left: 100px;
            padding-right: 100px;
        }

        .right {
            position: fixed;
            right: 0px;
            width: 300px;
            bottom: 30px;
            line-height: 8px;
        }
    </style>
@endpush
<header>
    <div style="text-align: center; font-size: 28px; font-weight: bold;">
        <p>หนังสือมอบอำนาจ</p>
    </div>
    <div class="second-half">
        <p class="text-right" style="font-size: 18px;">เขียนที่ <span class="separated" style="padding-left: 100px;"> บริษัท
                ทรู
                ลีสซิ่งจํากัด</span> </p>
        <p class="text-right" style="font-size: 18px;">
            วันที่________เดือน______________ พ.ศ________ </p>
    </div>
</header>

@section('content')
    <div class="content">
        <div class="text-left" style="line-height: 12px;">
            <div class="indent">
                <p>โดยหนังสือฉบับนี้ ข้าพเจ้า
                    <span class="separated padding-100"> บริษัท ทรู ลีสซิ่งจํากัด</span>
                    อายุ <span class="separated" style="padding-left: 70px;
                    padding-right: 70px;">-
                    </span>ปี
                </p>
            </div>
            <p>เชื้อชาติ <span class="separated padding-25"> - </span>
                สัญชาติ <span class="separated padding-25"> ไทย </span>
                อยู่บ้านเลขที่ <span class="separated padding-100">18 อาคารทรูทาวเวอร์</span>
                หมู่ <span class="separated padding-50">-</span>
            </p>
            <p>ซอย <span class="separated padding-25"> - </span>
                ถนน <span class="separated padding-75"> รัชดาภิเษก </span>
                ตำบล/แขวง <span class="separated padding-50">ห้วยขวาง</span>
                อำเภอ/เขต <span class="separated"
                    style="padding-left: 40px;
                padding-right: 40px;">ห้วยขวาง</span>
            </p>
            <p>จังหวัด <span class="separated padding-100"> กรุงเทพมหานคร </span>
                ซึ่งเป็นผู้มีอำนาจลงนามผูกพัน____________________________________________
            </p>
            <p>สำนักงานตั้งอยู่เลขที่______________________________
                ถนน______________________________
                ตำบล/แขวง________________________________
            </p>
            <p>อำเภอ/เขต______________________________
                จังหวัด________________________________________
                โทรศัพท์______________________________
            </p>
            <div class="indent" style="line-height: 12px;">
                <p>ขอมอบอำนาจให้__________________________________________
                    อายุ______________ ปี
                    เชื้อชาติ_______________________</p>
            </div>
            <p>สัญชาติ__________________
                อยู่บ้านเลขที่__________________
                หมู่_______________
                ซอย______________________
                ถนน______________________
            </p>
            <p>
                ตำบล/แขวง_____________________________
                อำเภอ/เขต_____________________________
                จังหวัด________________________________________
            </p>
            <div style="line-height: 12px;">
                <p>เป็นผู้มีอำนาจทำการแทนข้าพเจ้า</p>
                <p class="separated" style="font-weight: bold;">ดำเนินการแจ้งย้ายรถยนต์</p>
                <p class="separated" style="font-weight: bold;">ทะเบียน &nbsp;&nbsp;&nbsp;{{ $license_plate }}</p>
                <p class="separated" style="font-weight: bold;">ใช้จังหวัด</p>
                <p>เพื่อเป็นหลักฐาน ข้าพเจ้าได้ลงลายมือชื่อ หรือพิมพ์ลายนิ้วมือไว้เป็นสำคัญต่อหน้าพยานแล้ว</p>
            </div>
        </div>
        <div class="right">
            <p>(ลงชื่อ)______________________________________ผู้มอบอำนาจ</p>
            <p class="separated" style="text-align: center;">บริษัท ทรู ลีสซิ่งจํากัด</p>
            <p>(ลงชื่อ)______________________________________ผู้รับมอบอำนาจ</p>
            <p>______________________________________________________</p>
            <p style="font-size: 14px;">ข้าพเจ้าขอรับรองว่าเป็นลายมือชื่อ หรือลายพิมพ์นิ้วมืออันแท้จริงของผู้มอบอำนาจ</p>
            <p>(ลงชื่อ)______________________________________พยาน</p>
            <p>______________________________________________</p>
            <p>(ลงชื่อ)______________________________________พยาน</p>
            <p>______________________________________________</p>
        </div>
    </div>
@endsection
