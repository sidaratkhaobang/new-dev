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
        'เตรียมข้อมูลต่ออายุภาษี' => ["admin.tax-renewals.edit,admin.tax-renewals.show"],
        'กรอกวันที่ส่งต่อภาษี' => ["admin.tax-renewals.edit-waiting-send-tax,admin.tax-renewals.show-waiting-send-tax"],
        'บันทึกข้อมูลหลังต่อภาษี' => ["admin.tax-renewals.edit-taxing,admin.tax-renewals.show-taxing"],
        'ส่งป้ายภาษีและคืนเล่มทะเบียน' => ["admin.tax-renewals.edit-waiting-send-tax-register-book,admin.tax-renewals.show-waiting-send-tax-register-book"],
    ],
])
