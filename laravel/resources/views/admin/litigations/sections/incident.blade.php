<div class="block {{ __('block.styles') }}">
    @include('admin.components.block-header', [
        'text' => __('litigations.incident_detail'),
    ])
    <div class="block-content">
        <div class="row push">
            <div class="col-sm-3">
                <x-forms.select-option id="location_case" :value="$d->location_case" :list="$location_name_list" :label="__('litigations.location_case')"
                    :optionals="['required' => true]"  />
            </div>
        </div>
        <div class="row push">
            <div class="col-sm-12">
                <x-forms.text-area-new-line id="details" :value="$d->details" :label="__('litigations.detail_case')" 
                    :optionals="['placeholder' => __('lang.input.placeholder'), 'row' => 5]"/>
            </div>
        </div>
        @if (!empty($d->location_case))
        <div class="row push">
            <div class="col-sm-6">
                <x-forms.upload-image :id="'litigation_files'" :label="__('litigations.litigation_file')" />
            </div>
            <div class="col-sm-3">
                <x-forms.date-input id="request_date" :value="$d->request_date" :label="__('litigations.request_date')"/>
            </div>
            <div class="col-sm-3">
                <x-forms.date-input id="receive_date" :value="$d->receive_date" :label="__('litigations.receive_date')"/>
            </div>
        </div>
        @endif
    </div>
</div>
