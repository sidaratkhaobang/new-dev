<label class="{{ $label_class }}" for="{{ $id }}" class="{{ $label_class }}">{{ $label }}
    @if ($required)
        <span class="text-danger">*</span>
    @endif
</label>
<div class="input-group">
    <input type="{{ $type }}" class="{{ $input_class }}" id="{{ $id }}" name="{{ $id }}"
        placeholder="{{ $placeholder }}" data-enable-time="true" data-no-calendar="true" data-date-format="H:i"
        data-time_24hr="true" readonly="readonly" {{ $required ? 'required' : '' }} value="{{ $value }}">
    <span class="input-group-text">
        <i class="far fa-calendar-check"></i>
    </span>
</div>
