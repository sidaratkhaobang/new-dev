<div class="block {{ __('block.styles') }}">
    <div class="block-content box-padding-bottom">
        @include('admin.components.block-header',[
     'text' =>  __('request_premium.customer_detail')  ,
    'block_icon_class' => 'icon-document'
])
        <div class="row">
            <div class="col-sm-3 mb-2">
                <x-forms.input-new-line id="customer_id"
                                        :value="$d?->getLongTermRental?->customer?->customer_code.'-'.$d?->getLongTermRental?->customer?->name"
                                        :label="__('request_premium.customer_id')"
                                        :optionals="['required' => false]"/>
                <x-forms.hidden id="customer_id" :value="null"/>
            </div>
            <div class="col-sm-3 mb-2">
                <x-forms.input-new-line id="customer" :value="$d?->getLongTermRental?->customer_name"
                                        :label="__('request_premium.customer')"
                                        :optionals="['required' => false]"/>
                <x-forms.hidden id="customer" :value="null"/>
            </div>
            <div class="col-sm-6 mb-2">
                <x-forms.select-option id="customer_group" :value="$d?->customer_group" :list="$customer_group_list"
                                       :label="__('customers.customer_group')"
                                       :optionals="['multiple' => true]"/>
            </div>
            <div class="col-sm-3 mb-2">
                <x-forms.input-new-line id="customer_email" :value="$d?->getLongTermRental?->customer_email"
                                        :label="__('request_premium.email')"
                                        :optionals="['required' => false]"/>
                <x-forms.hidden id="customer_email" :value="null"/>
            </div>
            <div class="col-sm-3 mb-2">
                <x-forms.input-new-line id="customer_phone" :value="$d?->getLongTermRental?->customer_tel"
                                        :label="__('request_premium.phone')"
                                        :optionals="['required' => false]"/>
                <x-forms.hidden id="customer_phone" :value="null"/>
            </div>
        </div>
    </div>
</div>

