<div class="row push mb-4">
    <div class="col-sm-4">
        @if (isset($view))
            <x-forms.view-image :id="'tor_file'" :label="__('long_term_rentals.tor_file')" :list="$tor_files"/>
        @else
            <x-forms.upload-image :id="'tor_file'" :label="__('long_term_rentals.tor_file')" />
        @endif

    </div>
    <div class="col-sm-4">
        @if (isset($view))
            <x-forms.view-image :id="'rental_file'" :label="__('long_term_rentals.rental_file')" :list="$rental_files" />
        @else
            <x-forms.upload-image :id="'rental_file'" :label="__('long_term_rentals.rental_file')" />
        @endif

    </div>
</div>
    {{-- <div class="row push mb-4">
        <div class="col-sm-3">
            <x-forms.radio-inline id="check_spec" :value="$d->check_spec" :list="$check_spec_list" :label="__('long_term_rentals.check_spec')" />
        </div>
        <div class="col-sm-3" id="rental_tor"
        @if (strcmp($d->check_spec, BOOL_TRUE) == 0) style="display: block;"
        @else
        style="display: none;" @endif>
            <x-forms.select-option id="bom_id" :value="$d->bom_id" :list="$spec_bom_list" :label="null" />
        </div>
    </div> --}}
<br>
