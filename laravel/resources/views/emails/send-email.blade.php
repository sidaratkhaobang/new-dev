@component('mail::message')

**เรียน** {{$mail_data['dealer_name']}}

{{-- &nbsp;&nbsp;&nbsp;&nbsp;ทาง Trueleasing ได้แนบลิงก์สำหรับกรอกข้อมูลการส่งมอบรถ.<br>
กรุณากรอกหมายเลขตัวถัง เลขตัวถัง และวันที่พร้อมส่งมอบ. --}}


&nbsp;&nbsp;&nbsp;&nbsp;บริษัท ทรูลีสซิ่ง จำกัด มีความต้องการให้ทาง {{$mail_data['dealer_name']}} ทำการกรอกข้อมูลการส่งมอบรถตามใบสั่งซื้อรถเลขที่ {{$mail_data['po_no']}} โดยกรุณากรอกหมายเลขตัวถัง เลขตัวถัง และวันที่พร้อมส่งมอบ ในลิงก์ด้านล่าง


ลิงก์ : <{{$mail_data['url']}}>

## ขอบคุณค่ะ

--------------------------------------------------------

**Address** : 616 Luang Phang Road, Tubyao, Lad Krabang, Bangkok 10520.<br>
Facebook : Trueleasing | Instagram : Trueleasing | [www.trueleasing.co.th](www.trueleasing.co.th)

![Trueleasing]({{ $mail_data['image']}})

@endcomponent
