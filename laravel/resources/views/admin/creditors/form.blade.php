@extends('admin.layouts.layout')

@section('page_title', $page_title)

@section('content')
    <div class="block {{ __('block.styles') }}">
        <div class="block-content">
            <form id="save-form">
                <h4>{{ __('creditors.creditor_detail') }}</h4>
                <hr>
                <div class="row push mb-4">
                    <div class="col-sm-4">
                        <x-forms.input-new-line id="code" :value="$d->code" :label="__('creditors.code')" :optionals="['required' => true]" />
                    </div>
                    <div class="col-sm-8">
                        <x-forms.input-new-line id="name" :value="$d->name" :label="__('creditors.name')" :optionals="['required' => true]" />
                    </div>
                </div>
                <div class="row push mb-4">
                    <div class="col-sm-6">
                        <x-forms.input-new-line id="tel" :value="$d->tel" :label="__('creditors.tel')" />
                    </div>
                    <div class="col-sm-6">
                        <x-forms.input-new-line id="mobile" :value="$d->mobile" :label="__('creditors.mobile')" />
                    </div>
                </div>
                <div class="row push mb-4">
                    <div class="col-sm-6">
                        <x-forms.input-new-line id="fax" :value="$d->fax" :label="__('creditors.fax')" />
                    </div>
                    <div class="col-sm-6">
                        <x-forms.input-new-line id="email" :value="$d->email" :label="__('creditors.email')" />
                    </div>
                </div>
                <div class="row push mb-4">
                    <div class="col-sm-3">
                        <x-forms.select-option id="province_id" :value="$d->province_id" :list="$province_list" :label="__('creditors.province')" />
                    </div>
                </div>
                <div class="row push mb-4">
                    <div class="col-sm-12">
                        <x-forms.text-area-new-line id="address" :value="$d->address" :label="__('creditors.address')" />
                    </div>
                </div>
                <div class="row push mb-5">
                    <div class="col-sm-12">
                        <x-forms.checkbox-inline id="creditor_types" :list="$creditor_type_list" :label="__('creditors.creditor_type')"
                            :value="$creditor_types" />
                    </div>
                </div>

                <h4>{{ __('creditors.contact_detail') }}</h4>
                <hr>
                <div class="row push mb-4">
                    <div class="col-sm-8">
                        <x-forms.input-new-line id="contact_name" :value="$d->contact_name" :label="__('creditors.contact_name')"
                            :optionals="['required' => true]" />
                    </div>
                    <div class="col-sm-4">
                        <x-forms.input-new-line id="contact_position" :value="$d->contact_position" :label="__('creditors.contact_position')" />
                    </div>
                </div>
                <div class="row push mb-4">
                    <div class="col-sm-4">
                        <x-forms.input-new-line id="tax_no" :value="$d->tax_no" :label="__('creditors.tax_no')" />
                    </div>
                    <div class="col-sm-4">
                        <x-forms.input-new-line id="credit_terms" :value="$d->credit_terms" :label="__('creditors.credit_terms')"
                            :optionals="['placeholder' => 'วัน', 'type' => 'number']" />
                    </div>
                    <div class="col-sm-4">
                        <x-forms.input-new-line id="install_duration" :value="$d->install_duration" :label="__('creditors.install_duration')"
                                                :optionals="['placeholder' => 'วัน', 'type' => 'number']" />
                    </div>
                </div>
                <div class="row push mb-4">
                    <div class="col-sm-12">
                        <x-forms.text-area-new-line id="payment_condition" :value="$d->payment_condition" :label="__('creditors.payment_condition')" />
                    </div>
                </div>
                <div class="row push mb-4">
                    <div class="col-sm-12">
                        <x-forms.input-new-line id="authorized_sign" :value="$d->authorized_sign" :label="__('creditors.authorized_sign')" />
                    </div>
                </div>
                <div class="row push mb-4">
                    <div class="col-sm-12">
                        <x-forms.text-area-new-line id="contact_address" :value="$d->contact_address" :label="__('creditors.contact_address')" />
                    </div>
                </div>
                <div class="row push mb-5">
                    <div class="col-sm-6">
                        <x-forms.input-new-line id="remark" :value="$d->remark" :label="__('lang.remark')" />
                    </div>
                </div>
                <x-forms.hidden id="id" :value="$d->id" />
                <x-forms.submit-group :optionals="['url' => 'admin.creditors.index', 'view' => empty($view) ? null : $view]" />
            </form>
        </div>
    </div>
@endsection

@include('admin.components.select2-default')
@include('admin.components.sweetalert')
@include('admin.components.form-save', [
    'store_uri' => route('admin.creditors.store'),
])

@push('scripts')
    <script>
        $status = '{{ isset($view) }}';
        if ($status) {
            $('#code').prop('disabled', true);
            $('#name').prop('disabled', true);
            $('#email').prop('disabled', true);
            $('#contact_position').prop('disabled', true);
            $('#province_id').prop('disabled', true);
            $('#tel').prop('disabled', true);
            $('#fax').prop('disabled', true);
            $('#mobile').prop('disabled', true);
            $('#address').prop('disabled', true);
            $('input[name="creditor_types[]"]').prop('disabled', true);
            $('#contact_name').prop('disabled', true);
            $('#contact_positon').prop('disabled', true);
            $('#tax_no').prop('disabled', true);
            $('#install_duration').prop('disabled', true);
            $('#credit_terms').prop('disabled', true);
            $('#payment_condition').prop('disabled', true);
            $('#authorized_sign').prop('disabled', true);
            $('#contact_address').prop('disabled', true);
            $('#remark').prop('disabled', true);
        }
    </script>
@endpush
