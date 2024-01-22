<h4>{{ __('short_term_rentals.location_detail') }}</h4>
<hr>
<div id="location" v-cloak data-detail-uri="" data-title="">
    <div class="table-wrap">
        <table class="table table-striped">
            <thead class="bg-body-dark">
                <th>#</th>
                <th>{{ __('short_term_rentals.location') }}</th>
                <th>{{ __('short_term_rentals.location_description') }}</th>
                <th class="sticky-col text-center">{{ __('lang.tools') }}</th>
            </thead>
            <tbody v-if="location_list.length > 0">
                <tr v-for="(item, index) in location_list">
                    <td>@{{ index + 1 }}</td>
                    <td>@{{ item.location_text }}</td>
                    <td>@{{ item.location_description }}</td>
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
                                        <a class="dropdown-item" v-on:click="editLocation(index)"><i
                                                class="far fa-edit me-1"></i> แก้ไข</a>
                                        <a class="dropdown-item btn-delete-row" v-on:click="removeLocation(index)"><i
                                                class="fa fa-trash-alt me-1"></i> ลบ</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </td>
                    <input type="hidden" v-bind:name="'location['+ index+ '][id]'" v-bind:value="item.id">
                    <input type="hidden" v-bind:name="'location['+ index+ '][location_id]'" id="location_id"
                        v-bind:value="item.location_id">
                    <input type="hidden" v-bind:name="'location['+ index+ '][location_description]'"
                        id="location_description" v-bind:value="item.location_description">
                </tr>
            </tbody>
            <tbody v-else>
                <tr class="table-empty">
                    <td class="text-center" colspan="5">“
                        {{ __('lang.no_list') . __('short_term_rentals.location_detail') }} “</td>
                </tr>
            </tbody>
        </table>
    </div>
    <div class="row">
        <div class="col-md-12 text-end">
            <button type="button" class="btn btn-primary" onclick="addLocation()"
                >{{ __('lang.add') }}</button>
        </div>
    </div>
</div>
<br>
@include('admin.short-term-rental-info.modals.location-modal')
