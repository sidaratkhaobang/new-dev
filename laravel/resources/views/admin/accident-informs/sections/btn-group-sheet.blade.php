<div class="row items-push mb-4 ">
    <div class="col-sm-8">
        @if (!isset($view))
            <div class="btn-group bg-white" role="group">
                @can(Actions::Manage . '_' . Resources::AccidentInformSheet)
                    <a type="button" href="{{ route('admin.accident-inform-sheets.edit', $d->id) }}"
                        class="btn btn-outline-primary
                    {{ in_array(Route::currentRouteName(), ['admin.accident-inform-sheets.edit', 'admin.accident-inform-sheets.show']) ? 'active' : '' }}">
                        {{ __('accident_informs.accident_job') }}
                    </a>
                @endcan
                @can(Actions::Manage . '_' . Resources::AccidentInformSheet)
                    <a type="button" href="{{ route('admin.accident-inform-sheets.edit-claim', $d->id) }}"
                        class="btn btn-outline-primary 
                    {{ in_array(Route::currentRouteName(), ['admin.accident-inform-sheets.edit-claim']) ? 'active' : '' }}">
                        {{ __('accident_informs.claim_detail') }}
                    </a>
                @endcan
            </div>
        @else
            <div class="btn-group bg-white" role="group">
                @can(Actions::View . '_' . Resources::AccidentInformSheet)
                    <a type="button" href="{{ route('admin.accident-inform-sheets.show', $d->id) }}"
                        class="btn btn-outline-primary
                    {{ in_array(Route::currentRouteName(), ['admin.accident-inform-sheets.show']) ? 'active' : '' }}">
                        {{ __('accident_informs.accident_job') }}
                    </a>
                @endcan
                @can(Actions::View . '_' . Resources::AccidentInformSheet)
                    <a type="button" href="{{ route('admin.accident-inform-sheets.show-claim', $d->id) }}"
                        class="btn btn-outline-primary 
                    {{ in_array(Route::currentRouteName(), ['admin.accident-inform-sheets.show-claim']) ? 'active' : '' }}">
                        {{ __('accident_informs.claim_detail') }}
                    </a>
                @endcan
            </div>
        @endif
    </div>
</div>
