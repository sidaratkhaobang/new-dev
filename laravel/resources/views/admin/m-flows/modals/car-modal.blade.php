<div class="modal fade" id="modal-car" tabindex="-1" data-bs-keyboard="false" data-bs-backdrop="static"
    aria-labelledby="modal-car" aria-hidden="false">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-popout modal-dialog-scrollable"
        style="width:1250px;">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="car-modal-label"><i class="icon-document"></i>ข้อมูลงานเช่าและคนขับ</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="block {{ __('block.styles') }}">
                    @include('admin.components.block-header', [
                        'text' => 'ข้อมูลงานเช่า',
                    ])
                    <div class="block-content">
                        <div class="row push">
                            <div class="col-sm-3">
                                <x-forms.label id="rental_no" :value="null" :label="__('m_flows.rental_no')" />
                            </div>
                            <div class="col-sm-3">
                                <x-forms.label id="rental_name" :value="null" :label="__('m_flows.rental_name')" />
                            </div>
                            <div class="col-sm-3">
                                <x-forms.label id="business_line" :value="null" :label="__('m_flows.business_line')" />
                            </div>
                            <div class="col-sm-3">
                                <x-forms.label id="car_type" :value="null" :label="__('m_flows.car_type')" />
                            </div>
                        </div>
                        <div class="row push">
                            <div class="col-sm-3">
                                <x-forms.label id="contract_no" :value="null" :label="__('m_flows.contract_no')" />
                            </div>
                            <div class="col-sm-3">
                                <x-forms.label id="contract_start_date" :value="null" :label="__('m_flows.contract_start_date')" />
                            </div>
                            <div class="col-sm-3">
                                <x-forms.label id="contract_end_date" :value="null" :label="__('m_flows.contract_end_date')" />
                            </div>
                            <div class="col-sm-3">
                                <x-forms.label id="customer_group" :value="null" :label="__('m_flows.customer_group')" />
                            </div>
                        </div>
                        <div class="row mb-4">
                            <div class="col-sm-12">
                                <x-forms.label id="customer_address" :value="null" :label="__('m_flows.customer_address')" />
                            </div>
                        </div>
                    </div>
                </div>
                <div class="block {{ __('block.styles') }}">
                    @include('admin.components.block-header', [
                        'text' => __('m_flows.driver_data'),
                    ])
                    <div class="block-content">
                        <div class="row push">
                            <div class="col-sm-3">
                                <x-forms.label id="full_name" :value="null" :label="__('m_flows.full_name')" />
                            </div>
                            <div class="col-sm-3">
                                <x-forms.label id="agency" :value="null" :label="__('m_flows.agency')" />
                            </div>
                            <div class="col-sm-3">
                                <x-forms.label id="driver_tel" :value="null" :label="__('m_flows.driver_tel')" />
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-clear-search"
                    data-bs-dismiss="modal">{{ __('lang.back') }}</button>
            </div>
        </div>
    </div>
</div>
