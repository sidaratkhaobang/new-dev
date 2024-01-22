@push('styles')
    <style>
        .btn-group {
            background-color: #fff;
        }
    </style>
@endpush
<div class="row items-push">
    <div class="col-sm-8">
        <div class="btn-group" role="group">
            @can(Actions::View . '_' . Resources::GPSRemoveStopSignalJob)
                <a type="button" href="{{ route('admin.gps-remove-stop-signal-jobs.index') }}"
                    class="btn btn-outline-primary
                    {{ in_array(Route::currentRouteName(), ['admin.gps-remove-stop-signal-jobs.index']) ? 'active' : '' }}">
                    {{ __('gps.job_tab') }}
                </a>
            @endcan
            @can(Actions::View . '_' . Resources::GPSRemoveSignalJob)
                <a type="button" href="{{ route('admin.gps-remove-signal-jobs.index') }}"
                    class="btn btn-outline-primary
                    {{ in_array(Route::currentRouteName(), ['admin.gps-remove-signal-jobs.index']) ? 'active' : '' }}">
                    {{ __('gps.job_remove_tab') }}
                </a>
            @endcan
            @can(Actions::View . '_' . Resources::GPSStopSignalJob)
                <a type="button" href="{{ route('admin.gps-stop-signal-jobs.index') }}"
                    class="btn btn-outline-primary
                {{ in_array(Route::currentRouteName(), ['admin.gps-stop-signal-jobs.index']) ? 'active' : '' }}">
                    {{ __('gps.job_stop_tab') }}
                </a>
            @endcan
        </div>
    </div>
</div>
