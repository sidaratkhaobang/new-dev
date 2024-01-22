<!-- Side Navigation -->
<div class="content-side">
    <ul class="nav-main">
        @php
            $sideMenu = new \App\Repositories\SideBarMenuRepository();
        @endphp
        {!! $sideMenu->render() !!}
    </ul>
</div>
<!-- END Side Navigation -->
