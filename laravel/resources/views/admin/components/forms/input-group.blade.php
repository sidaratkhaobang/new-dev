<label class="{{ $label_class }}" for="{{ $id }}" class="{{ $label_class }}">{{ $label }}
    @if ($required)
        <span class="text-danger">*</span>
    @endif
</label>
<div class="input-group input-group-custom">
    @if ($prefix)
        <span class="input-group-text input-group-text-custom">{{ $prefix }}</span>
    @endif
    <input type="{{ $type }}" class="form-control {{ $input_class }}" id="{{ $id }}"
        name="{{ $id }}" placeholder="{{ $placeholder }}" maxlength="{{ $maxlength }}" oninput="{{ $oninput }}" min="{{$min}}"
        {{ $required ? 'required' : '' }} value="{{ $value }}">
    @if ($suffix)
        <span class="input-group-text input-group-text-custom">{{ $suffix }}</span>
    @endif
</div>