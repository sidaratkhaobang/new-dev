<div class="row push">
    <div class="col-sm-2">
        <x-forms.input-new-line id="code" :value="$d->code" :label="__('branches.code')" :optionals="['required' => true]" />
    </div>
    <div class="col-sm-4">
        <x-forms.input-new-line id="name" :value="$d->name" :label="__('branches.name')" :optionals="['required' => true]" />
    </div>
    <div class="col-sm-3">
        <x-forms.radio-inline id="is_main" :value="$d->is_main" :list="$yes_no_list" :label="__('branches.is_main')"
            :optionals="['required' => true]" />
    </div>
    <div class="col-sm-3">
        <x-forms.radio-inline id="is_head_office" :value="$d->is_head_office" :list="$yes_no_list" :label="__('branches.is_head_office')"
            :optionals="['required' => true]" />
    </div>
</div>

<div class="row push">
    <div class="col-sm-4">
        <x-forms.input-new-line id="tax_no" :value="$d->tax_no" :label="__('branches.tax_no')" />
    </div>
    <div class="col-sm-4">
        <x-forms.input-new-line id="tel" :value="$d->tel" :label="__('branches.tel')" />
    </div>
    <div class="col-sm-4">
        <x-forms.input-new-line id="email" :value="$d->email" :label="__('branches.email')" />
    </div>
</div>
<div class="row push">
    <div class="col-sm-6">
        <x-forms.text-area-new-line id="address" :value="$d->address" :label="__('branches.address')" />
    </div>
    <div class="col-sm-3">
        <label class="text-start col-form-label" for="open_time">{{ __('branches.open') }}</label>
        <div class="input-group">
            <input type="text" class="js-flatpickr form-control flatpickr-input" id="open_time" name="open_time"
                data-enable-time="true" value="{{ $d->open_time }}" data-no-calendar="true" data-date-format="H:i"
                data-time_24hr="true" readonly="readonly" />
        </div>
    </div>
    <div class="col-sm-3">
        <label class="text-start col-form-label" for="close_time">{{ __('branches.open') }}</label>
        <div class="input-group">
            <input type="text" class="js-flatpickr form-control flatpickr-input" id="close_time" name="close_time"
                data-enable-time="true" value="{{ $d->close_time }}" data-no-calendar="true" data-date-format="H:i"
                data-time_24hr="true" readonly="readonly" />
        </div>
    </div>
</div>
<div class="row push">
    <div class="col-sm-3">
        <x-forms.input-new-line id="lat" :value="$d->lat" :label="__('locations.lat')" />
    </div>
    <div class="col-sm-3">
        <x-forms.input-new-line id="lng" :value="$d->lng" :label="__('locations.lng')" />
    </div>
    <div class="col-sm-3">
        <x-forms.input-new-line id="cost_center" :value="$d->cost_center" :label="__('branches.cost_center')" />
    </div>
</div>

<div class="row">
    <div class="col-sm-3">
        <x-forms.input-new-line id="document_prefix" :value="$d->document_prefix" :label="__('branches.document_prefix')" :optionals="['maxlength' => 2]" />
    </div>
    <div class="col-sm-3">
        <x-forms.input-new-line id="registered_code" :value="$d->registered_code" :label="__('branches.registered_code')" :optionals="['maxlength' => 2]" />
    </div>
</div>