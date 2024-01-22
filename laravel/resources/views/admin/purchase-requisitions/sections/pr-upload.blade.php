<h5>{{ __('purchase_requisitions.upload_table') }}</h5>
<hr>
<div class="row push mb-4">
    <div class="col-sm-6">
        <x-forms.upload-image :id="'rental_images'" :label="__('purchase_requisitions.rental_files')" />
    </div>
    <div class="col-sm-6 mb-4">
        <x-forms.upload-image :id="'refer_images'" :label="__('purchase_requisitions.reference_file')" />
    </div>
</div>
<br>
