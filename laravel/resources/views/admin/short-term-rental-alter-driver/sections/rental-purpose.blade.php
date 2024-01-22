<h4 class="grey-text">{{ __('short_term_rentals.description_other') }}</h4>
<hr>
<div class="row push mb-3">
    <div class="col-sm-4">
        @if (isset($view))
            <p class="grey-text mt-1">{{ __('short_term_rentals.objective') }}</p>
            <p>{{ $d->objective ?? '-' }}</p>
        @else
            <x-forms.input-new-line id="objective" :value="$d->objective" :label="__('short_term_rentals.objective')" />
        @endif
    </div>
    <div class="col-sm-6">
        @if (isset($view))
            <p class="grey-text mt-1">{{ __('short_term_rentals.remark') }}</p>
            <p>{{ $d->remark ?? '-' }}</p>
        @else
            <x-forms.input-new-line id="rental_remark" :value="$d->remark" :label="__('short_term_rentals.remark')" />
        @endif
    </div>
</div>