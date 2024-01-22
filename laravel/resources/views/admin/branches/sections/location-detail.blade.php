<div id="branch-locations" v-cloak data-detail-uri="" data-title="">
    <div class="table-wrap mb-3">
        <table class="table table-striped">
            <thead class="bg-body-dark">
                <th>#</th>
                <th>{{ __('branches.location_group') }}</th>
                <th>{{ __('branches.location') }}</th>
                <th class="text-center">{{ __('branches.can_origin') }}</th>
                <th class="text-center">{{ __('branches.can_stopover') }}</th>
                <th class="text-center"x>{{ __('branches.can_destination') }}</th>
                <th class="sticky-col text-center">{{ __('lang.tools') }}</th>
            </thead>
            <tbody v-if="branch_location_list.length > 0">
                <tr v-for="(item, index) in branch_location_list">
                    <td>@{{ index + 1 }}</td>
                    <td>@{{ item.location_group_text }}</td>
                    <td>@{{ item.location_text }}</td>
                    <td class="text-center">
                        <span class="badge larger-badge badge-pill text-white"
                            :class="{ 'bg-success': item.can_origin == 1, 'bg-secondary': item.can_origin == 0 }">
                            @{{ getYesNoText(item.can_origin) }}
                        </span>
                    </td>
                    <td class="text-center">
                        <span class="badge larger-badge badge-pill text-white"
                            :class="{ 'bg-success': item.can_stopover == 1, 'bg-secondary': item.can_stopover == 0 }">
                            @{{ getYesNoText(item.can_stopover) }}
                        </span>
                    </td>
                    <td class="text-center">
                        <span class="badge larger-badge badge-pill text-white"
                            :class="{ 'bg-success': item.can_destination == 1, 'bg-secondary': item.can_destination == 0 }">
                            @{{ getYesNoText(item.can_destination) }}
                        </span>
                    </td>
                    <td class="sticky-col text-center">
                        @include('admin.components.dropdown-action-vue')
                    </td>
                    <input type="hidden" v-bind:name="'branch_locations['+ index+ '][location_group_id]'"
                        id="location_group_id" v-bind:value="item.location_group_id">
                    <input type="hidden" v-bind:name="'branch_locations['+ index+ '][location_id]'" id="location_id"
                        v-bind:value="item.location_id">
                    <input type="hidden" v-bind:name="'branch_locations['+ index+ '][can_origin]'" id="can_origin"
                        v-bind:value="item.can_origin">
                    <input type="hidden" v-bind:name="'branch_locations['+ index+ '][can_stopover]'" id="can_stopover"
                        v-bind:value="item.can_stopover">
                    <input type="hidden" v-bind:name="'branch_locations['+ index+ '][can_destination]'"
                        id="can_destination" v-bind:value="item.can_destination">
                </tr>
            </tbody>
            <tbody v-else>
                <tr class="table-empty">
                    <td class="text-center" colspan="7">" {{ __('lang.no_list') }} "</td>
                </tr>
            </tbody>
        </table>
    </div>
    <div class="row">
        <div class="col-md-12 text-end">
            <button type="button" class="btn btn-primary"
                onclick="openBranchLocationModal()">{{ __('lang.add') }}</button>
        </div>
    </div>
    @include('admin.branches.modals.branch-location')
</div>
