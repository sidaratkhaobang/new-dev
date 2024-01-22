<div class="row mb-4">
    <div class="col-sm-3">
        <x-forms.select-option id="type" :list="$type_list" :value="$d?->type" :optionals="['required' => true]" :label="__('request_receipts.type')" />
    </div>
    <div class="col-sm-3">
        <x-forms.input-new-line id="title" :value="$d?->title" :optionals="['required' => true]" :label="__('request_receipts.title')" />
    </div>
</div>
<div class="row mb-4">
    <div class="col-sm-12">
        <x-forms.text-area-new-line id="detail" :value="$d->detail" :label="__('request_receipts.detail')" :optionals="['row' => 3]" />
    </div>
</div>
<div class="row mb-4">
    <div class="col-sm-3">
        @if (isset($view))
            <x-forms.view-image :id="'optional_files'" :label="__('change_registrations.optional_files')" :list="$optional_files" />
        @else
            <x-forms.upload-image :id="'optional_files'" :label="__('change_registrations.optional_files')" />
        @endif
    </div>

</div>
