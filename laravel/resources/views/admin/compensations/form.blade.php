@extends('admin.layouts.layout')

@section('page_title', $page_title)

@section('content')
<form id="save-form">
    @include('admin.compensations.sections.car-detail', ['car' => $d->accident?->car])
    @include('admin.compensations.sections.rental-detail')
    @include('admin.compensations.sections.insurance-detail')
    @include('admin.compensations.sections.prescription')
    @include('admin.compensations.sections.party')
    @include('admin.compensations.sections.compens-sum')
    @includeWhen($d->creator_id, 'admin.compensations.sections.notice')
    @includeWhen($d->type, 'admin.compensations.sections.negotiation')
    @includeWhen($is_end_nogotiation, 'admin.compensations.sections.terminate')
    @includeWhen(in_array($d->status, [CompensationStatusEnum::CONFIRM, CompensationStatusEnum::COMPLETE]), 'admin.compensations.sections.withdraw')
    @includeWhen(in_array($d->status, [CompensationStatusEnum::CONFIRM, CompensationStatusEnum::COMPLETE]), 'admin.compensations.sections.payment')
    <div class="block {{ __('block.styles') }}">
        <div class="block-content">
            <x-forms.hidden id="id" name="id" :value="$d->id" />
                @include('admin.compensations.sections.submit-group')
        </div>
    </div>

    
    {{-- <div class="block {{ __('block.styles') }}">
        <div class="block-content">
            <x-forms.hidden id="id" name="id" :value="$d->id" />
        </div>
    </div> --}}
</form>
@endsection

@include('admin.components.select2-default')
@include('admin.components.sweetalert')
@include('admin.components.form-save', [
    'store_uri' => route('admin.compensations.store'),
])
@include('admin.components.date-input-script')
@include('admin.compensations.scripts.notice-script')
@include('admin.compensations.scripts.claim-script')
@include('admin.compensations.scripts.terminate-script')


@include('admin.components.select2-ajax', [
    'id' => 'insurer_parties_id',
    'url' => route('admin.util.select2-insurance.insurance-companies'),
])

@include('admin.components.select2-ajax', [
    'id' => 'creator_id',
    'url' => route('admin.util.select2.users'),
])

@include('admin.components.select2-ajax', [
    'id' => 'car_brand_parties_id',
    'url' => route('admin.util.select2.car-brand'),
])
@include('admin.components.upload-image-scripts')
@include('admin.components.upload-image', [
    'id' => 'termination_files',
    'max_files' => 10,
    'accepted_files' => '.jpg,.jpeg,.bmp,.png,.pdf',
    'mock_files' => $termination_files,
    'view_only' => $can_edit_termination_files ? null : true,
    'show_url' => true
])

@include('admin.components.upload-image', [
    'id' => 'receive_files',
    'max_files' => 10,
    'accepted_files' => '.jpg,.jpeg,.bmp,.png,.pdf',
    'mock_files' => $receive_files,
    'view_only' => $can_edit_payment_files ? null : true,
    'show_url' => true
])

@include('admin.components.upload-image', [
    'id' => 'payment_files',
    'max_files' => 10,
    'accepted_files' => '.jpg,.jpeg,.bmp,.png,.pdf',
    'mock_files' => $payment_files,
    'show_url' => true,
    'view_only' => $can_edit_payment_files ? null : true,
])

@include('admin.components.upload-image', [
    'id' => 'tax_invoice_files',
    'max_files' => 10,
    'accepted_files' => '.jpg,.jpeg,.bmp,.png,.pdf',
    'mock_files' => $tax_invoice_files,
    'show_url' => true,
    'view_only' => $can_edit_payment_files ? null : true,
])

@push('scripts')
    <script>
        $("#negotiation_type").on('select2:select', function(e) {
            __log('d');
            var data = e.params.data;
            if (data.id === '{{ NegotiationTypeEnum::INSURANCE }}') {
                $(".insurance-section").css("display","block");
                $(".oic-section").css("display","none");
            }

            if (data.id === '{{ NegotiationTypeEnum::OIC }}') {
                $(".insurance-section").css("display","none");
                $(".oic-section").css("display","block");
            }
        });

        $("#negotiation_type").on('select2:unselect', function(e) {
            $(".insurance-section").css("display","none");
            $(".oic-section").css("display","none");
        });

        function disableInputs(inputs) {
            inputs.forEach(input => {
                input.setAttribute('readonly', 'true');
                input.setAttribute('disabled', 'true');
            });
        }

        const inputs_readonly = document.querySelectorAll('.content-readonly input, .content-readonly select, #accident_date');
        disableInputs(inputs_readonly);

        var status = @if (isset($d->status)) @json($d->status) @else null @endif;
        if (status == "{{ CompensationStatusEnum::UNDER_NEGOTIATION }}") {
            var selector = '.prescription-block input, .prescription-block select,';
            selector += '.party-block input, .party-block select,';
            selector += '.compen-sum-block input, .compen-sum-block select,';
            selector += '.terminate-block input, .terminate-block select';
            const inputs = document.querySelectorAll(selector);
            disableInputs(inputs);
        }
        if (status == "{{ CompensationStatusEnum::END_NEGOTIATION }}") {
            var selector = '.prescription-block input, .prescription-block select,';
            selector += '.party-block input, .party-block select,';
            selector += '.compen-sum-block input, .compen-sum-block select';
            const inputs = document.querySelectorAll(selector);
            disableInputs(inputs);
        }
        const is_view = @if (is_view()) true @else false @endif;
        if (status == "{{ CompensationStatusEnum::CONFIRM }}") {
            var selector = '.prescription-block input, .prescription-block select,';
            selector += '.party-block input, .party-block select,';
            selector += '.compen-sum-block input, .compen-sum-block select,';
            selector += '.terminate-block input, .terminate-block select';
            const inputs = document.querySelectorAll(selector);
            disableInputs(inputs);
        }

        
        if (is_view || status == "{{ CompensationStatusEnum::COMPLETE }}") {
            const inputs = document.querySelectorAll('input, select');
            disableInputs(inputs);
        }
    </script>
@endpush