@extends('admin.layouts.layout')
@section('page_title', $page_title)
@push('custom_styles')
    <style>
        .grey-text {
            color: #858585;
        }

        .size-text {
            font-size: 16px;
            font-weight: bold;
        }

        .col-form-label {
            padding-top: calc(0rem + 0px);
        }
    </style>
@endpush
@section('content')
    <div class="block {{ __('block.styles') }}">
        <div class="block-content">
            <form id="save-form">
                <div class="justify-content-between mb-4">
                    @include('admin.gps-check-signal-jobs.sections.rental-info')
                    @include('admin.gps-check-signal-jobs.sections.car-info')
                    <h4 class="mt-4">{{ __('gps.gps_data') }}</h4>
                    <div class="row push mb-4">
                        <div class="col-sm-3">
                            <p class="grey-text">{{ __('gps.vid') }}</p>
                            <p class="size-text" id="vid">{{ $d->vid }}</p>
                        </div>
                        <div class="col-sm-3">
                            <p class="grey-text">{{ __('gps.sim') }}</p>
                            <p class="size-text" id="sim">{{ $d->sim }}</p>
                        </div>
                        <div class="col-sm-3">
                            <p class="grey-text">{{ __('gps.must_check_date') }}</p>
                            <p class="size-text" id="must_check_date">
                                {{ $d->must_check_date ? get_thai_date_format($d->must_check_date, 'd/m/Y') : null }}</p>
                        </div>
                        <div class="col-sm-3">
                            <p class="grey-text">{{ __('gps.check_date') . '(' . $branch_name . ')' }}</p>
                            <p class="size-text" id="check_date">
                                {{ $d->check_date ? get_thai_date_format($d->check_date, 'd/m/Y') : null }}</p>
                        </div>
                    </div>
                    <div class="row push mb-4">
                        <div class="col-sm-3">
                            <p class="grey-text">{{ __('lang.status') . '(' . $branch_name . ')' }}</p>
                            <p class="size-text" id="status">{{ __('gps.status_text_' . $d->status) }}</p>
                        </div>
                        <div class="col-sm-3">
                            <p class="grey-text">{{ __('gps.remark') . '(' . $branch_name . ')' }}</p>
                            <p class="size-text" id="remark">{{ $d->remark }}</p>
                        </div>
                        <div class="col-sm-3">
                            <x-forms.date-input id="main_branch_date" :value="$d->main_branch_date" :label="__('gps.check_date') . '(' . 'สาขาหลัก' . ')'"
                                :optionals="['placeholder' => __('lang.select_date')]" />
                        </div>
                        <div class="col-sm-3">
                            <x-forms.select-option id="status_main_branch" :value="$d->status_main_branch" :list="$status_approve"
                                :label="__('lang.status') . '(' . 'สาขาหลัก' . ')'" />
                        </div>
                    </div>
                    <div class="row push mb-4">
                        <div class="col-sm-6">
                            <x-forms.input-new-line id="remark_main_branch" :value="$d->remark_main_branch" :label="__('gps.remark') . '(' . 'สาขาหลัก' . ')'" />
                        </div>
                    </div>
                    <hr>

                    <x-forms.hidden id="id" :value="$d->id" />
                    <x-forms.submit-group :optionals="[
                        'url' => 'admin.gps-check-signal-job-branch.index',
                        'view' => empty($view) ? null : $view,
                    ]" />
            </form>
        </div>
    </div>
@endsection

@include('admin.components.sweetalert')
@include('admin.components.date-input-script')
@include('admin.components.select2-default')
@include('admin.components.form-save', [
    'store_uri' => route('admin.gps-check-signal-job-branch.store'),
])
@push('scripts')
    <script>
        $view = '{{ isset($view) }}';
        if ($view) {
            $('#status').prop('disabled', true);
            $('#remark').prop('disabled', true);
            $("input[type=radio]").attr('disabled', true);
            $('#main_branch_date').prop('disabled', true);
            $('#status_main_branch').prop('disabled', true);
            $('#remark_main_branch').prop('disabled', true);
        }
    </script>
@endpush
