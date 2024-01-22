<div class="{{ $form_class }}">
    <label class="{{ $label_class }}" for="{{ $id }}">{{ $label }}
        @if($required)
        <span class="text-danger" >*</span>
        @endif
        @if($sub_label)
        <p class="text-gray">{{ $sub_label }}</p>
        @endif
    </label>
    <div class="{{ $input_class }}">
        @if($id == 'tel')
        <textarea class="form-control {{ (($html) ? $html_class : '') }}" id="{{ $id }}" name="{{ $id }}" rows="4" placeholder="{{ $placeholder }}">{{ $value = str_replace(",", "\n", str_replace(array("[","]", "\""),"",$value ) ) }}</textarea>
        @else
        <textarea class="form-control {{ (($html) ? $html_class : '') }}" id="{{ $id }}" name="{{ $id }}" rows="4" placeholder="{{ $placeholder }}">{{ $value }}</textarea>
        @endif
    </div>
</div>