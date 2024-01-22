<x-blocks.block :title="__('short_term_rentals.rental_detail')" :optionals="['is_toggle' => false]">
    <div class="form-group row push">
        <div class="col-sm-3">
            <x-forms.date-input id="pickup_date" :value="$d->pickup_date" :label="__('short_term_rentals.pickup_datetime')" :optionals="['date_enable_time' => true, 'placeholder' => __('lang.select_date'), 'required' => true]" />
        </div>
        <div class="col-sm-3 offset-3">
            <x-forms.date-input id="return_date" :value="$d->return_date" :label="__('short_term_rentals.return_datetime')" :optionals="['date_enable_time' => true, 'placeholder' => __('lang.select_date'), 'required' => true]" />
        </div>
    </div>
    <div class="form-group row push">
        {{-- origin --}}
        <div class="col-sm-6">
            <div class="row gx-3">
                @php
                $origin_class_name = 'col-sm-10';
                if(isset($edit_rental)){
                $origin_class_name = 'col-sm-12';
                }
                @endphp

                <div class="{{ $origin_class_name }}">
                    <label class="text-start col-form-label" for="origin_id">
                        {{ __('short_term_rentals.origin') }}<span class="text-danger">*</span>
                    </label>
                    <select name="origin_id" id="origin_id" class="form-control js-select2-default" style="width: 100%;">
                        @if (!empty($d->origin_id))
                        <option value="{{ $d->origin_id }}">{{ $origin_name }}</option>
                        @endif
                    </select>
                </div>
                @if(!isset($edit_rental))
                <div class="col-sm-2 align-self-end px-0">
                    <button type="button" class="btn btn-primary btn-mini" onclick="openOriginModal()">
                        <i class="fa fa-circle-plus"></i>
                    </button>
                </div>
                @endif
            </div>
            <x-forms.hidden id="origin_lat" :value="$d->origin_lat" />
            <x-forms.hidden id="origin_lng" :value="$d->origin_lng" />
            <x-forms.hidden id="origin_name" :value="$d->origin_name" />
            <x-forms.hidden id="origin_address" :value="$d->origin_address" />
        </div>

        {{-- destination --}}
        <div class="col-sm-6">
            <div class="row gx-3">
                <div class="col-sm-10">
                    <label class="text-start col-form-label" for="destination_id">
                        {{ __('short_term_rentals.destination') }}<span class="text-danger">*</span>
                    </label>
                    <select name="destination_id" id="destination_id" class="form-control js-select2-default" style="width: 100%;">
                        @if (!empty($d->destination_id))
                        <option value="{{ $d->destination_id }}">{{ $destination_name }}</option>
                        @endif
                    </select>
                </div>
                <div class="col-sm-2 align-self-end px-0">
                    <button type="button" class="btn btn-primary btn-mini" onclick="openDestinationModal()">
                        <i class="fa fa-circle-plus"></i>
                    </button>
                </div>
            </div>
            <x-forms.hidden id="destination_lat" :value="$d->destination_lat" />
            <x-forms.hidden id="destination_lng" :value="$d->destination_lng" />
            <x-forms.hidden id="destination_name" :value="$d->destination_name" />
            <x-forms.hidden id="destination_address" :value="$d->destination_address" />
        </div>
    </div>
    <div class="form-group row push">
        <div class="col-sm-6">
            <x-forms.text-area-new-line id="origin_remark" :value="$d->origin_remark" :label="__('short_term_rentals.origin_remark')" />
        </div>
        <div class="col-sm-6">
            <x-forms.text-area-new-line id="destination_remark" :value="$d->destination_remark" :label="__('short_term_rentals.destination_remark')" />
        </div>
    </div>
    <div class="form-group row">
        <div class="col-sm-3">
            <x-forms.input-new-line id="avg_distance" :value="$d->avg_distance" :label="__('short_term_rentals.avg_distance')" :optionals="['type' => 'number']" />
        </div>
    </div>
</x-blocks.block>
@include('admin.short-term-rental-info.modals.origin')
@include('admin.short-term-rental-info.modals.destination')