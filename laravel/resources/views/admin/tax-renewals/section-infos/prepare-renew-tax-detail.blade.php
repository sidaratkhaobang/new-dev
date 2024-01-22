<div class="block {{ __('block.styles') }}">
    {{-- @section('block_options_list')
    <div class="block-options">
    </div>
@endsection --}}

    @include('admin.components.block-header', [
        'text' => __('tax_renewals.prepare_tax_detail'),
        'block_option_id' => '_list',
        'is_toggle' => true,
    ])
    <div class="block-content">
        <div class="row mb-4">
            <div class="col-sm-3">
                <x-forms.date-input id="request_cmi_date" :value="$d->request_cmi_date" :label="__('tax_renewals.request_cmi_date')" :optionals="['required' => true]" />
            </div>
            <div class="col-sm-3">
                <x-forms.date-input id="receive_cmi_date" :value="$d->receive_cmi_date" :label="__('tax_renewals.receive_cmi_date')" :optionals="['required' => true]" />
            </div>
            <div class="col-sm-3">
                <x-forms.input-new-line id="amount_day_wait_cmi" :value="$d->amount_day_wait_cmi" :label="__('tax_renewals.amount_day_wait_cmi')" />
            </div>
        </div>
        <div class="row mb-4">
            <div class="col-sm-3">
                <x-forms.radio-inline id="is_check_inspection" :value="$d->is_check_inspection" :list="[
                    ['name' => __('tax_renewals.inspect'), 'value' => 1],
                    ['name' => __('tax_renewals.no_inspect'), 'value' => 0],
                ]"
                    :label="__('tax_renewals.need_inspect_vif')" :optionals="['required' => true]"/>
            </div>
            <div class="col-sm-3">
                <x-forms.radio-inline id="is_check_lpg_ngv" :value="$d->is_check_lpg_ngv" :list="[
                    ['name' => __('tax_renewals.inspect'), 'value' => 1],
                    ['name' => __('tax_renewals.no_inspect'), 'value' => 0],
                ]"
                    :label="__('tax_renewals.need_inspect_lpg_ngv')" :optionals="['required' => true]"/>
            </div>
            <div class="col-sm-3" @if($d->registered_sign == RegisterSignTypeEnum::BLUE_SIGN) style="display:block;" @else style="display:none;" @endif>
                <x-forms.radio-inline id="is_check_blue_sign" :value="$d->is_check_blue_sign" :list="[
                    ['name' => __('tax_renewals.inspect'), 'value' => 1],
                    ['name' => __('tax_renewals.no_inspect'), 'value' => 0],
                ]"
                    :label="__('tax_renewals.need_inspect_blue_sign')" :optionals="['required' => true]"/>
            </div>
            <div class="col-sm-3" @if($d->registered_sign == RegisterSignTypeEnum::YELLOW_SIGN) style="display:block;" @else style="display:none;" @endif>
                <x-forms.radio-inline id="is_check_yellow_sign" :value="$d->is_check_yellow_sign" :list="[
                    ['name' => __('tax_renewals.inspect'), 'value' => 1],
                    ['name' => __('tax_renewals.no_inspect'), 'value' => 0],
                ]"
                    :label="__('tax_renewals.need_inspect_yellow_sign')" :optionals="['required' => true]"/>
            </div>
            <div class="col-sm-3" @if($d->registered_sign == RegisterSignTypeEnum::GREEN_SERVICE_SIGN) style="display:block;" @else style="display:none;" @endif>
                <x-forms.radio-inline id="is_check_green_sign" :value="$d->is_check_green_sign" :list="[
                    ['name' => __('tax_renewals.inspect'), 'value' => 1],
                    ['name' => __('tax_renewals.no_inspect'), 'value' => 0],
                ]"
                    :label="__('tax_renewals.need_inspect_green_sign')" :optionals="['required' => true]"/>
            </div>

        </div>
        <div class="row mb-4">
                <div class="col-sm-3" id="is_receive_document_label" style="display:none;">
                    <x-forms.checkbox-inline id="is_receive_documents" :list="[
                        [
                            'id' => 1,
                            'name' => 'เอกสารครบถ้วน',
                            'value' => 1,
                        ],
                    ]" :label="__('tax_renewals.receive_document_inspect')" :optionals="['required' => true]"
                                             :value="[$d->is_receive_documents]"/>
                </div>
                <div class="col-sm-3">
                    <x-forms.date-input id="request_registration_book_date" :value="$d->request_registration_book_date" :label="__('tax_renewals.request_registration_book_date')" :optionals="['required' => true]" />
                </div>
                <div class="col-sm-3">
                    <x-forms.date-input id="receive_registration_book_date" :value="$d->receive_registration_book_date" :label="__('tax_renewals.receive_registration_book_date')" :optionals="['required' => true]" />
                </div>
                <div class="col-sm-3">
                    <x-forms.input-new-line id="amount_wait_register_book_date" :value="$d->amount_wait_register_book_date" :label="__('tax_renewals.amount_wait_register_book_date')" />
                </div>
        </div>
        <div class="row mb-4">
            <div class="col-sm-12">
                <label class="text-start col-form-label"> {{ __('lang.remark') }}</label>
                <textarea class="form-control" id="remark" name="remark" placeholder="" :value="$d->remark"
                    :label="__('lang.remark')"></textarea>
            </div>
          


        </div>
        <div class="row mb-4">
            <div class="col-sm-3">
                @if (isset($view))
                    <x-forms.view-image :id="'optional_files'" :label="__('registers.optional_files')" :list="$optional_files" />
                @else
                    <x-forms.upload-image :id="'optional_files'" :label="__('registers.optional_files')" />
                @endif
            </div>


        </div>
    </div>
</div>
