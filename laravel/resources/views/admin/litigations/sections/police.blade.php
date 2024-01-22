<div class="block {{ __('block.styles') }}">
    @include('admin.components.block-header', [
        'text' => __('litigations.police_detail'),
    ])
    <div class="block-content">
        <div class="row mb-4">
            <div class="col-sm-3">
                <x-forms.date-input id="court_filing_date" :value="$d->court_filing_date" :label="__('litigations.police_filing_date')"/>
            </div>
            <div class="col-sm-6">
                <x-forms.input-new-line id="location_name" :value="$d->location_name" :label="__('litigations.police_station')" />
            </div>
            <div class="col-sm-3">
                <x-forms.input-new-line id="inquiry_official" :value="$d->inquiry_official" :label="__('litigations.inquiry_official')" />
            </div>
        </div>
        <div class="row mb-4">
            <div class="col-sm-3">
                <x-forms.input-new-line id="inquiry_official_tel" :value="$d->inquiry_official_tel" :label="__('litigations.inquiry_official_tel')" />
            </div>
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
