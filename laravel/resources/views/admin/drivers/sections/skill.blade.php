<h4>{{ __('drivers.driver_skill_table') }}</h4>
<hr>
<div id="driver-skill" v-cloak data-detail-uri="" data-title="">
    <div class="table-wrap">
        <table class="table table-striped">
            <thead class="bg-body-dark">
                <th style="width: 2px;">#</th>
                <th>{{ __('drivers.skill_name') }}</th>
                <th>{{ __('drivers.skill') }}</th>
                @if (!isset($view))
                    <th class="sticky-col text-center">{{ __('lang.tools') }}</th>
                @endif
            </thead>
            <tbody v-if="driver_skill_list.length > 0">
                <tr v-for="(item, index) in driver_skill_list">
                    <td>@{{ index + 1 }}</td>
                    <td>@{{ item.driving_skill_text }}</td>
                    <td>
                        <div v-if="getFilesPendingCount(item.skill_files) > 0">
                            <p class="m-0">{{ __('drivers.pending_file') }} : @{{ getFilesPendingCount(item.skill_files) }}
                                {{ __('lang.file') }}</p>
                        </div>
                        <div v-if="item.skill_files">
                            <div v-for="(skill_file, index) in item.skill_files">
                                <div v-if="skill_file.saved">
                                    <a target="_blank" v-bind:href="skill_file.url"><i
                                            class="fa fa-download text-primary"></i>
                                        @{{ skill_file.name }}</a>
                                </div>
                            </div>
                        </div>
                    </td>
                    @if (!isset($view))
                        <td class="sticky-col text-center">
                            <div class="btn-group">
                                <div class="col-sm-12">
                                    <div class="dropdown dropleft">
                                        <button type="button" class="btn btn-sm btn-alt-secondary dropdown-toggle"
                                            id="dropdown-dropleft-dark" data-bs-toggle="dropdown" aria-haspopup="true"
                                            aria-expanded="false">
                                            <i class="fa fa-ellipsis-vertical"></i>
                                        </button>
                                        <div class="dropdown-menu" aria-labelledby="dropdown-dropleft-dark">
                                            <a class="dropdown-item" v-on:click="editDriverSkill(index)"><i
                                                    class="far fa-edit me-1"></i> แก้ไข</a>
                                            <a class="dropdown-item btn-delete-row"
                                                v-on:click="removeDriverSkill(index)"><i
                                                    class="fa fa-trash-alt me-1"></i> ลบ</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </td>
                    @endif
                    <input type="hidden" v-bind:name="'driver_skill['+ index+ '][id]'" v-bind:value="item.id">
                    <input type="hidden" v-bind:name="'driver_skill['+ index+ '][driving_skill_id]'"
                        id="driving_skill_id" v-bind:value="item.driving_skill_id">
                </tr>
            </tbody>
            <tbody v-else>
                <tr class="table-empty">
                    <td class="text-center" colspan="8">“
                        {{ __('lang.no_list') . __('drivers.driver_skill_table') }} “</td>
                </tr>
            </tbody>
        </table>
    </div>
    @if (!isset($view))
        <div class="row">
            <div class="col-md-12 text-end">
                <button type="button" class="btn btn-primary" onclick="addDriverSkill()"
                    id="openModal">{{ __('lang.add') }}</button>
            </div>
        </div>
    @endif
</div>
<br>
@include('admin.drivers.modals.driver-skill-modal')
