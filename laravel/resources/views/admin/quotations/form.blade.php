@extends('admin.layouts.layout')

@section('page_title', $page_title)
@section('history')
    @include('admin.components.btns.history')
@endsection
@push('styles')
    <style>
        .form-progress-bar .form-progress-bar-header {
            text-align: left;

        }

        .form-progress-bar .form-progress-bar-steps {
            margin: 30px 0 10px 0;
        }

        div.check-status {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100%;
            flex-direction: column;
        }

        .form-progress-bar .form-progress-bar-steps li,
        .form-progress-bar .form-progress-bar-labels li {
            width: 16.6%;
            float: left;
            position: relative;
        }

        .form-progress-bar-line {
            background-color: #f3f3f3;
            content: "";
            height: 2px;
            left: 0;
            /* position: absolute; */
            right: 0;
            top: 50%;
            transform: translateY(-50%);
            /* width: 70%; */
            border-bottom: 1px solid #dddddd;
            border-top: 1px solid #dddddd;
            margin-left: 20px;
            margin-right: 30px;
        }

        .form-progress-bar .form-progress-bar-steps span.check {
            background-color: #dddddd;
            border-radius: 50%;
            display: inline-block;
            height: 40px;
            line-height: 40px;
            position: relative;
            text-align: center;
            width: 40px;
        }

        .form-progress-bar .form-progress-bar-steps span.pending {
            background-color: #dddddd;
            border-radius: 50%;
            display: inline-block;
            height: 40px;
            line-height: 40px;
            position: relative;
            text-align: center;
            width: 40px;
        }

        .form-progress-bar .form-progress-bar-steps span.pending-secondary {
            background-color: #dddddd;
            border-radius: 50%;
            display: inline-block;
            height: 40px;
            line-height: 40px;
            position: relative;
            text-align: center;
            width: 40px;
        }

        .form-progress-bar .form-progress-bar-steps span.reject {
            background-color: #dddddd;
            border-radius: 50%;
            display: inline-block;
            height: 40px;
            line-height: 40px;
            position: relative;
            text-align: center;
            width: 40px;
        }

        .form-progress-bar .form-progress-bar-steps span.check,
        .form-progress-bar .form-progress-bar-steps span.check {
            background-color: #6f9c40;
            color: #ffffff;
        }

        .form-progress-bar .form-progress-bar-steps span.pending,
        .form-progress-bar .form-progress-bar-steps span.pending {
            background-color: #e69f17;
            color: #ffffff;
        }

        .form-progress-bar .form-progress-bar-steps span.pending-secondary,
        .form-progress-bar .form-progress-bar-steps span.pending-secondary {
            background-color: #909395;
            color: #ffffff;
        }

        .form-progress-bar .form-progress-bar-steps span.reject,
        .form-progress-bar .form-progress-bar-steps span.reject {
            background-color: red;
            color: #ffffff;
        }

        .bg-pending-previous {
            background-color: #909395;
        }

        .bg-check {
            background-color: #6f9c40;
        }

        .bg-pending {
            background-color: #e69f17;
        }

        /*.table-bordered > :not(caption) > * > * {*/
        /*    border-width: 1px 1px!important;*/
        /*}*/

        .table-bordered-custom thead,
        .table-bordered-custom tbody,
        .table-bordered-custom tfoot,
        .table-bordered-custom tr,
        .table-bordered-custom td,
        .table-bordered-custom th {
            border: 1px solid #cbd4e1!important;
        }

        /*td {*/
        /*    border: 1px solid #cbd4e1!important;*/
        /*}*/

        /*.table-bordered-custom thead th:last-child {*/
        /*    border-top-right-radius: 15px!important;*/
        /*}*/
    </style>
