<label class="text-start col-form-label" for="period_date">{{ $label }}</label>
<div class="form-group">
    <div class="input-daterange input-group" data-week-start="1" data-autoclose="true" data-today-highlight="true">
        <input type="text" data-date-format="Y-m-d" class="js-flatpickr form-control flatpickr-input" id="{{ $start_id }}" name="{{ $start_id }}" value="{{ $start_value }}" placeholder="{{ __('lang.select_date') }}" data-week-start="1" data-autoclose="true" data-today-highlight="true">
        <div class="input-group-prepend input-group-append">
            <span class="input-group-text font-w600">
                <i class="fa fa-fw fa-arrow-right"></i>
            </span>
        </div>
        <input type="text" data-date-format="Y-m-d" class="js-flatpickr form-control flatpickr-input" id="{{ $end_id }}" name="{{ $end_id }}" value="{{ $end_value }}" placeholder="{{ __('lang.select_date') }}" data-week-start="1" data-autoclose="true" data-today-highlight="true">
    </div>
</div>