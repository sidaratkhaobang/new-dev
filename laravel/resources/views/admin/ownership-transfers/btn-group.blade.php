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
        'เตรียมข้อมูลโอนกรรมสิทธิ์' => ['admin.ownership-transfers.edit,admin.ownership-transfers.show'],
        'บันทึกวันที่โอนรถ' => [
            'admin.ownership-transfers.edit-waiting-transfer,admin.ownership-transfers.show-waiting-transfer',
        ],
        'บันทึกข้อมูลหลังโอนและส่งเล่มคืน' => [
            'admin.ownership-transfers.edit-transfering,admin.ownership-transfers.show-transfering',
        ],
    ],
])
