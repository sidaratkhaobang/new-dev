@push('styles')
    <style>
        .active {
            color: #4D82F3 !important;
            background-color: #E5EDFE !important;
            border-color: #4D82F3 !important;
        }

        .inactive {
            color: #94A3B8 !important;
            background-color: #F6F8FC !important;
            border-color: #CBD4E1 !important;
        }
    </style>
@endpush
@include('admin.layouts.progress-bars', [
    'progress' => [
        'บันทึกตรวจสอบความผิดและแจ้งหน่วยงานที่รับผิดชอบ' => [null, in_array($d->status , [SignYellowTicketStatusEnum::DRAFT,SignYellowTicketStatusEnum::WAITING_WRONG]) ],
        'กรอกการชำระเงินจากกรมขนส่ง' => [null, $d->status == SignYellowTicketStatusEnum::WAITING_PAY_DLT],
        'กรอกการชำระเงินจากหน่วยงานที่รับผิดชอบ' => [null, $d->status == SignYellowTicketStatusEnum::WAITING_PAY_FINE],
    ],
])
