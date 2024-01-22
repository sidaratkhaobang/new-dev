<div class="row push">
    <label for="{{ $id }}" class="{{ $label_class }}" >{{ $label }}
        @if($required)
        <span class="text-danger" >*</span>
        @endif
    </label>
    <div class="{{ $input_class }}">
        <input type="{{ $type }}" class="form-control" id="{{ $id }}" name="{{ $id }}" placeholder="{{ $placeholder }}" maxlength="{{ $maxlength }}" 
            {{ $required ? 'required' : '' }} 
            value="{{ $value }}"
        >
    </div>
</div>