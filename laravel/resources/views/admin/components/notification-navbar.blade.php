<li class="noti-item @if(!empty($readat)) noti-read  @endif" >
    <a class="d-flex text-dark py-3" href="javascript:void(0)" onclick="readNotification('{{ $id }}')" >
        <div class="flex-shrink-0 mx-3 align-self-center">
            <i class="fa-fw {{ $icon }}"></i>
        </div>
        <div class="flex-grow-1 fs-sm pe-2">
            <div class="noti-title">{{ $title }}</div>
            <div class="noti-description">{{ $description }}</div>
            <div class="noti-timeago">{{ $timeago }}</div>
        </div>
        <div class="flex-shrink-0 ms-2 me-3 align-self-center">
            @if(empty($readat))
            <span class="dot-unread" >&#x2022;</span>
            @endif
        </div>
    </a>
</li>