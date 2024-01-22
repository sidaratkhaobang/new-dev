<h4>{{ __('long_term_rentals.spec_header') }}</h4>
<hr>
<div class="row push mb-4">
    <div class="col-sm-12">
        <x-forms.radio-inline id="is_spec" :value="$d->is_spec" :list="$yes_no_list" :label="__('long_term_rentals.require_spec')"
                              :optionals="['required' => true]"/>
    </div>
</div>
<div class="row push mb-4">
    <div class="col-sm-12" style="@if($d->is_spec == STATUS_ACTIVE) display:none; @endif">
        @include('admin.long-term-rental-spec-tors.sections.car-accessory')
    </div>
</div>

