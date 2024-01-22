@extends('admin.layouts.layout')
@section('page_title', __('insurance_car.insurance_car_title'))
@section('content')
    @include('admin.insurance-car-cmi.sections.form-btn-menu')
    <div class="block {{ __('block.styles') }}">
        <div class="block-content">
            @include('admin.components.block-header', [
                'text' =>  __('cmi_cars.coverage_info'),

            ])
            <div class="block-content">
                <div class="table-wrap table-responsive mb-4">
                    <table class="table table-striped table-vcenter">
                        <thead class="bg-body-dark">
                        <tr>
                            <th class="text-center" colspan="4">{{ __('cmi_cars.cmi_headline') }}</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td rowspan="2">
                                <p>1. จำนวนเงินค่าเสียหายเบื้องต้นได้โดยไม่ต้องรอพิสูจน์ความผิด</p>
                            </td>
                            <td colspan="2">
                                <p>1. ค่ารักษาพยาบาลจากการบาดเจ็บ (ตามจริง)</p>
                            </td>
                            <td>
                                <p>ไม่เกิน 30,00 บาท/คน</p>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2">
                                <p>2. การเสียชีวิต สูญเสียอวัยวะ หรือทุพพลภาพอย่างถาวร</p>
                            </td>
                            <td>
                                <p>35,000 บาท/คน</p>
                            </td>
                        </tr>
                        <tr>
                            <td rowspan="7">
                                <p>2. ค่าเสียหายส่วนที่เกินกว่าค่าเสียหายเบื้องต้น สำหรับผู้ประสบภัย<br>
                                    (จะได้รับภายหลังจากการพิสูจน์แล้วว่าไม่ได้เป็นฝ่ายละเมิด)</p>
                            </td>
                            <td colspan="2">
                                <p>1. ค่ารักษาพยาบาลจากการบาดเจ็บ</p>
                            </td>
                            <td>
                                <p>ไม่เกิน 80,000 บาท/คน</p>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2">
                                <p>2. การเสียชีวิตหรือทุพพลภาพอย่างถาวรสิ้นเชิง</p>
                            </td>
                            <td>
                                <p>ไม่เกิน 500,000 บาท/คน</p>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2">
                                <p>3. กรณีทุพพลภาพอย่างถาวร</p>
                            </td>
                            <td>
                                <p>ไม่เกิน 300,000 บาท/คน</p>
                            </td>
                        </tr>
                        <tr>
                            <td rowspan="3">4. สูญเสียอวัยวะ</td>
                            <td>นิ้วขาด 1 ข้อขึ้นไป</td>
                            <td>200,000 บาท/คน</td>
                        </tr>
                        <tr>
                            <td>สูญเสียอวัยวะ 1 ส่วน</td>
                            <td>250,000 บาท/คน</td>
                        </tr>
                        <tr>
                            <td>สูญเสียอวัยวะ 2 ส่วน</td>
                            <td>500,000 บาท/คน</td>
                        </tr>
                        <tr>
                            <td colspan="2">5. ชดเชยรายวัน 200 บาท รวมกันไม่เกิน 20 วัน<br> กรณีเข้าพักรักษาพยาบาลในสถานพยาบาลในฐานะคนไข้</td>
                            <td>สูงสุดไม่เกิน 4,000 บาท/คน</td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="block {{ __('block.styles') }}">
        <div class="block-content">
            @include('admin.components.block-header', [
                'text' =>  __('lang.remark'),

            ])
            <div class="block-content">
                <p>1. ผู้ขับขี่ที่กระทำละเมิด (ฝ่ายผิด) จะได้แค่ความคุ้มครอง ค่าเสียหายเบื้องต้นเท่านั้น</p>
                <p>2. ผู้ประสบภัย หมายรวมถึง ผู้ขับขี่ที่ถูกละเมิด ผู้โดยสาร และบุคคลภายนอก</p>
                <p>3. จำนวนเงินเงินค่าเสียหายเบื้องต้น (ตามตารางข้อ 1) เป็นส่วนหนึ่งของจำนวนเงินคุ้มครองผู้ประสบภับ (ตามตารางข้อ 2)</p>
            </div>
        </div>
    </div>
@endsection
