@extends('admin.layouts.layout-error')

@section('content')
    <div class="text-center" >
        <h1 class="error-status text-primary m-0" >403</h1>
        <h2 class="error-message text-secondary" >
            {{ ((!empty($exception->getMessage())) ? $exception->getMessage() : 'คุณไม่มีสิทธิ์เข้าถึงหน้านี้') }}
        </h2>
        <h3 class="error-message-2 text-secondary" >กรุณาติดต่อแอดมินเพื่อเปิดสิทธิ์การใช้งาน</h3>
    </div>
@endsection