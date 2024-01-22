@extends('admin.layouts.layout')
@section('page_title', $page_title . '' . $d->worksheet_no)
@section('page_title_sub')
    @if (isset($d->status))
        {!! badge_render(__('repairs.repair_class_' . $d->status), __('repairs.repair_text_' . $d->status), null) !!}
    @endif
@endsection

@push('styles')
    <style>
        .profile-image {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            overflow: hidden;
        }

        .profile-image img {
            width: 10%;
            height: 10%;
            object-fit: cover;
        }
    </style>
@endpush
@push('custom_styles')
    <style>
        .badge-custom {
            min-width: 20rem;
        }
    </style>
@endpush

@section('content')
    <form id="save-form">

        @include('admin.repair-orders.sections.user')
        @if (in_array($d->status, [
                RepairStatusEnum::PENDING_REPAIR,
                RepairStatusEnum::IN_PROCESS,
                RepairStatusEnum::WAIT_APPROVE_QUOTATION,
                RepairStatusEnum::REJECT_QUOTATION,
                RepairStatusEnum::EXPIRED,
                RepairStatusEnum::COMPLETED,
                RepairStatusEnum::CANCEL,
            ]))
            @include('admin.repair-orders.sections.btn-group')
        @endif


        <div class="block {{ __('block.styles') }}">
            <div class="block-content">
                @include('admin.repair-orders.sections.repair-condition')
                <x-forms.hidden id="id" :value="$d->id" />
                <x-forms.hidden id="quotation_id" :value="$quotation->id" />
                <div class="row push">
                    <div class="col-sm-12 text-end">
                        <a class="btn btn-secondary" href="{{ route('admin.repair-orders.index') }}">{{ __('lang.back') }}</a>
                        @if (empty($view))
                            <button type="button" class="btn btn-primary btn-save-form">{{ __('lang.save') }}</button>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </form>
@endsection

@include('admin.components.select2-default')
@include('admin.components.date-input-script')
@include('admin.components.sweetalert')
@include('admin.components.form-save', [
    'store_uri' => route('admin.repair-order-conditions.store'),
])
@include('admin.repair-orders.scripts.repair-condition-script')
@push('scripts')
    <script>
        $view = '{{ isset($view) }}';
        if ($view) {
            $('.form-control').prop('disabled', true);
            $("input[type=radio]").attr('disabled', true);
        }
    </script>
@endpush
