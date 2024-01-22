<div class="block {{ __('block.styles') }} party-block">
    @include('admin.components.block-header', [
        'text' => __('compensations.party_detail'),
    ])
    <div class="block-content">
        <div class="row push">
            <div class="col-sm-3">
                <x-forms.select-option id="insurer_parties_id" :value="$d->insurer_parties_id" :list="null" :label="__('compensations.insurer_company')" 
                :optionals="['ajax' => true, 'default_option_label' => $insuer_name, 'required' => true]" />
            </div>
            <div class="col-sm-6">
                <x-forms.input-new-line id="insurer_parties_address" :value="$d->insurer_parties_address" :label="__('compensations.insurer_parties_address')" 
                    :optionals="['required' => true]" />
            </div>
        </div>
        <div class="row push">
            <div class="col-sm-3">
                <x-forms.input-new-line id="name_parties" :value="$d->name_parties" :label="__('compensations.name_parties')" 
                    :optionals="['required' => true]" />
            </div>
            <div class="col-sm-3">
                <x-forms.input-new-line id="tel_parties" :value="$d->tel_parties" :label="__('compensations.tel_parties')" 
                    :optionals="['required' => true]" />
            </div>
            <div class="col-sm-6">
                <x-forms.input-new-line id="address_parties" :value="$d->address_parties" :label="__('compensations.address_parties')" 
                    :optionals="['required' => true]"  />
            </div>
        </div>
        <div class="row push">
            <div class="col-sm-3">
                <x-forms.input-new-line id="id_card_parties" :value="$d->id_card_parties" :label="__('compensations.id_card_parties')" 
                    :optionals="['required' => true]" />
            </div>
            <div class="col-sm-3">
                <x-forms.input-new-line id="vmi_no_parties" :value="$d->vmi_no_parties" :label="__('compensations.vmi_no_parties')" 
                    :optionals="['required' => true]" />
            </div>
            <div class="col-sm-3">
                <x-forms.input-new-line id="claim_no_parties" :value="$d->claim_no_parties" :label="__('compensations.claim_no_parties')"
                    :optionals="['required' => true]" />
            </div>
        </div>
        <div class="row push">
            <div class="col-sm-3">
                <x-forms.input-new-line id="car_type_parties" :value="$d->car_type_parties" :label="__('compensations.car_type')" 
                    :optionals="['required' => true]" />
            </div>
            <div class="col-sm-3">
                <x-forms.select-option id="car_brand_parties_id" :value="$d->car_brand_parties_id" :list="null" :label="__('compensations.car_brand')" 
                    :optionals="['ajax' => true, 'default_option_label' => $car_brand_name, 'required' => true]"/>
            </div>
            <div class="col-sm-3">
                <x-forms.input-new-line id="license_plate_parties" :value="$d->license_plate_parties" :label="__('compensations.license_plate')" 
                    :optionals="['required' => true]" />
            </div>
            <div class="col-sm-3">
                <x-forms.select-option id="province_parties_id" :value="$d->province_parties_id" :list="$province_list" :label="__('compensations.provinces')" 
                    :optionals="['required' => true]" />
            </div>
        </div>
    </div>
</div>
