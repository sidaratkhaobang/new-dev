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
        'เตรียมข้อมูลก่อนจดทะเบียน' => ['admin.registers.edit,admin.registers.show'],
        'บันทึกข้อมูลหลังจดทะเบียน' => ['admin.registers.edit-registered,admin.registers.show-registered'],
    ],
])
