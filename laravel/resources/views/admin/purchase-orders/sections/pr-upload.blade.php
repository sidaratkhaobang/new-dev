<h5>{{ __('purchase_requisitions.upload_table') }}</h5>
<hr>
<div class="row push mb-4">
    <div class="col-sm-6">
        <x-forms.view-image :id="'rental_images'" :label="__('purchase_requisitions.rental_files')" :list="$rental_images_files" />
    </div>
    <div class="col-sm-6">
        <x-forms.view-image :id="'refer_images'" :label="__('purchase_requisitions.refer_files')" :list="$refer_images_files" />
    </div>
</div>
<div class="row push mb-5">
    <div class="col-sm-6">
        <x-forms.upload-image :id="'quotation_files'" :label="__('purchase_requisitions.quotation_file')" :list="$quotation_files" />
    </div>
</div>