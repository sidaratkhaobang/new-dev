<div class="block {{ __('block.styles') }}">
    <form id="save-form">
        <x-forms.hidden id="id" :value="$d->id"/>
        @include('admin.components.block-header',[
   'text' =>    __('insurances.insurance_data')     ,
  'block_icon_class' => 'icon-document',
])
        <div class="block-content">
            <div class="row push">
                <div class="col-sm-3">
                    <x-forms.input-new-line id="insurance_id" :value="$d->code" :label="__('insurances.insurance_id')"
                                            :optionals="['required' => true]"/>
                </div>
                @if(isset($edit))
                    <x-forms.input-new-line id="insurance_id" :value="$d->code" :label="__('insurances.insurance_id')"
                                            :optionals="['required' => true,'label_class'=>'d-none','input_class' => 'd-none']"/>
                    <x-forms.input-new-line id="update" :value="1" :label="__('insurances.insurance_id')"
                                            :optionals="['required' => true,'label_class'=>'d-none','input_class' => 'd-none']"/>
                @endif
                <div class="col-sm-3">
                    <x-forms.input-new-line id="insurance_th" :value="$d->insurance_name_th"
                                            :label="__('insurances.insurance_th')" :optionals="['required' => true]"/>
                </div>
                <div class="col-sm-3">
                    <x-forms.input-new-line id="insurance_en" :value="$d->insurance_name_en"
                                            :label="__('insurances.insurance_en')" :optionals="['required' => false]"/>
                </div>
                <div class="col-sm-3">
                    <x-forms.input-new-line id="website" :value="$d->insurance_web" :label="__('insurances.website')"
                                            :optionals="['required' => false]"/>
                </div>
            </div>
            <div class="row push">
                <div class="col-3">
                    <x-forms.input-new-line id="insurance_phone" :value="$d->insurance_tel"
                                            :label="__('insurances.insurance_phone')"
                                            :optionals="['required' => false,'type'=> 'phone']"/>
                </div>
                <div class="col-3">
                    <x-forms.input-new-line id="insurance_email" :value="$d->insurance_email"
                                            :label="__('insurances.insurance_email')"
                                            :optionals="['required' => false,'type'=> 'email']"/>
                </div>
                <div class="col-3">
                    <x-forms.input-new-line id="insurance_fax" :value="$d->insurance_fax"
                                            :label="__('insurances.insurance_fax')" :optionals="['required' => false]"/>
                </div>
            </div>
            <div class="row push">

                <div class="col-12">
                    <x-forms.input-new-line id="address" :value="$d->insurance_address"
                                            :label="__('insurances.address')" :optionals="['required' => false]"/>
                </div>
            </div>
            <div class="row push">
                <div class="col-3">
                    <x-forms.input-new-line id="coordinator_name" :value="$d->contact_name"
                                            :label="__('insurances.coordinator_name')"
                                            :optionals="['required' => false]"/>
                </div>
                <div class="col-3">
                    <x-forms.input-new-line id="coordinator_email" :value="$d->contact_email"
                                            :label="__('insurances.insurance_email')"
                                            :optionals="['required' => false,'type'=> 'email']"/>
                </div>
                <div class="col-3">
                    <x-forms.input-new-line id="coordinator_phone" :value="$d->contact_tel"
                                            :label="__('insurances.insurance_phone')"
                                            :optionals="['required' => false,'type'=> 'phone']"/>
                </div>
            </div>
            <div class="row push">
                <div class="col-12">
                    <x-forms.input-new-line id="remark" :value="$d->remark" :label="__('lang.remark')"
                                            :optionals="['required' => false]"/>
                </div>
            </div>
            <div class="row push">
                <div class="col-sm-12">
                    <x-forms.radio-inline id="status" :value="(!empty($d->status))?$d->status:1" :list="$list_status"
                                          :label="__('lang.status')"/>
                </div>
            </div>
            <x-forms.submit-group
                :optionals="['url' => 'admin.insurances-companies.index', 'view' => empty($view) ? null : $view,'manage_permission' => Actions::Manage . '_' . Resources::InsuranceCompanies]"/>
        </div>
    </form>
</div>
