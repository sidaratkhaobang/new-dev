<li class="nav-main-item">
    <a class="nav-main-link {{ ($is_active ? 'active' : '') }}" href="{{ $link }}">
        @if(!empty($icon))
        <span class="{{ $icon }}" ></span> 
        @endif
        <span class="nav-main-link-name">{!! $title !!}</span>
    </a>
</li>
