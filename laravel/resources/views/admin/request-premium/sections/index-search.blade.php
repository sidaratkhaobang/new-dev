<div class="block {{ __('block.styles') }}">
    @include('admin.components.block-header',[
     'text' =>   __('lang.search')    ,
    'block_icon_class' => 'icon-search',
       'is_toggle' => true
])

    <div class="block-content">
        <div class="justify-content-between mb-4">
            <form action="" method="GET" id="form-search">
                <div class="row">
                    <div class="col-sm-3">
                        <x-forms.select-option id="job_id" :value="$worksheet_no" :list="$request_premium_list"
                                               :label="__('request_premium.longterm_rental_number')"
                                               :optionals="['required' => false]"/>
                    </div>
                    <div class="col-sm-3">
                        <x-forms.select-option id="job_type" :value="$job_type" :list="$longter_reantal_type_list"
                                               :label="__('request_premium.job_type')"
                                               :optionals="['required' => false]"/>
                    </div>
                    <div class="col-sm-3">
                        <label class="text-start col-form-label" for="customer_id">{{ __('request_premium.customer') }}</label>
                        <select name="customer_id" id="customer_id" class="form-control js-select2-default" style="width: 100%;" >
                            @if (!empty($customer_code))
                                <option value="{{$customer_id}}">{{ $customer_code }}</option>
                            @endif
                        </select>
{{--                        <x-forms.select-option id="customer_code" :value="$customer_code" :list="$customer_list"--}}
{{--                                               :label=""--}}
{{--                                               :optionals="['required' => false]"/>--}}
                    </div>
                    <div class="col-sm-3">
                        <x-forms.select-option id="request_status" :value="$request_status" :list="$request_premium_status_list"
                                               :label="__('request_premium.status')"
                                               :optionals="['required' => false]"/>
                    </div>
                </div>
                @include('admin.components.btns.search')
            </form>
        </div>
    </div>
</div>
