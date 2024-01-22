<div class="dropdown d-inline-block">
    <button type="button" class="btn btn-alt-secondary" id="page-header-user-dropdown" data-bs-toggle="dropdown"
        aria-haspopup="true" aria-expanded="false">
        <i class="fa fa-fw fa-user d-sm-none"></i>
        <span class="d-none d-sm-inline-block">{{ Auth::user() ? Auth::user()->username : ''}}</span>
        <i class="fa fa-fw fa-angle-down opacity-50 ms-1 d-none d-sm-inline-block"></i>
    </button>
    <div class="dropdown-menu dropdown-menu-end p-0" aria-labelledby="page-header-user-dropdown">
        <div class="bg-primary-dark rounded-top fw-semibold text-white text-center p-3">
            User Options
        </div>
        <div class="p-2">
            {{-- <a class="dropdown-item" href="be_pages_generic_profile.html">
                <i class="far fa-fw fa-user me-1"></i> Profile
            </a>
            <a class="dropdown-item" href="javascript:void(0)" data-toggle="layout" data-action="side_overlay_toggle">
                <i class="far fa-fw fa-building me-1"></i> Settings
            </a>
            <div role="separator" class="dropdown-divider"></div> --}}
            <a class="dropdown-item" href="{{ route('logout') }}"
                onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                <i class="far fa-fw fa-arrow-alt-circle-left me-1"></i> {{ __('auth.signout') }}
                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                    @csrf
                </form>
            </a>
        </div>
    </div>
</div>
