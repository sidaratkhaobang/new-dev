<h4>{{ __('long_term_rentals.approval_info') }}</h4>
<hr>
<div class="row push mb-4">
    <div class="col-sm-3">
        <x-forms.select-option id="approve_status" :value="$d->status" :list="$approval_status_list" :label="__('lang.status')"
            :optionals="['required' => true]" />
    </div>
    <div class="col-sm-3">
        <x-forms.date-input id="require_date" name="require_date" :value="$d->require_date" :label="__('purchase_requisitions.require_date')"
            :optionals="['required' => true, 'placeholder' => __('lang.select_date')]" />
    </div>
    <div class="col-sm-6">
        @if (isset($view))
            <x-forms.view-image :id="'approved_rental_file'" :label="__('long_term_rentals.approved_rental_file')" :list="$approved_rental_files" />
        @else
            <x-forms.upload-image :id="'approved_rental_file'" :label="__('long_term_rentals.approved_rental_file')" />
        @endif
    </div>
</div>