<!-- Notifications Dropdown -->
@php
    $user_notifications = get_user_notifications();
    $nav_btn_classes = 'btn-alt-secondary';
    if(sizeof($user_notifications) > 0){
        $nav_btn_classes = 'btn-primary';
    }
    $size_notis = get_unread_user_notifications_count($user_notifications);
    if($size_notis > 99){
        $size_notis = '99+';
    } else if($size_notis <= 0){
        $size_notis = '';
    }
@endphp
<div class="dropdown d-inline-block">
    <button type="button" class="btn {{ $nav_btn_classes }}" id="page-header-notifications-dropdown"
        data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
        <i class="fa fa-fw fa-bell"></i>
        <span id="noti-count" >{{ $size_notis }}</span>
    </button>
    <div class="dropdown-menu dropdown-menu-lg dropdown-menu-end p-0"
        aria-labelledby="page-header-notifications-dropdown">
        <div class="rounded-top fw-semibold p-3">
            การแจ้งเตือน
        </div>
        <div class="ps-3 pe-3 pb-2" >
            <div class="form-check form-block">
                <input class="form-check-input" type="radio" value="1" id="noti-radio-1" name="noti-radio" checked >
                <label class="form-check-label" for="noti-radio-1">
                    <span class="">ยังไม่ได้อ่าน</span>
                </label>
            </div>
            <div class="form-check form-block">
                <input class="form-check-input" type="radio" value="2" id="noti-radio-2" name="noti-radio">
                <label class="form-check-label" for="noti-radio-2">
                    <span class="">ทั้งหมด</span>
                </label>
            </div>
        </div>
        <ul class="nav-items mt-2 mb-0" id="my-notifications" >
            @foreach($user_notifications as $notification)
            <x-notification-navbar :id="$notification['id']" :title="$notification['title']" :description="$notification['description']" 
                :url="$notification['url']" :type="$notification['type']" :datetime="$notification['datetime']" :readat="$notification['readat']" />
            @endforeach
            @if(sizeof($user_notifications) <= 0)
            <li class="noti-item ">
                <div class="flex-grow-1 fs-sm pe-2">
                    <div class="noti-title text-center py-4">ไม่มีแจ้งเตือน</div>
                </div>
            </li>
            @endif
        </ul>
    </div>
</div>
<!-- END Notifications Dropdown -->

@push('scripts')
@include('admin.components.scripts.notification-script')
@endpush