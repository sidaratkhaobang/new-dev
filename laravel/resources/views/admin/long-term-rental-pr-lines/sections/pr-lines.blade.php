<div class="row push">
    <div class="col-auto">
        <h4>{{ __('long_term_rentals.pr_line_detail') }}</h4>
    </div>
    @if (!isset($view_mode))
    <div class="col-auto">
            <a target="_blank" onclick="addLongTermRentalPRLine()" class="btn btn-primary">{{ __('long_term_rentals.add_one_line') }}</a>
        </div>
        <div class="col-auto">
            <a target="_blank" onclick="addAllLongTermRentalPRLine()" class="btn btn-primary">{{ __('long_term_rentals.add_all_line') }}</a>
        </div>
    @endif
</div>
<hr>
@include('admin.long-term-rental-pr-lines.modals.pr-lines')
<div class="mb-5" id="lt-pr-line" v-cloak data-detail-uri="" data-title="">
    <table class="table table-striped">
        <thead class="bg-body-dark bg-body-border-line">
            <tr>
                <th>#</th>
                <th>{{ __('long_term_rentals.car_class_and_color') }}</th>
                <th>{{ __('long_term_rentals.car_amount') }}</th>
                <th>{{ __('long_term_rentals.rental_duration') }}</th>
                <th>{{ __('long_term_rentals.approved_rental_file') }}</th>
                <th>{{ __('lang.remark') }}</th>

                <th v-if="!view_mode">{{ __('lang.tools') }}</th>
            </tr>
        </thead>
        <tbody v-if="lt_pr_line_list.length > 0">
            <tr v-for="(item, index) in lt_pr_line_list">
                <td>@{{ index + 1 }}</td>
                <td>@{{ item.lt_line_text }}</td>
                <td>@{{ item.amount }}</td>
                <td>@{{ item.month_text }}</td>
                <td>
                    <div v-if="getFilesPendingCount(item.approved_rental_files) > 0">
                        <p class="m-0">{{ __('long_term_rentals.approved_rental_file') }} : @{{ getFilesPendingCount(item.approved_rental_files) }}
                            {{ __('lang.file') }}
                        </p>
                    </div>
                    <div v-if="item.approved_rental_files">
                        <div v-for="(approved_rental_file, index) in item.approved_rental_files">
                            <div v-if="approved_rental_file.saved">
                                <a target="_blank" v-bind:href="approved_rental_file.url"><i
                                        class="fa fa-download text-primary"></i>
                                        @{{ approved_rental_file.name }}</a>
                            </div>
                        </div>
                    </div>
                </td>
                <td>@{{ item.remark }}</td>
                <td v-if="!view_mode" class="sticky-col text-center">
                    @include('admin.components.dropdown-action-vue')
                </td>
                <input type="hidden" v-bind:name="'lt_pr_lines[' + index + '][id]'" v-bind:value="item.id">
                <input type="hidden" v-bind:name="'lt_pr_lines[' + index + '][lt_rental_line_id]'" v-bind:value="item.lt_line">
                <input type="hidden" v-bind:name="'lt_pr_lines[' + index + '][lt_rental_month_id]'" v-bind:value="item.month">
                <input type="hidden" v-bind:name="'lt_pr_lines[' + index + '][amount]'" v-bind:value="item.amount">
                {{-- <input type="hidden" v-bind:name="'lt_pr_lines[' + index + '][approved_rental_files]'" v-bind:value="item.approved_rental_files"> --}}
                <input type="hidden" v-bind:name="'lt_pr_lines[' + index + '][remark]'" v-bind:value="item.remark">
            </tr>
        </tbody>
        <tbody v-else>
            <tr class="table-empty">
                <td class="text-center" colspan="7">
                    " {{ __('lang.no_list') . __('long_term_rentals.pr_line_detail') }} "
                </td>
            </tr>
        </tbody>
    </table>
    <div v-for="(item) in pending_delete_lt_pr_line_ids">
        <input type="hidden" v-bind:name="'pending_delete_lt_pr_line_ids[]'"
            v-bind:value="item">
    </div>
</div>