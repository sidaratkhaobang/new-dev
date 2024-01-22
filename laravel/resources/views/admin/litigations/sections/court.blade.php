<div class="block {{ __('block.styles') }}">
    @include('admin.components.block-header', [
        'text' => __('litigations.court_detail'),
    ])
    <div class="block-content">
        <div class="row push">
            <div class="col-sm-3">
                <x-forms.date-input id="court_filing_date" :value="$d->court_filing_date" :label="__('litigations.sue_date')"/>
            </div>
            <div class="col-sm-3">
                <x-forms.input-new-line id="location_name" :value="$d->location_name" :label="__('litigations.court_name')" />
            </div>
            <div class="col-sm-3">
                <x-forms.input-new-line id="black_number" :value="$d->black_number" :label="__('litigations.black_number')" />
            </div>
            <div class="col-sm-3">
                <x-forms.input-new-line id="red_number" :value="$d->red_number" :label="__('litigations.red_number')" />
            </div>
        </div>
        <div class="row push">
            <div class="col-sm-3">
                <x-forms.input-new-line id="age" :value="$d->age" :label="__('litigations.age')" />
            </div>
            <div class="col-sm-3">
                <x-forms.input-new-line id="remark" :value="$d->remark" :label="__('litigations.remark')" />
            </div>
            <div class="col-sm-3">
                <x-forms.date-input id="due_date" :value="$d->due_date" :label="__('litigations.due_date')"/>
            </div>
        </div>
    </div>
</div>
