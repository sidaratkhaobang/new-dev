<div class="form-check form-block form-block-custom form-block-custom--checkbox">
    <input type="checkbox" class="form-check-input" id="{{ $id }}" name="{{ $name }}" value="{{ $value }}" {{ (strcmp($value, $selected) == 0 ? 'checked' : '') }} >
    <label class="form-check-label" for="{{ $id }}">
        {{ $slot }}
    </label>
</div>