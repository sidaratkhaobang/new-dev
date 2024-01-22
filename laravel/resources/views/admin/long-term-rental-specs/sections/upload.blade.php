<h4>{{ __('long_term_rentals.upload_table') }}</h4>
<hr>
<div class="row push mb-4">
    <div class="col-sm-6">
        <x-forms.view-image :id="'tor_file'" :label="__('long_term_rentals.tor_file')" :list="$tor_files" />
    </div>
</div>
@if ($d->spec_status == SpecStatusEnum::REJECT)
    <div class="row push mb-4">
        <div class="col-sm-6">
            <span>{{ __('long_term_rentals.remark_reason') }}{{ $d->reject_spec_reason }}</span>
        </div>
    </div>
@endif
<br>
