@component('mail::message')

@foreach($mail_data['before_medias_arr'] as $before_medias)
<img class="img-fluid" src='{{$before_medias}}' alt="">
@endforeach

**เรียน** {{ $mail_user }}

&nbsp;&nbsp;&nbsp;&nbsp;จากที่ลูกค้าแจ้งเคลมรถ{{ $mail_data['car_brand'] }} {{ $mail_data['car_class'] }} ทะเบียน {{ $mail_data['license_plate'] }} เกิดอุบัติเหตุแล้วยกเข้าซ่อมที่{{ $mail_data['cradle_name'] }} ตรวจสอบความเสียหายเบื้องต้น
ส่งใบเสนอราคาค่าซ่อม พร้อมรูปถ่านมาตามแบบ จึงขอนัดคุมราคา 3 ฝ่าย ผ่านระบบ Vroom ในวันที่ {{ $mail_data['appointment_time'] }} เวลา {{ $mail_data['appointment_date'] }} น.

{{Auth::user()->name}} is inviting you to a meeting.

Join the meeting:
{{ $mail_data['remark'] }}


## Best Regards

--------------------------------------------------------

**Address** : 616 Luang Phang Road, Tubyao, Lad Krabang, Bangkok 10520.<br>
Facebook : Trueleasing | Instagram : Trueleasing | [www.trueleasing.co.th](www.trueleasing.co.th)

![Trueleasing]({{ $mail_data['image'] }})
@endcomponent
