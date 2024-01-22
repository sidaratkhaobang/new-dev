<h5>{{ __('car_classes.upload_table') }}</h5>
<hr>
<div class="row push mb-4">
    <div class="col-sm-6 mb-4">
        <x-forms.upload-image :id="'images'" :label="__('car_classes.attach_files')" />
    </div>
    <div class="col-sm-6 mb-4">
        <x-forms.upload-image :id="'car_images'" :label="__('car_classes.sample_car_images')" />
    </div>
</div>