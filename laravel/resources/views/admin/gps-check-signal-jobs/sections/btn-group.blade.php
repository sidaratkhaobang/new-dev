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
            @can(Actions::View . '_' . Resources::GPSCheckSignalShortTerm)
                <a type="button" href="{{ route('admin.gps-check-signal-jobs.index') }}"
                    class="btn btn-outline-primary
                    {{ in_array(Route::currentRouteName(), ['admin.gps-check-signal-jobs.index']) ? 'active' : '' }}">
                    {{ __('gps.job_short_term') }}
                    @if ($short_term_count > 0)
                        ({{ $short_term_count }})
                    @else
                        (0)
                    @endif
                </a>
            @endcan
            @if ($allow_user)
                @can(Actions::View . '_' . Resources::GPSCheckSignalLongTerm)
                    <a type="button" href="{{ route('admin.gps-check-signal-job-long-term.index') }}"
                        class="btn btn-outline-primary
                    {{ in_array(Route::currentRouteName(), ['admin.gps-check-signal-job-long-term.index']) ? 'active' : '' }}">
                        {{ __('gps.job_long_term') }}
                        @if ($long_term_count > 0)
                            ({{ $long_term_count }})
                        @else
                            (0)
                        @endif
                    </a>
                @endcan
            @endif
            @if ($allow_user)
                @can(Actions::View . '_' . Resources::GPSCheckSignalReplacement)
                    <a type="button" href="{{ route('admin.gps-check-signal-job-replaces.index') }}"
                        class="btn btn-outline-primary
                    {{ in_array(Route::currentRouteName(), ['admin.gps-check-signal-job-replaces.index']) ? 'active' : '' }}">
                        {{ __('gps.job_replacement') }}
                        @if ($replacement_car_count > 0)
                            ({{ $replacement_car_count }})
                        @else
                            (0)
                        @endif
                    </a>
                @endcan
            @endif
            @if ($allow_user)
                @can(Actions::View . '_' . Resources::GPSCheckSignalShortTermBranch)
                    <a type="button" href="{{ route('admin.gps-check-signal-job-branch.index') }}"
                        class="btn btn-outline-primary
                {{ in_array(Route::currentRouteName(), ['admin.gps-check-signal-job-branch.index']) ? 'active' : '' }}">
                        {{ __('gps.job_short_term_branch') }}
                        @if ($short_branch_count > 0)
                            ({{ $short_branch_count }})
                        @else
                            (0)
                        @endif
                    </a>
                @endcan
            @endif
            @can(Actions::View . '_' . Resources::GPSCheckSignalKratos)
                <a type="button" href="{{ route('admin.gps-check-signal-job-kratos.index') }}"
                    class="btn btn-outline-primary {{ in_array(Route::currentRouteName(), ['admin.gps-check-signal-job-kratos.index']) ? 'active' : '' }}">
                    {{ __('gps.job_kratos_tracking') }}
                </a>
            @endcan
        </div>
    </div>
</div>
