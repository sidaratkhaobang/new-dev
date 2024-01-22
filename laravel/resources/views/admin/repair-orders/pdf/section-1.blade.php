@extends('admin.layouts.pdf.pdf-layout')
@push('custom_styles')
    <style>
        @page {
            margin: 140px 25px 10px 25px;
        }

        header {
            top: -100px;
            height: 10px;
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

        .table-collapse {
            border-collapse: collapse;
        }

        .table-collapse td,
            {
            border: 1px solid black;
            padding-left: 10px;
        }

        tbody {
            line-height: 16px;
            margin: 0;
            padding: 0;
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
            width: 30%;
        }

        .block-x {
            display:
        }

        .text-content {
            margin-left: 5%;
            color: #333333;
        }
    </style>
@endpush

<header>
    <div class="header-text-l">
        <div class="first-half">
            <img src="{{ base_path('storage/logo-pdf/true.jpg') }}" style="float: left;" alt="">
            <p style="font-size: 20px; padding:3px; margin-left:180px;">บริษัท ทรู ลีสซิ่ง จํากัด </p>
            <p style="font-size: 15px; margin-left:180px;">18 อาคารทรูทาวเวอร์ ถนนรัชดาภิเษก แขวงห้วยขวาง กรุงเทพฯ 10310
            </p>
        </div>
        <div class="second-half">
            <p class="text-right" style="font-weight: bold; font-size: 20px;">{{ $data['worksheet_name'] ?? '' }}</p>
            <div class="display-left">
                <p class="text-right">{{ $data['worksheet_no'] ?? '' }}</p>
            </div>
        </div>
    </div>
</header>

@section('content')
    <div class="contet" style="">
        <table class="content table-collapse">
            <tbody class="content">
                <tr>
                    <td colspan="4">วันที่เวลา / DateTime <span class="text-content">{{ $data['datetime'] ?? '' }}</span>
                    </td>
                    <td colspan="4">อ้างถึง / Ref. <span class="text-content">{{ $data['ref'] ?? '' }}</span></td>
                </tr>
                <tr>
                    <td class="height-4 text-content" colspan="8">
                        ศูนย์บริการ
                        <span class="text-content">{{ $data['center_name'] ?? '' }}</span>
                    </td>
                </tr>
                <tr>
                    <td class="height-4" colspan="8">
                        <span>{{ $data['center_address'] ?? '' }}</span> <br>
                        <span>Tel. {{ $data['center_tel'] ?? '' }}</span>
                        <span>Fax. {{ $data['center_fax'] ?? '' }}</span>
                    </td>
                </tr>
                <tr>
                    <td class="font-xl" colspan="8">รถยนต์ / Vehicle</td>
                </tr>
                <tr>
                    <td colspan="1">ทะเบียน / Reg No.</td>
                    <td colspan="3">ยี่ห้อ / รุ่น / Brand / Model</td>
                    <td colspan="2">เลขไมล์ / Mileage</td>
                    <td colspan="2">สี / Colors</td>
                </tr>
                <tr>
                    <td class="height-4" colspan="1">
                        <span class="text-content">{{ $data['license_plate'] ?? '' }}</span>
                    </td>
                    <td class="height-4" colspan="3">
                        <span class="text-content">{{ $data['car_class'] ?? '' }}</span>
                    </td>
                    <td class="height-4" colspan="2">
                        <span class="text-content">{{ $data['mileage'] ?? '' }}</span>
                    </td>
                    <td class="height-4" colspan="2">
                        <span class="text-content">{{ $data['car_color'] ?? '' }}</span>
                    </td>
                </tr>
                <tr>
                    <td colspan="1">จดทะเบียน / Reg date.</td>
                    <td colspan="3">เลขตัวถัง / Chassis No.</td>
                    <td colspan="4">เลขเครื่อง / Engine No.</td>
                </tr>
                <tr>
                    <td class="height-4" colspan="1">
                        <span class="text-content">{{ $data['registered_date'] ?? '' }}</span>
                    </td>
                    <td class="height-4" colspan="3">
                        <span class="text-content">{{ $data['chassis_no'] ?? '' }}</span>
                    </td>
                    <td class="height-4" colspan="4">
                        <span class="text-content">{{ $data['engine_no'] ?? '' }}</span>
                    </td>
                </tr>
                <tr>
                    <td class="font-xl" colspan="8">รายละเอียดผู้ใช้รถ / Customer's Details</td>
                </tr>
                <tr>
                    <td colspan="3">บริษัท / Company Name</td>
                    <td colspan="2">ผู้ขอซ่อม / Customer Name.</td>
                    <td colspan="2">โทรศัพท์ / Tel.</td>
                    <td colspan="1">โทรสาร / Fax.</td>
                </tr>
                <tr>
                    <td class="height-4" colspan="3">
                        <span class="text-content">{{ $data['company_name'] ?? '' }}</span>
                    </td>
                    <td class="height-4" colspan="2">
                        <span class="text-content">{{ $data['customer_name'] ?? '' }}</span>
                    </td>
                    <td class="height-4" colspan="2">
                        <span class="text-content">{{ $data['tel'] ?? '' }}</span>
                    </td>
                    <td class="height-4" colspan="1">
                        <span class="text-content">{{ $data['fax'] ?? '' }}</span>
                    </td>
                </tr>
                <tr>
                    <td colspan="4" style="width: 100px;">รายละเอียดตรวจเช็ค/Maintenance Items</td>
                    <td colspan="4" style="width: 100px;">เงื่อนไขส่งซ่อมศูนย์บริการ</td>
                </tr>
                <tr>
                    <td colspan="4" style="vertical-align: baseline; width: 100px;">
                        <table class="border-none">
                            @foreach ($data['repair_order_line'] as $index => $d)
                                <tr class="border-none" style="line-height: 13px;">
                                    <td class="border-none">{{ $index + 1 }}</td>
                                    <td class="border-none">{{ $d->code_name }}</td>
                                    <td class="border-none">{{ $d->check_text }}</td>
                                </tr>
                            @endforeach
                        </table>
                    </td>
                    <td colspan="4" style="width: 100px;">
                        @foreach ($data['condition_repair'] as $index => $d)
                            <span>{{ $d->name }}<br>
                                @if (sizeof($d->sub_condition_repair) > 0)
                                    @foreach ($d->sub_condition_repair as $key_checklist => $sub_condition)
                                    &nbsp;-{{ $sub_condition->name }}
                                        <br>
                                    @endforeach
                                @endif
                            </span>
                        @endforeach
                    </td>
                </tr>
                <tr>
                    <td colspan="8">วันหมดอายุใบสั่งซ่อม / Expiry date
                        <span class="text-content">{{ $data['expire_date'] ?? '' }}</span>
                    </td>
                </tr>
                <tr>
                    <td colspan="8">มีปัญหางานซ่อม ให้ติดต่อฝ่ายบริหารซ่อมบำรุง
                        สำนักงานสาขาลาดกระบังเท่านั้น
                    </td>
                </tr>
                <tr>
                    <td class="height-4" colspan="8">
                        สำนักงานใหญ่:<span class="text-content">เลขที่18 อาคาร ทรู ทาวเวอร์ ถนนรัชดาภิเษก แขวงห้วยขวาง
                            เขตห้วยขวาง กรุงเทพฯ
                            10310</span> <br>
                        สาขาลาดกระบัง:<span class="text-content">เลขที่616 ถนนหลวงแพ่ง แขวงทับยาว เขตลาดกระบัง กรุงเทพฯ
                            10520 โทรศัพท์ 0-2762-3500 โทรสาร 0-2762-3535 </span>
                    </td>
                </tr>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="8">
                        <table>
                            <tbody>
                                <tr class="text-center">
                                    <td class="border-none height-4">_______________________________</td>
                                    <td class="border-none height-4">_______________________________</td>
                                    <td class="border-none height-4">_______________________________</td>
                                </tr>
                                <tr class="text-center">
                                    <td class="border-none">ผู้สั่งงาน</td>
                                    <td class="border-none">ผู้ตรวจสอบ</td>
                                    <td class="border-none">ผู้อนุมัติ</td>
                                </tr>
                            </tbody>
                        </table>
                    </td>
                </tr>
            </tfoot>
        </table>
    </div>
@endsection
