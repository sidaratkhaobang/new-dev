<div id="index-table" class="block {{ __('block.styles') }}">
    @include('admin.components.block-header', [
           'text' => __('insurance_deduct.title_content_accident'),
       ])
    <div class="block-content">
        <div class="justify-content-between mb-4">
            <div class="row push mb-4">
                <div class="col-sm-3">
                    <x-forms.label id="accident" :value=" $d?->accident_date?get_date_time_by_format($d?->accident_date, 'd/m/Y H:i'):'-'"
                                   :label="__('insurance_deduct.accident_datetime')"/>
                </div>
                <div class="col-sm-3">
                    <x-forms.label id="accident_wrong_type"
                                   :value="__('accident_informs.mistake_'.$d->wrong_type) ?? '-'"
                                   :label="__('insurance_deduct.wrong_type')"/>
                </div>
                <div class="col-sm-3">
                    <x-forms.label id="accident_claim_no" :value="$d?->claim_no ?? '-'"
                                   :label="__('insurance_deduct.claim_no')"/>
                </div>
            </div>
            <div class="row push mb-4">
                <div class="col-sm-3">
                    <x-forms.label id="accident_case" :value="__('accident_informs.case_'.$d?->case) ?? '-'"
                                   :label="__('insurance_deduct.accident_case')"/>
                </div>
                <div class="col-sm-3">
                    <x-forms.label id="accident_description" :value="$d?->accident_description ?? '-'"
                                   :label="__('insurance_deduct.accident_description')"/>
                </div>
                <div class="col-sm-3">
                    <x-forms.label id="accident_driver" :value="$d?->driver ?? '-'"
                                   :label="__('insurance_deduct.name_driver')"/>
                </div>
                <div class="col-sm-3">
                    <x-forms.label id="accident_place" :value="$d?->accident_place ?? '-'"
                                   :label="__('insurance_deduct.accident_place')"/>
                </div>
            </div>
        </div>
    </div>
</div>
