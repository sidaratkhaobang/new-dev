<div class="row push">
    <label class="{{ $label_class }}">{{ $label }}
        @if ($required)
            <span class="text-danger">*</span>
        @endif
    </label>
    <div class="{{ $input_class }}">
        @foreach ($list as $item)
            <div class="custom-control custom-radio custom-control custom-control-primary mb-2">
                <input type="radio" class="custom-control-input" id="{{ $id }}{{ $item['value'] }}"
                    data-name="{{ $item['name'] }}" name="{{ $id }}" value="{{ $item['value'] }}"
                    @if ($model) v-model="{{$model}}" @endif
                    @if ($value == $item['value']) checked @endif>
                <label class="custom-control-label"
                    for="{{ $id }}{{ $item['value'] }}">{{ $item['name'] }}</label>
            </div>
        @endforeach
    </div>
</div>
