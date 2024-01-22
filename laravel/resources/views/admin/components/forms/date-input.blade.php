<label class="{{ $label_class }}" for="{{ $id }}" class="{{ $label_class }}">{{ $label }}
    @if ($required)
    <span class="text-danger" id="{{ $id }}_require">*</span>
    @endif
</label>
<div class="input-group">
    <input type="{{ $type }}" class="form-control {{ $input_class }}" id="{{ $id }}" name="{{ $id }}" placeholder="{{ $placeholder }}" @if ($date_enable_time) data-date-format="Y-m-d H:i" data-enable-time="true" data-time_24hr="true" @else data-date-format="Y-m-d" @endif {{ $required ? 'required' : '' }} value="{{ $value }}">
    <span class="input-group-text js-flatpickr-icon">
        <i class="icon-calendar"></i>
    </span>
</div>