@extends('admin.layouts.layout')
@section('page_title',$page_title)

@section('page_title_sub')
    <span class="ms-1 me-1 lt_no" style="color: var(--neutral-light-fonts-01, #64748B);">
        {{$lt_id}}
    </span>
    {{--    @if (isset($d->status))--}}
    {!! badge_render(
                                      __('request_premium.request_premium_status_class_' . $d->status),
                                      __('request_premium.request_premium_status_' . $d->status),
                                  ) !!}
    {{--    @endif--}}
@endsection
@section('history')
    @include('admin.components.btns.history')
@endsection
@push('styles')
    <style>
        .badge {
            margin-left: 0.25rem;
            margin-right: 0.25rem;
        }

        .box-padding-bottom {
            padding-bottom: 20px !important;
        }

        .btn-tor {
            pointer-events: none;
            width: 100%;
            display: flex;
            height: 40px;
            justify-content: center;
            align-items: center;
            border-radius: 6px;
            background: var(--action-color-info-02, #4D82F3);
            border: none;
            color: white;
        }

        .border-right {
            border-right: 1px solid #CBD4E1;
        }

        .box-car-pic {
            display: flex;
            width: 370px;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            gap: 24px;
        }

        .car-border {
            padding: 24px;
            border: 1px solid #CBD4E1;
        }

        .car-img {
            width: 100%;
            height: auto;
            flex-shrink: 0;
        }

        .btn-accessory {
            width: 100%;
            display: flex;
            padding: 8px;
            justify-content: center;
            align-items: center;
            gap: 8px;
            flex: 1 0 0;
            border-radius: 6px;
            border: 1px solid #4D82F3;
            background: rgba(77, 130, 243, 0.07);
        }

        .car-table-header > th {
            border: none !important;
        }

        #car-table-input > td {
            border: none;
        }

        #table-car-premium-header {
            border-radius: 6px 0px 0px 0px;
            border-right: 1px solid #F6F8FC;
            background: #E2E8F0;
            height: 55px;
        }

        #table-car-premium-sub-header {
            height: 66px;
            border-right: 1px solid #E2E8F0;
            background: #F6F8FC;
        }

        th > .push {
            margin: 0px !important;
        }

        th {
            border: 1px solid #E2E8F0 !important;
            padding: 8px !important;
        }

        .block-car-coverage td {
            border: 1px solid #E2E8F0 !important;
            padding: 8px !important;
        }


        .table-background-gray {
            background: var(--bs-table-striped-bg);
        }

        .table-background-white {
            --bs-table-accent-bg: var(--bs-table-bg) !important;

        }

        .btn-apply-all {
            display: flex;
            width: 180px;
            height: 42px;
            padding: 8px 16px;
            justify-content: center;
            align-items: center;
            gap: 8px;
            border-radius: 6px;
            border: none;
            color: white;
            background: var(--action-color-info-02, #4D82F3);
        }

        .block-submit > .row.push {
            margin: 0 !important;
        }

        .car-wrap .font-w800 {
            display: none;
        }

        .fa-chevron-up {
            /*transform: rotate(0deg) ;*/
            /*transform: rotate(180deg) ;*/
            transition: all 0.3s ease;
            display: inline-block;

        }

        .collapsed > .fa-chevron-up {
            /*background: red !important;*/
            transform: rotate(180deg) !important;
        }

        .btn-accessory {
            color: white;
            border-radius: 6px;
            background: var(--action-color-info-02, #4D82F3);
            width: 180px;
            height: 42px;
        }
    </style>
@endpush
@section('content')

    @include('admin.components.creator')
    <form id="save-form">
        <x-forms.hidden id="lt_rental_id" :value="$d?->id"/>
        @include('admin.request-premium.sections.form-longterm-rental-details')
        @include('admin.request-premium.sections.form-customer-details')
        @include('admin.request-premium.sections.form-cars-details')
        @include('admin.request-premium.sections.form-remark')
        <div class="block {{ __('block.styles') }}">
                    <div class="block-content box-padding-bottom block-submit">
                        <x-forms.submit-group
                            :optionals="['url' => 'admin.request-premium.index', 'view' => empty($view) ? null : $view,'manage_permission' => Actions::Manage . '_' . Resources::RequestPremium]"/>
                    </div>
                </div>
            </form>

            @include('admin.request-premium.modals.modal-accessory')

@endsection
@include('admin.request-premium.scripts.request-premium-car-script')
@include('admin.request-premium.scripts.request-premium-premium-script')
@include('admin.request-premium.scripts.request-premium-modal-script')
@include('admin.components.select2-default')
@include('admin.components.sweetalert')
@include('admin.long-term-rentals.scripts.rental-month-script')
@include('admin.components.form-save', [
    'store_uri' => route('admin.request-premium.store'),
])
@push('custom_styles')
    <link rel='stylesheet'
          href='https://bootstrap-tagsinput.github.io/bootstrap-tagsinput/dist/bootstrap-tagsinput.css'>
    <style>
        .bootstrap-tagsinput {
            display: block;
            width: 100%;
            padding: 0.375rem 0.75rem;
            line-height: 1.5;
            color: #343a40;
            background-color: #fff;
            background-clip: padding-box;
            border: 1px solid #d1d8ea;
            border-radius: 0.25rem;
            background: #f1f1f1 !important;
            pointer-events: none;
        }

        .bootstrap-tagsinput .tag {
            margin-right: 3px;
            color: white;
            background: #157CF2;
            padding: 3px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: normal;

        }
    </style>
@endpush
@push('scripts')
    <script>

        $edit = '{{isset($edit)}}';
        view = '{{isset($view)}}';
        if ($edit) {
            $('#job_type').prop('disabled', true)
            $('#rental_duration').prop('disabled', true)
            $('#job_type').prop('disabled', true)
            $('#rental_duration').prop('disabled', true)
            $('.request_premium_date').prop('disabled', true)
            $('#customer_id').prop('disabled', true)
            $('#customer').prop('disabled', true)
            $('#customer_group').prop('disabled', true)
            $('#customer_email').prop('disabled', true)
            $('#customer_phone').prop('disabled', true)
        }

        if (view) {
            $('#job_type').prop('disabled', true)
            $('#rental_duration').prop('disabled', true)
            $('#job_type').prop('disabled', true)
            $('#rental_duration').prop('disabled', true)
            $('.request_premium_date').prop('disabled', true)
            $('.sum_insured_accessories').prop('disabled', true)
            $('.sum_insured').prop('disabled', true)
            $('.sum_insured_car').prop('disabled', true)
            $('input').prop('disabled', true)
            $('select').prop('disabled', true)
        }
        $(document).ready(function () {
            $('.rounded-pill').insertAfter('.lt_no')
            $('.badge').addClass('ms-1 me-1')
        })
    </script>
@endpush
