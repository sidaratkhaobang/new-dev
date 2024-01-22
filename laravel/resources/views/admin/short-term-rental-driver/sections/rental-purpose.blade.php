<x-blocks.block :title="'รายละเอียดการเดินทาง'" >
    <div class="row push">
        <div class="col-sm-12">
            @if (isset($view))
                <p class="grey-text mt-1">{{ __('short_term_rentals.objective') }}</p>
                <p>{{ $d->objective ?? '-' }}</p>
            @else
                <x-forms.text-area-new-line id="objective" :value="$rental->objective"
                    :label="__('short_term_rentals.objective')"/>
            @endif
        </div>
    </div>
    <div class="row">
        <div class="col-sm-12">
            @if (isset($view))
                <p class="grey-text mt-1">{{ __('short_term_rentals.remark') }}</p>
                <p>{{ $d->remark ?? '-' }}</p>
            @else
                <x-forms.text-area-new-line id="rental_remark" :value="$rental->remark"
                    :label="__('short_term_rentals.remark')"/>
            @endif
        </div>
    </div>
</x-blocks.block>