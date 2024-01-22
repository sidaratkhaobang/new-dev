<label class="{{ $label_class }}">
    {{ $label }}
    @if ($required)
        <span class="text-danger">*</span>
    @endif
</label>
<div class="{{ $input_class }}">
    @foreach ($list as $item)
        <div class="form-check form-check-inline">
            <input class="form-check-input" type="radio" id="{{ $id }}{{ $item['value'] }}"
                data-name="{{ $item['name'] }}" name="{{ $id }}" value="{{ $item['value'] }}"
                @if ($model) v-model="{{ $model }}" @endif
                @if ($value == $item['value']) checked @endif>
            <label class="form-check-label" for="{{ $id }}{{ $item['value'] }}">
                {{ $item['name'] }}
            </label>
        </div>
    @endforeach
</div>
