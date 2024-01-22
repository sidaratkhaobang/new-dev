<div id="branch-locations">
    <div class="table-wrap">
        <table class="table table-striped">
            <thead class="bg-body-dark">
                <th>#</th>
                <th>{{ __('branches.location_group') }}</th>
                <th>{{ __('branches.location') }}</th>
                <th class="text-center">{{ __('branches.can_origin') }}</th>
                <th class="text-center">{{ __('branches.can_stopover') }}</th>
                <th class="text-center">{{ __('branches.can_destination') }}</th>
            </thead>
            @if (sizeof($branch_location_list) > 0)
                @foreach ($branch_location_list as $index => $item)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $item->location_group_text }}</td>
                        <td>{{ $item->location_text }}</td>
                        <td class="text-center">
                            <span
                                class="badge larger-badge badge-pill bg-{{ $item->can_origin == 1 ? 'success' : 'secondary' }} text-white">{{ $item->can_origin == 1 ? __('lang.yes') : __('lang.no') }}</span>
                        </td>
                        <td class="text-center">
                            <span
                                class="badge larger-badge badge-pill bg-{{ $item->can_stopover == 1 ? 'success' : 'secondary' }} text-white">{{ $item->can_stopover == 1 ? __('lang.yes') : __('lang.no') }}</span>
                        </td>
                        <td class="text-center">
                            <span
                                class="badge larger-badge badge-pill bg-{{ $item->can_destination == 1 ? 'success' : 'secondary' }} text-white">{{ $item->can_destination == 1 ? __('lang.yes') : __('lang.no') }}</span>
                        </td>
                    </tr>
                @endforeach
            @else
                <tbody>
                    <tr class="table-empty">
                        <td class="text-center" colspan="6">" {{ __('lang.no_list') }} "</td>
                    </tr>
                </tbody>
            @endif
        </table>
    </div>
</div>
