<div class="form-group row">
    <label class="{{ $label_class }}">{{ $label }}
        @if ($required)
            <span class="text-danger">*</span>
        @endif
    </label>
    <div class="{{ $input_class }}">
        @foreach ($list as $item)
            <div class="form-check form-check-inline mt-1">
                <input type="radio" class="form-check-input" id="{{ $id }}{{ $item['value'] }}"
                    data-name="{{ $item['name'] }}" name="{{ $id }}" value="{{ $item['value'] }}"
                    @if ($value == $item['value']) checked @endif>
                <label class="form-check-label"
                    for="{{ $id }}{{ $item['value'] }}">{{ $item['name'] }}</label>
            </div>
        @endforeach
    </div>
</div>
