<div id="wage-job" v-cloak data-detail-uri="" data-title="">
    <div class="table-wrap">
        <table class="table table-striped">
            <thead class="bg-body-dark">
                <th style="width: 2px;">#</th>
                <th>{{ __('driving_jobs.driver_wage') }}</th>
                <th>{{ __('driving_jobs.remark') }}</th>
                <th>{{ __('driving_jobs.amount') }}</th>
                <th class="sticky-col text-center">{{ __('lang.tools') }}</th>
            </thead>
            <tbody v-if="driver_wage_job_list.length > 0">
                <tr v-for="(item, index) in driver_wage_job_list">
                    <td>@{{ index + 1 }}</td>
                    <td>@{{ item.driver_wage_text }}</td>
                    <td>@{{ item.remark }}</td>
                    <td>@{{ convertNumberToFloat(item.amount) }} @{{ convertTextAmountType(item.amount_type) }}</td>
                    <td class="sticky-col text-center">
                        <div class="btn-group" v-if="status">
                            <div class="col-sm-12">
                                <div class="dropdown dropleft">
                                    <button type="button" class="btn btn-sm btn-alt-secondary dropdown-toggle"
                                        id="dropdown-dropleft-dark" data-bs-toggle="dropdown" aria-haspopup="true"
                                        aria-expanded="false">
                                        <i class="fa fa-ellipsis-vertical"></i>
                                    </button>
                                    <div class="dropdown-menu" aria-labelledby="dropdown-dropleft-dark">
                                        <a class="dropdown-item" v-on:click="editWageJob(index)"><i
                                                class="far fa-edit me-1"></i> แก้ไข</a>
                                        <a class="dropdown-item btn-delete-row" v-on:click="removeWageJob(index)"><i
                                                class="fa fa-trash-alt me-1"></i> ลบ</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </td>
                    <input type="hidden" v-bind:name="'wage_job['+ index+ '][id]'" v-bind:value="item.id">
                    <input type="hidden" v-bind:name="'wage_job['+ index+ '][driver_wage_id]'" id="driver_wage_id"
                        v-bind:value="item.driver_wage_id">
                    <input type="hidden" v-bind:name="'wage_job['+ index+ '][remark]'" id="remark"
                        v-bind:value="item.remark">
                    <input type="hidden" v-bind:name="'wage_job['+ index+ '][amount]'" id="amount"
                        v-bind:value="item.amount">
                    <input type="hidden" v-bind:name="'wage_job['+ index+ '][driver_wage_text]'" id="driver_wage_text"
                        v-bind:value="item.driver_wage_text">
                    <input type="hidden" v-bind:name="'wage_job['+ index+ '][amount_type]'" id="amount_type"
                        v-bind:value="item.amount_type">
                </tr>
            </tbody>
            <tbody v-else>
                <tr class="table-empty">
                    <td class="text-center" colspan="8">“
                        {{ __('lang.no_list') . __('driving_jobs.wage_job_table') }} “</td>
                </tr>
            </tbody>
        </table>
    </div>
    @if (!isset($view))
        <div class="row">
            <div class="col-md-12 text-end">
                <button type="button" class="btn btn-primary" onclick="addWageJob()"
                    id="openModal">{{ __('lang.add') }}</button>
            </div>
        </div>
    @endif
</div>

@include('admin.driving-jobs.modals.wage-job-modal')
