<div class="form-group row">
    <label class="{{ $label_class }}">{{ $label }}</label>
    <div class="{{ $input_class }}">
        <div class="row">
            @foreach ($list as $key => $item)
                <div class="col-sm-6">
                    <div class="custom-control custom-checkbox custom-control-inline custom-control-primary">
                        <input type="radio" class="custom-control-input" id="{{ $id . '_' . $key }}" 
                            name="{{ $id }}[]" @if ($value && in_array($item['value'], $value)) checked @endif 
                            value="{{ $item['value'] }}">
                        <label class="custom-control-label" for="{{ $id . '_' . $key }}">{{ $item['name'] }}</label>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>
