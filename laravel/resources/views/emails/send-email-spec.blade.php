@component('mail::message')

**เรียน** {{$mail_data['dealer_name']}}

{{-- &nbsp;&nbsp;&nbsp;&nbsp;ทาง Trueleasing ได้แนบลิงก์สำหรับกรอกข้อมูลการส่งมอบรถ.<br>
กรุณากรอกหมายเลขตัวถัง เลขตัวถัง และวันที่พร้อมส่งมอบ. --}}


&nbsp;&nbsp;&nbsp;&nbsp;บริษัท ทรูลีสซิ่ง จำกัด มีความต้องการให้ทาง {{$mail_data['dealer_name']}} ทำการกรอกข้อมูลเช็กรถ โดยกรุณากรอกวันที่พร้อมส่งมอบของรุ่นรถต่างๆ ตามที่บริษัท ทรูลีสซิ่ง จำกัด ต้องการจัดหา ในลิงก์ด้านล่าง


ลิงก์ : <{{$mail_data['url']}}>

หากมีความต้องการจัดซื้อรถในอนาคต ทางบริษัทจะทำการติดต่อ {{$mail_data['dealer_name']}} กลับไป <br>
## ขอบคุณค่ะ

--------------------------------------------------------

**Address** : 616 Luang Phang Road, Tubyao, Lad Krabang, Bangkok 10520.<br>
Facebook : Trueleasing | Instagram : Trueleasing | [www.trueleasing.co.th](www.trueleasing.co.th)

![Trueleasing]({{ $mail_data['image']}})

@endcomponent
