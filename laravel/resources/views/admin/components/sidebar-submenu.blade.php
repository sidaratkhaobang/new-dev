<li class="nav-main-item {{ ($is_open ? 'open' : '') }}">
    <a class="nav-main-link nav-main-link-submenu" data-toggle="submenu" aria-haspopup="true" aria-expanded="false" href="#">
        @if(!empty($icon))
        <span class="{{ $icon }}" ></span> 
        @endif
        <span class="nav-main-link-name">{!! $title !!}</span>
    </a>
    <ul class="nav-main-submenu">
        @foreach($menus as $menu)
        {!! $menu->render() !!}
        @endforeach
    </ul>
</li>
