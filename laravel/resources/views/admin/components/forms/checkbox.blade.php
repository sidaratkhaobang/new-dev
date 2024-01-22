<div class="row push">
    <div class="{{ $label_class }}">
        <label>{{ $label }}
            @if ($required)
                <span class="text-danger">*</span>
            @endif
        </label>
        @if ($sub_label)
            <p class="text-gray-dark">{{ $sub_label }}</p>
        @endif
    </div>
    <div class="{{ $input_class }}">
        <div class="form-group">
            @foreach ($list as $key => $item)
                <div class="custom-control custom-checkbox custom-control-primary mb-2">
                    <input type="checkbox" class="custom-control-input " id="{{ $id . '_' . $key }}"
                        @if ($name) name="{{ $name }}[]" @else name="{{ $id }}[]" @endif value="{{ $item['value'] }}" @if ($value && in_array($item['value'], $value)) checked @endif>
                    <label class="custom-control-label" for="{{ $id . '_' . $key }}">{{ $item['name'] }}</label>
                </div>
            @endforeach
            @if ($extra_input)
                <input type="text" class="form-control col-sm-12 mt-2" id="extra_input" name="extra_input[{{ $list->last()->id }}]"
                    @if ($value && is_array(end($value)) && !empty(end($value))) value="{{ implode("", end($value)) }}" @endif>
            @endif
        </div>
    </div>
</div>

@push('scripts')
    <script>
        @if ($extra_input)
            $('#extra_input').prop('disabled', true);
            @if ($value && is_array(end($value)) && !empty(end($value)))
                $('#extra_input').prop('disabled', false);
            @endif
        
            $('[name="{{ $id }}[]"]:last').change(function () {
            if ($(this).is(":checked")) {
            $('#extra_input').prop('disabled', false);
            }else {
            $('#extra_input').prop('disabled', true);
            $('#extra_input').val('');
            }
            });
        @endif
    </script>
@endpush
