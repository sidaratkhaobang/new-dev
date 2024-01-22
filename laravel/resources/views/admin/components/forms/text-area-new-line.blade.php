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
        <textarea class="form-control {{ (($html) ? $html_class : '') }}" id="{{ $id }}" name="{{ $id }}" rows="{{$row}}" placeholder="{{ $placeholder }}" {{ $readonly ? 'readonly' : '' }} >{{ $value }}</textarea>
    </div>
</div>
