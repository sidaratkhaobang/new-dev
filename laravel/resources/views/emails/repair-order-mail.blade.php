@component('mail::message')
**เรียน** {{ $mail_data['center_name'] }}

&nbsp;&nbsp;&nbsp;&nbsp;บริษัท ทรูลีสซิ่ง จำกัด ขอนำส่งใบสั่งซ่อม ประเภทงาน{{ $mail_data['repair_type'] }} ของทะเบียนรถ
{{ $mail_data['license_plate'] }} โดยจะมีรายละเอียดการซ่อม/ตรวจเช็ก ในไฟล์แนบ กรณีจำเป็นต้องซ่อมนอกเหนือรายการนี้
กรุณาติดต่อกลับฝ่ายบริหารการซ่อมบำรุง โทร. 02762357180 มือถือ. 0891399494, 0891399595, 0859804004, 0859803771


## Best Regards

--------------------------------------------------------

**Address** : 616 Luang Phang Road, Tubyao, Lad Krabang, Bangkok 10520.<br>
Facebook : Trueleasing | Instagram : Trueleasing | [www.trueleasing.co.th](www.trueleasing.co.th)

![Trueleasing]({{ $mail_data['image'] }})
@endcomponent
