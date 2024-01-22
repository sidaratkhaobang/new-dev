<div class="form-group row">
    <label class="{{ $label_class }}">{{ $label }}
        @if ($required)
        <span class="text-danger">*</span>
    @endif
</label>
    <div class="{{ $input_class }} mt-1">
        @foreach ($list as $key => $item)
            <div class="form-check form-check-inline mt-1">
                <input type="checkbox" class="form-check-input" id="{{ $id . '_' . $key }}" name="{{ $id }}[]"
                @if ($value && in_array($item['value'], $value)) checked @endif value="{{ $item['value'] }}">
                <label class="custom-control-label" for="{{ $id . '_' . $key }}">{{ $item['name'] }}</label>
            </div>
        @endforeach
    </div>
</div>