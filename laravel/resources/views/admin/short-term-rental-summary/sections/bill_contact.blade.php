<x-blocks.block :title="'ข้อมูลผู้ติดต่อวางบิล'" :optionals="['is_toggle' => false]">
    <div class="row push">
        <div class="col-sm-3">
            <x-forms.input-new-line :id="'biller_name'" :label="'ผู้ติดต่อ'" :value="null"/>
        </div>
        <div class="col-sm-3">
            <x-forms.input-new-line :id="'biller_tel'" :label="'เบอร์โทร'" :value="null"/>
        </div>
        <div class="col-sm-3">
            <x-forms.input-new-line :id="'biller_email'" :label="'อีเมล'" :value="null"/>
        </div>
    </div>
    <div class="row push">
        <div class="col-sm-12">
            <x-forms.input-new-line :id="'biller_address'" :label="'ที่อยู่'" :value="null"/>
        </div>
    </div>
    <div class="row push">
        <div class="col-sm-3">
            <x-forms.select-option :id="'biller_province_id'" :label="'จังหวัด'" :list="[]" :value="null"/>
        </div>
        <div class="col-sm-3">
            <x-forms.select-option :id="'biller_district_id'" :label="'อำเภอ/เขต'" :list="[]" :value="null"/>
        </div>
        <div class="col-sm-3">
            <x-forms.select-option :id="'biller_subdistrict_id'" :label="'ตำบล/แขวง'" :list="[]" :value="null"/>
        </div>
        <div class="col-sm-3">
            <x-forms.input-new-line :id="'bill_postal_code'" :label="'รหัสไปรษณีย์'" :value="null"/>
        </div>
    </div>
</x-blocks.block>