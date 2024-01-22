<h4>{{ __('short_term_rentals.package_detail') }}</h4>
<hr>
<div class="row push mb-4">
    <div class="col-sm-3">
        <x-forms.checkbox-inline id="is_permanent_disabled" :list="[
            [
                'id' => 1,
                'name' => __('parking_lots.is_permanent_disabled'),
                'value' => 1,
            ],
        ]" :label="null" :value="null" />
    </div>
</div>
