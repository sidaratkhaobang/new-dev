<div class="row items-push mb-4 ">
    <div class="col-sm-8">
        @if (!isset($view))
            <div class="btn-group bg-white" role="group">
                @can(Actions::Manage . '_' . Resources::AccidentInform)
                    <a type="button" href="{{ route('admin.accident-informs.edit', $d->id) }}"
                        class="btn btn-outline-primary
                    {{ in_array(Route::currentRouteName(), ['admin.accident-informs.edit', 'admin.accident-informs.show']) ? 'active' : '' }}">
                        {{ __('accident_informs.accident_job') }}
                    </a>
                @endcan
                @can(Actions::Manage . '_' . Resources::AccidentInform)
                    <a type="button" href="{{ route('admin.accident-informs.show-claim', $d->id) }}"
                        class="btn btn-outline-primary 
                    {{ in_array(Route::currentRouteName(), ['admin.accident-informs.show-claim']) ? 'active' : '' }}">
                        {{ __('accident_informs.claim_detail') }}
                    </a>
                @endcan
            </div>
        @else
            <div class="btn-group bg-white" role="group">
                @can(Actions::View . '_' . Resources::AccidentInform)
                    <a type="button" href="{{ route('admin.accident-informs.show', $d->id) }}"
                        class="btn btn-outline-primary
                    {{ in_array(Route::currentRouteName(), ['admin.accident-informs.show']) ? 'active' : '' }}">
                        {{ __('accident_informs.accident_job') }}
                    </a>
                @endcan
                @can(Actions::View . '_' . Resources::AccidentInform)
                    <a type="button" href="{{ route('admin.accident-informs.show-claim', $d->id) }}"
                        class="btn btn-outline-primary 
                    {{ in_array(Route::currentRouteName(), ['admin.accident-informs.show-claim']) ? 'active' : '' }}">
                        {{ __('accident_informs.claim_detail') }}
                    </a>
                @endcan
            </div>
        @endif
    </div>
</div>
