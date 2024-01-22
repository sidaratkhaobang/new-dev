@extends('admin.layouts.pdf.pdf-layout')
@push('custom_styles')
    <style>
        @page {
            margin: 210px 50px 30px 50px;
        }

        header {
            top: -190px;
            height: 100px;
            bottom: 0px;
        }

        footer {
            bottom: 50px;
        }

        .header-text-r {
            /* text-align: center; */
            float: right;
            margin-right: 0px;
            line-height: 5px;
            width: 20%;
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

        .indent {
            text-indent: 100px;
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
            width: 320px;
            bottom: 150px;
            line-height: 16px;
        }

        .font-size-18 {
            font-size: 18px;
        }
    </style>
@endpush
<header>
    {{-- <div class="header-text-r">
        <img src="{{ base_path('storage/logo-pdf/true.jpg') }}" style="float: top;" alt="">
    </div> --}}
    <div class="text-center" style="margin-top: 50px;">
        <p style="font-size: 36px; font-weight: bold;">หนังสือยืนยันการซื้อขาย</p>
        <p style="font-size: 24px; font-weight: bold; line-height: 8px;">Transaction Confirmation Form</p>
    </div>

</header>
@section('content')
    <div class="content">
        <div class="text-left" style="line-height: 12px;">
            <div class="indent">
                <p>ข้าพเจ้า
                    <span class="separated" style="padding-left: 200px;
                    padding-right: 250px;"> บริษัท
                        ทรู ลีสซิ่งจํากัด</span>
                </p>
            </div>
            <p>อยู่บ้านเลขที่ <span class="separated padding-100">18 อาคารทรูทาวเวอร์</span>
                หมู่ <span class="separated padding-25">-</span>
                ถนน <span class="separated" style="padding-left: 80px;
                padding-right: 100px;"> รัชดาภิเษก
                </span>
            </p>
            <p>ตำบล/แขวง <span class="separated padding-50">ห้วยขวาง</span>
                อำเภอ/เขต <span class="separated"
                    style="padding-left: 40px;
                padding-right: 40px;">ห้วยขวาง</span>
                จังหวัด <span class="separated padding-100"> กรุงเทพมหานคร </span>
            </p>
            <p>
                ตกลงขายรถยนต์ &nbsp;&nbsp;ทะเบียน <span class="separated padding-25">{{ $data['license_plate'] }}</span>
                เลขตัวถัง <span class="separated padding-50">{{ $data['chassis_no'] }}</span>
                เลขเครื่อง <span class="separated padding-50">{{ $data['engine_no'] }}</span>
            </p>
            <p>แก่_____________________________________________________________________________________________________________________
            </p>
            <p>_______________________________________________________________________________________________________________________
            </p>
            <p class="font-size-18">
                รถคันดังกล่าวนี้เป็นรถที่ข้าพเจ้ารับมาจาก บริษัท ทรูลีสซิ่ง จำกัด
            <p class="font-size-18">รถคันนี้ได้ตกลงขายในราคา<span class="separated"
                    style="padding-left: 250px;
                padding-right: 250px;">{{ number_format($data['selling_price'], 2, '.', ',') }}&nbsp;({{ bahtText(number_format($data['selling_price'], 2, '.', ',')) }})</span>
            </p>
            <p style="font-size: 20px; line-height: 16px;" class="indent">
                ข้าพเจ้าขอยืนยันว่าข้าพเจ้าเป็นเจ้าของและมีสิทธิในการขายรถยนต์คันนี้แต่ผู้เดียวและรถยนต์คันดังกล่าวไม่
                มีภาระผูกพันหรือข้อเรียกร้องใดๆทั้งสิ้น
            </p>
        </div>

        <div class="right">
            <p>(ลงชื่อ)______________________________________ผู้ขาย/ประทับตรา</p>
            <p class="separated" style="text-align: center;">บริษัท ทรู ลีสซิ่งจํากัด</p>
            <p>(ลงชื่อ)______________________________________พยาน</p>
            <p>(ลงชื่อ)______________________________________พยาน</p>
        </div>
        {{-- <footer>
            <div class="header-text-l">
                <p class="font-size-18">สำนักงานใหญ่ 18 อาคารทรูทาวเวอร์ ถนนรัชดาภิเษก แขวงห้วยขวาง เขตห้วยขวาง กรุงเทพฯ</p>
                <p>สำนักงานลาดกระบัง เลขที่ 616 แขวงทับยาว เขตลาดกระบัง กรุงเทพฯ 10520 โทร 02-859-7878 แฟ็กซ์ 02-8592-7979
                </p>
            </div>
            <p class="separated"></p>
        </footer> --}}
    </div>
@endsection
