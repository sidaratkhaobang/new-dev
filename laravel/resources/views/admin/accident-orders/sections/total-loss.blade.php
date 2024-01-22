<div class="block {{ __('block.styles') }}">
    @include('admin.components.block-header', [
        'text' => __('accident_orders.total_loss_car_detail'),
    ])
    <div class="block-content">
        <div class="row push mb-4">
            <div class="col-sm-3">
                @if (isset($view) || $gps_remove_stop_signal->id)
                    <x-forms.view-image :id="'total_loss_files'" :label="__('accident_orders.total_loss')" :list="$total_loss_files" />
                @else
                    <x-forms.upload-image :id="'total_loss_files'" :label="__('accident_orders.total_loss')" />
                @endif
            </div>
            <div class="col-sm-3">
                <x-forms.input-new-line id="compensation" :value="$d->compensation" :label="__('accident_orders.compensation')" :optionals="['input_class' => 'number-format']" />
            </div>
            <div class="col-sm-3">
                <x-forms.input-new-line id="carcass_cost" :value="$d->carcass_cost" :label="__('accident_orders.purchase_option')" :optionals="['input_class' => 'number-format']" />
            </div>
        </div>
        <div class="row push mb-4">
            <div class="col-sm-3">
                <x-forms.checkbox-inline id="is_stop_gps" :list="[
                    [
                        'id' => STATUS_ACTIVE,
                        'name' => __('accident_orders.noti_remove_stop_gps'),
                        'value' => STATUS_ACTIVE,
                    ],
                ]" :label="__('accident_orders.appointment')" :value="[$d->is_stop_gps]" />
            </div>
            <div class="col-sm-3"
                @if ($d->is_stop_gps && $gps_remove_stop_signal->worksheet_no) style="display: block;" @else  style="display: none;" @endif>
                <x-forms.label id="worksheet_no_gps" :value="$gps_remove_stop_signal->worksheet_no" :label="__('gps.worksheet_no_gps')" />
            </div>
            <div class="col-sm-3 gps"
                @if ($d->is_stop_gps) style="display: block;" @else  style="display: none;" @endif>
                <x-forms.date-input id="inform_date" :value="$gps_remove_stop_signal->inform_date" :label="__('accident_orders.noti_remove_stop_gps_date')" :optionals="['required' => true]" />
            </div>
            <div class="col-sm-3 gps"
                @if ($d->is_stop_gps) style="display: block;" @else  style="display: none;" @endif>
                <x-forms.radio-inline id="is_check_gps" :value="$gps_remove_stop_signal->is_check_gps" :list="[
                    ['name' => __('gps.remove_gps'), 'value' => STATUS_ACTIVE],
                    ['name' => __('gps.stop_gps'), 'value' => STATUS_INACTIVE],
                ]" :label="__('accident_orders.noti_remove_stop')"
                    :optionals="['required' => true]" />
            </div>
        </div>
        <div class="row push mb-4">
            <div class="col-sm-3">
                <x-forms.checkbox-inline id="is_status_rental_car" :list="[
                    [
                        'id' => STATUS_ACTIVE,
                        'name' => __('accident_orders.noti_contract_rental_status'),
                        'value' => STATUS_ACTIVE,
                    ],
                ]" :label="null"
                    :value="[$d->is_status_rental_car]" />
            </div>
            <div class="col-sm-3">
                <x-forms.checkbox-inline id="is_pick_up_book" :list="[
                    [
                        'id' => STATUS_ACTIVE,
                        'name' => __('accident_orders.noti_pick_up'),
                        'value' => STATUS_ACTIVE,
                    ],
                ]" :label="null"
                    :value="[$d->is_pick_up_book]" />
            </div>
        </div>
    </div>
</div>
