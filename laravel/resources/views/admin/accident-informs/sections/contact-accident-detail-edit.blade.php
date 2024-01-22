<div class="block {{ __('block.styles') }}">
    @include('admin.components.block-header', [
        'text' => __('accident_informs.contact_accident'),
    ])
    <div class="block-content">
        <div class="row push mb-4" class="contact-accident">
            <div class="col-sm-3">
                <x-forms.label id="contact_segment_1" :value="'อุบัติเหตุร้ายแรง ขอรถยก'" :label="__('accident_informs.segment')" :optionals="['required' => true]" />
            </div>
            <div class="col-sm-3">
                <x-forms.label id="contact_fullname_1" :value="'คุณวุฒิภัทร อชิรวราชัย'" :label="__('accident_informs.fullname')" :optionals="['required' => true]" />
            </div>
            <div class="col-sm-3">
                <x-forms.label id="contact_department_1" :value="'อุบัติเหตุ'" :label="__('accident_informs.department')" :optionals="['required' => true]" />

            </div>
            <div class="col-sm-3">
                <x-forms.label id="contact_tel_1" :value="'085-980-3737'" :label="__('accident_informs.tel')" :optionals="['required' => true]" />
            </div>
        </div>
        <div class="row push mb-4" class="contact-accident">
            <div class="col-sm-3">
                <x-forms.label id="contact_segment_2" :value="'อุบัติเหตุร้ายแรง ขอรถยก ติดตามสถานะรถ ระหว่างซ่อม'" :label="__('accident_informs.segment')" :optionals="['required' => true]" />
            </div>
            <div class="col-sm-3">
                <x-forms.label id="contact_fullname_2" :value="'คุณสุชาติ ยุติธรรม'" :label="__('accident_informs.fullname')" :optionals="['required' => true]" />
            </div>
            <div class="col-sm-3">
                <x-forms.label id="contact_department_2" :value="'อุบัติเหตุ'" :label="__('accident_informs.department')" :optionals="['required' => true]" />

            </div>
            <div class="col-sm-3">
                <x-forms.label id="contact_tel_2" :value="'084-075-4748'" :label="__('accident_informs.tel')" :optionals="['required' => true]" />
            </div>
        </div>
        <div class="row push mb-4" class="contact-accident">
            <div class="col-sm-3">
                <x-forms.label id="contact_segment_3" :value="'อุบัติเหตุร้ายแรง ขอรถยก ติดตามสถานะรถ ระหว่างซ่อม'" :label="__('accident_informs.segment')" :optionals="['required' => true]" />
            </div>
            <div class="col-sm-3">
                <x-forms.label id="contact_fullname_3" :value="'คุณอดิเรก อินทรมงคล'" :label="__('accident_informs.fullname')" :optionals="['required' => true]" />
            </div>
            <div class="col-sm-3">
                <x-forms.label id="contact_department_3" :value="'อุบัติเหตุ'" :label="__('accident_informs.department')" :optionals="['required' => true]" />

            </div>
            <div class="col-sm-3">
                <x-forms.label id="contact_tel_3" :value="'089-455-1290'" :label="__('accident_informs.tel')" :optionals="['required' => true]" />
            </div>
        </div>
        <div class="row push mb-4" id="email-input">
            <div class="col-sm-12">
                <label for="email" class="text-start col-form-label">{{ __('import_cars.email') }}</label>
                <div class="tag-field js-tags" id="js-tag-car">
                    <input type="text" class="form-control js-tag-input" id="email" name="email"
                        placeholder="ระบุข้อมูล...">
                </div>
            </div>
        </div>

    </div>
</div>
