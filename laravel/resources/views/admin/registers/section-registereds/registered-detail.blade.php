<div class="block {{ __('block.styles') }}">
    {{-- @section('block_options_list')
    <div class="block-options">
    </div>
@endsection --}}

    @include('admin.components.block-header', [
        'text' => __('registers.registered_detail'),
        'block_option_id' => '_list',
        'is_toggle' => true,
    ])
    <div class="block-content">
        <div class="row mb-4">
            <div class="col-sm-3">
                <x-forms.date-input id="document_date" :value="$d->document_date" :label="__('registers.document_date')" :optionals="['required' => true]" />
            </div>
            <div class="col-sm-3">
                <x-forms.input-new-line id="document_date_amount" :value="$d->pr_no" :label="__('registers.document_date_amount')" />
            </div>
        </div>
        <div class="row mb-4">
            <div class="col-sm-3">
                <x-forms.date-input id="receive_registered_dress_date" :value="$d->receive_registered_dress_date" :label="__('registers.receive_registered_dress_date')"
                    :optionals="['required' => true]" />
            </div>
            <div class="col-sm-3">
                <x-forms.input-new-line id="receive_registered_dress_date_amount" :value="$d->pr_no" :label="__('registers.receive_registered_dress_date_amount')" />
            </div>
        </div>
        <div class="row mb-4">
            <div class="col-sm-3">
                <x-forms.date-input id="receive_cmi" :value="$d->receive_cmi" :label="__('registers.receive_cmi')" :optionals="['required' => true]" />
            </div>
            <div class="col-sm-3">
                <x-forms.input-new-line id="receive_cmi_amount" :value="$d->pr_no" :label="__('registers.receive_cmi_amount')" />
            </div>
        </div>
        <div class="row mb-4">
            <div class="col-sm-3">
                <x-forms.date-input id="receive_document_sale_date" :value="$d->receive_document_sale_date" :label="__('registers.receive_document_sale_date')"
                    :optionals="['required' => true]" />
            </div>
            <div class="col-sm-3">
                <x-forms.input-new-line id="receive_document_sale_date_amount" :value="$d->pr_no" :label="__('registers.receive_document_sale_date_amount')" />
            </div>
        </div>
        <div class="row mb-4">
            <div class="col-sm-3">
                <x-forms.radio-inline id="is_roof_receipt" :value="$d->is_roof_receipt" :list="$is_receipt_roof_status_list" :label="__('registers.is_receipt_roof')"
                    :optionals="['required' => true, 'input_class' => 'col-sm-6 input-pd']" />

            </div>
            <div class="col-sm-3 check-roof"
                @if (strcmp($d->is_roof_receipt, STATUS_ACTIVE) == 0) style="display: block;" @else style="display: none;" @endif>
                <x-forms.date-input id="receive_roof_receipt_date" :value="$d->receive_roof_receipt_date" :label="__('registers.receive_roof_receipt_date')"
                    :optionals="['required' => true]" />
            </div>
            <div class="col-sm-3 check-roof"
                @if (strcmp($d->is_roof_receipt, STATUS_ACTIVE) == 0) style="display: block;" @else style="display: none;" @endif>
                <x-forms.input-new-line id="receive_roof_receipt_date_amount" :value="$d->receive_roof_receipt_date_amount" :label="__('registers.receive_roof_receipt_date_amount')" />
            </div>
        </div>
        <hr>
        <div class="row mb-4">
            <div class="col-sm-6">
                <x-forms.radio-inline id="is_lock_license_plate" :value="$d->is_lock_license_plate" :list="$status_list"
                    :label="__('registers.is_lock_license_plate')" :optionals="['required' => true, 'input_class' => 'col-sm-6 input-pd']" />

            </div>
            <div class="col-sm-6 check-lock"
                @if (strcmp($d->is_lock_license_plate, STATUS_ACTIVE) == 0) style="display: block;" @else style="display: none;" @endif>
                <x-forms.select-option id="type_lock_license_plate" :value="$d->type_lock_license_plate" :list="$lock_license_plate_list"
                    :label="__('registers.type_lock_license_plate')" :optionals="['required' => true]" />
            </div>
            <div class="col-sm-6 check-lock"
                @if (strcmp($d->is_lock_license_plate, STATUS_ACTIVE) == 0) style="display: block;" @else style="display: none;" @endif>
                <x-forms.input-new-line id="detail_lock_license_plate" :value="$d->detail_lock_license_plate" :label="__('registers.detail_lock_license_plate')"
                    :optionals="['required' => true]" />
            </div>
            {{-- <div class="col-sm-3">
                <x-forms.date-input id="send_registered_date" :value="$d->send_registered_date" :label="__('registers.send_registered_date')"
                    :optionals="['required' => true]" />
            </div> --}}
        </div>
        <div class="row mb-4">
            <div class="col-sm-3">
                <x-forms.date-input id="send_registered_date" :value="$d->send_registered_date" :label="__('registers.send_registered_date')"
                    :optionals="['required' => true]" />
            </div>
            <div class="col-sm-3">
                <x-forms.date-input id="registered_date" :value="$d->registered_date" :label="__('registers.registered_date')" :optionals="['required' => true]" />
            </div>
            <div class="col-sm-3">
                <x-forms.date-input id="receive_information_date" :value="$d->receive_information_date" :label="__('registers.receive_information_date')"
                    :optionals="['required' => true]" />
            </div>
            <div class="col-sm-3">
                <x-forms.input-new-line id="registered_date_amount" :value="$d->registered_date_amount" :label="__('registers.registered_date_amount')" />
            </div>
        </div>
        <div class="row mb-4">
            <div class="col-sm-3">
                <x-forms.input-new-line id="license_plate" :value="$d->car->license_plate" :label="__('registers.license_plate')" />
            </div>
            <div class="col-sm-3">
                <x-forms.checkbox-inline id="receive_register_sign" :list="$receive_register_sign" :label="__('registers.receive_register_sign')" :value="$d->receive_register_sign" />
            </div>
            <div class="col-sm-3">
                <x-forms.date-input id="car_tax_exp_date" :value="$d->car_tax_exp_date" :label="__('registers.car_tax_exp_date')"
                    :optionals="['required' => true]" />
            </div>
        </div>
        <div class="row mb-4">
            <div class="col-sm-6">
                <x-forms.input-new-line id="link" :value="$d->link" :label="__('registers.link')" />
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
            <div class="col-sm-9">
                <x-forms.input-new-line id="remark" :value="$d->remark" :label="__('registers.remark')" />
            </div>

        </div>
    </div>
</div>
