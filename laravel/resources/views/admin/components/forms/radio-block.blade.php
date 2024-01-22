<div class="form-check form-block form-block-custom h-100">
    <input type="radio" class="form-check-input" id="{{ $id }}" name="{{ $name }}" value="{{ $value }}" {{ (strcmp($value, $selected) == 0 ? 'checked' : '') }}>
    <label class="form-check-label h-100" for="{{ $id }}">
        {{ $slot }}
    </label>
</div>