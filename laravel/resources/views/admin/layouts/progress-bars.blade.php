
<div class="content-side">
    <ul class="nav-main">
        @php
            $sideMenu = new \App\Repositories\ProgressBarRepository($progress);
        @endphp
        {!! $sideMenu->render() !!}
    </ul>