@endpush
@section('btn-nav')
    @if (isset($lt_rental_lines))
        <nav class="flex-sm-00-auto ml-sm-3">
            <a target="_blank" href="{{ route('admin.quotations.long-term-rental-pdf', ['quotation' => $d]) }}"
                class="btn btn-primary">
                พิมพ์ใบเสนอราคา
            </a>
        </nav>
    @else
        <nav class="flex-sm-00-auto ml-sm-3">
            <a target="_blank"
                href="{{ route('admin.quotations.short-term-rental-pdf', ['rental_bill_id' => $d->rental_bill_id]) }}"
                class="btn btn-primary">
                พิมพ์ใบเสนอราคา
            </a>
        </nav>
    @endif
@endsection

@section('content')
    {{-- @if (isset($approve_line_list) && $approve_line_list)
        @include('admin.components.step-progress')
    @endif --}}
    <x-approve.step-approve :configenum="null" :id="$d->id" :model="get_class($d)" />
    <div class="block {{ __('block.styles') }}">
        <div class="block-content">
            <form id="save-form">
                @include('admin.quotations.sections.customer')
                @if (isset($lt_rental_lines))
                    @include('admin.quotations.sections.rental')
                @else
                    @include('admin.quotations.sections.short-term-rental')
                @endif
                @include('admin.quotations.sections.condition')

                <x-forms.hidden id="id" name="id" :value="$d->id" />
                <div class="row push">
                    <div class="col-sm-12 text-end">
                        <a class="btn btn-secondary" href="{{ route('admin.quotations.index') }}">{{ __('lang.back') }}</a>

                        @if (empty($view))
                            @can(Actions::Manage . '_' . Resources::Quotation)
                                <button type="button" class="btn btn-info btn-save-form">{{ __('lang.save') }}</button>
                            @endcan
                        @endif
                    </div>
                </div>
            </form>
        </div>
    </div>
    @include('admin.components.transaction-modal')
@endsection

@include('admin.components.sweetalert')
@include('admin.components.form-save', [
    'store_uri' => route('admin.quotations.store'),
])
@include('admin.quotations.scripts.condition-quotation-script')


@push('scripts')
    <script>
        $('#customer_type').prop('disabled', true);
        $('#customer_id').prop('disabled', true);
        $('#customer_name').prop('disabled', true);
        $('#customer_tel').prop('disabled', true);
        $('#customer_address').prop('disabled', true);


        $status = '{{ isset($view) }}';

        if ($status) {
            $('.form-control').prop('disabled', true);
            $("input[type=checkbox]").attr('disabled', true);
            $('input[name=check_vat]').prop('disabled', true);
        }

        vat_include = '{{ $d->check_vat }}';
        if (vat_include == '{{ STATUS_ACTIVE }}') {
            $('#include_vat').show();
            $('#exclude_vat').hide();
            price = @json($lt_rental_line_month);
            price.forEach(function(item, index) {
                $('#price' + index).html((item.total_price).toString().replace(/\B(?=(\d{3})+(?!\d))/g, ","));
            })
        } else {
            $('#exclude_vat').show();
            $('#include_vat').hide();
            price = @json($lt_rental_line_month);
            price.forEach(function(item, index) {
                $('#price' + index).html((item.subtotal_price).toString().replace(/\B(?=(\d{3})+(?!\d))/g, ","));
            })
        }

        $('input[name=check_vat]').click(function() {
            check_vat = $('input[name=check_vat]:checked').val();
            if (check_vat == '{{ STATUS_ACTIVE }}') {
                $('#include_vat').show();
                $('#exclude_vat').hide();
                price = @json($lt_rental_line_month);
                price.forEach(function(item, index) {
                    $('#price' + index).html((item.total_price).toString().replace(/\B(?=(\d{3})+(?!\d))/g,
                        ","));
                })
            } else {
                $('#exclude_vat').show();
                $('#include_vat').hide();
                price = @json($lt_rental_line_month);
                price.forEach(function(item, index) {
                    $('#price' + index).html((item.subtotal_price).toString().replace(
                        /\B(?=(\d{3})+(?!\d))/g, ","));
                })
            }
        });
    </script>
@endpush
