@component('mail::message')
**เรียน** : ฝ่ายอุบัติเหตุ


&nbsp;&nbsp;&nbsp;&nbsp;จากที่รถ{{ $mail_data['car_license_plate'] }} รุ่นรถ {{ $mail_data['car_brand_name'] }} ได้เกิดอุบัติเหตุในวันที่ {{ $mail_data['accident_date'] }} เวลา {{ $mail_data['accident_time'] }}
เคส{{ $mail_data['case'] }} ลักษณะที่เกิดเหตุ {{ $mail_data['accident_description'] }} สถานที่ {{ $mail_data['accident_place'] }} ตำบล/แขวง {{ $mail_data['subdistrict'] }} อำเภอ/เขต{{ $mail_data['district'] }} จังหวัด {{ $mail_data['province'] }} สถานที่ปัจจุบันของรถ {{ $mail_data['current_place'] }} @if($mail_data['is_parties']) มีคู่กรณี  @else ไม่มีคู่กรณี @endif @if($mail_data['amount_wounded_total'] > 0) มีผู้บาดเจ็บทั้งหมด {{ $mail_data['amount_wounded_total'] }} คน @endif @if($mail_data['amount_wounded_driver'] > 0) จำนวนผู้บาดเจ็บฝ่ายผู้ขับขี่ {{ $mail_data['amount_wounded_driver'] }} คน @endif
@if($mail_data['amount_wounded_parties'] > 0) จำนวนผู้บาดเจ็บฝ่ายคู่กรณี/อื่น {{ $mail_data['amount_wounded_parties'] }} คน @endif @if($mail_data['amount_deceased_total'] > 0) มีผู้เสียชีวิต มีผู้เสียชีวิตทั้งหมด {{ $mail_data['amount_deceased_total'] }} คน @endif
@if($mail_data['amount_deceased_driver'] > 0) จำนวนผู้เสียชีวิตฝ่ายผู้ขับขี่ {{ $mail_data['amount_deceased_driver'] }} คน @endif @if($mail_data['amount_deceased_parties'] > 0) จำนวนผู้เสียชีวิตฝ่ายคู่กรณี/อื่น {{ $mail_data['amount_deceased_parties'] }} คน @endif @if($mail_data['first_lifting']) โดยได้มีการรถยกครั้งที่ 1 โดย {{$mail_data['slide_driver']}} เบอร์โทรผู้ติดต่อ {{$mail_data['slide_tel']}} @endif @if($mail_data['need_folklift']) และต้องการรถยกของ TLS ไปยกจาก {{$mail_data['slide_from']}} ไปยัง {{$mail_data['slide_to']}} ในวันที่ {{$mail_data['slide_date']}} @endif

## ขอบคุณค่ะ

--------------------------------------------------------

**Address** : 616 Luang Phang Road, Tubyao, Lad Krabang, Bangkok 10520.<br>
Facebook : Trueleasing | Instagram : Trueleasing | [www.trueleasing.co.th](www.trueleasing.co.th)

![Trueleasing]({{ $mail_data['image'] }})
@endcomponent
