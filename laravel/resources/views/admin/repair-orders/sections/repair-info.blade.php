<div class="block {{ __('block.styles') }}">
    <div class="block-content">
        <div class="d-md-flex justify-content-md-between align-items-md-center mb-3">
            <div>
                <h4><i class="fa fa-file-lines me-1"></i>{{ __('repairs.repair_table') }}</h4>
            </div>
        </div>
        <div class="row push mb-4">
            <div class="col-sm-3">
                <x-forms.select-option id="repair_no" :list="$repair_no_list" :value="$d->repair_id" :label="__('repairs.worksheet_no')"
                    :optionals="['required' => true]" />
            </div>
            <div class="col-sm-3">
                <x-forms.input-new-line id="repair_type" :value="$d->repair ? __('repairs.repair_type_' . $d->repair->repair_type) : null" :label="__('repairs.repair_type')" />
            </div>
            <div class="col-sm-3">
                <x-forms.input-new-line id="alert_date" :value="$d->repair ? date('d/m/Y H:i', strtotime($d->repair->repair_date)) : null" :label="__('repairs.repair_date')" />
            </div>
            <div class="col-sm-3">
                <x-forms.input-new-line id="repair_create_by" :value="$d->repair && $d->repair->createdBy ? $d->repair->createdBy->name : null" :label="__('repair_orders.repair_create_by')" />
            </div>
        </div>
        <div class="row push mb-4">
            <div class="col-sm-3">
                <x-forms.input-new-line id="mileage" :value="$d->repair ? $d->repair->mileage : null" :label="__('repairs.mileage')" />
            </div>
            <div class="col-sm-3">
                <x-forms.input-new-line id="place" :value="$d->repair ? $d->repair->place : null" :label="__('repairs.place')" />
            </div>
            <div class="col-sm-6">
                <x-forms.input-new-line id="remark" :value="$d->repair ? $d->repair->remark : null" :label="__('repairs.remark')" />
            </div>
        </div>
        <div class="row push mb-4">
            <div class="col-sm-3">
                <x-forms.upload-image :id="'repair_documents'" :label="__('repairs.document')" />
            </div>
        </div>
        <div class="d-md-flex justify-content-md-between align-items-md-center mb-3">
            <div class="col-sm-6">
                <h4><i class="fa fa-file-lines me-1"></i>
                    {{ __('repairs.description_repair_table') }}</h4>
            </div>
        </div>
        <div id="repair-line" v-cloak data-detail-uri="" data-title="">
            <div class="table-wrap">
                <table class="table table-striped">
                    <thead class="bg-body-dark">
                        <th>{{ __('repairs.date') }}</th>
                        <th>{{ __('repairs.description') }}</th>
                        <th>{{ __('repairs.check') }}</th>
                        <th>{{ __('repairs.qc') }}</th>
                    </thead>
                    <tbody v-if="repair_line.length > 0">
                        <template v-for="(item, index) in repair_line">
                            <tr>
                                <td>@{{ formatDate(item.date) }}</td>
                                <td>@{{ item.description }}</td>
                                <td>@{{ item.check_text }}</td>
                                <td>@{{ item.qc }}</td>
                            </tr>
                        </template>
                    </tbody>
                    <tbody v-else>
                        <tr class="table-empty">
                            <td class="text-center" colspan="5">"
                                {{ __('lang.no_list') . __('repairs.description_repair_table') }} "</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
