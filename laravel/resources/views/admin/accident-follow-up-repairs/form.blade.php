@extends('admin.layouts.layout')

@section('page_title', $page_title)
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

        .img-fluid {
            /* width: 250px; */
            height: 100px;
            object-fit: cover;
        }

        .car-border {
            border: 1px solid #CBD4E1;
            width: 400px;
            border-radius: 6px;
            color: #475569;
            padding: 2rem;
            height: fit-content;
        }

        .hide {
            display: none !important;
        }

        .show {
            display: block !important;
            opacity: 1;
            animation: fade 1s;
        }

        .vl {
            border-right: 1px solid #CBD4E1;
            height: 30px;
            padding-right: 20px;
        }

        .th-topic {
            background-color: #F6F8FC;
            height: 100px;

        }

        .vl-topic {
            height: 50px;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .table thead th:first-child {
            border-top-left-radius: 0px !important;
        }

        .table thead th:last-child {
            border-top-right-radius: 0px !important;
        }

        @keyframes fade {
            0% {
                opacity: 0;
            }

            100% {
                opacity: 1;
            }
        }

        .size-text {
            font-size: 16px;
            font-weight: bold;
        }


        .expanded {
            width: 400px;
            height: 400px;
        }

        .modal-dialog {
            margin: 0;
            max-width: 100%;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .modal-image {
            max-width: 100%;
            max-height: 100%;
        }

        .image-container {
            position: relative;
            display: inline-block;
        }

        .overlay-icon {
            position: absolute;
            /* top: 90%; */
            /* right: 5px; */
            margin: 85% -13%;
            transform: translate(-50%, -50%);
            font-size: 24px;
            color: white;
            background-color: rgba(0, 0, 0, 0.5);
            padding: 10px;
            border-radius: 50%;
            cursor: pointer;
        }

        .container-section {
            display: grid;
            grid-template-columns: 1fr 0.1fr 1.5fr;
            grid-template-rows: 1fr;
            gap: 9px 9px;
            grid-auto-flow: row dense;
            grid-template-areas: "left-section center-section right-section";
        }

        .left-section {
            grid-area: left-section;

        }

        .center-section {
            grid-area: center-section;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .right-section {
            grid-area: right-section;
        }

        .svg-container:hover {
            cursor: pointer;
            background-color: #e2e8f0;
            border-radius: 50%;
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
        @include('admin.accident-follow-up-repairs.sections.car-info')
        @include('admin.accident-follow-up-repairs.sections.garage-detail')
        @include('admin.accident-follow-up-repairs.sections.follow-up-repair')
        {{-- @include('admin.components.creator')
        @include('admin.accident-orders.sections.car-info-selected')
        @include('admin.accident-orders.sections.accident-all')
        @include('admin.accident-orders.sections.accident-open') --}}

        <div class="block {{ __('block.styles') }}">
            <div class="block-content">
                <div class="row push">
                    <div class="col-12 text-end">
                        @if (!isset($call_center))
                            <a class="btn btn-outline-secondary btn-custom-size"
                                href="{{ route('admin.accident-follow-up-repairs.index') }}">{{ __('lang.back') }}</a>
                        @else
                            <a class="btn btn-outline-secondary btn-custom-size"
                                href="{{ route('admin.call-center-follow-up-repairs.index') }}">{{ __('lang.back') }}</a>
                        @endif
                        @if (!isset($view))
                            <button type="button"
                                class="btn btn-primary btn-custom-size btn-save-form ">{{ __('lang.save') }}</button>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        <x-forms.hidden id="id" :value="$d->id" />
        @include('admin.components.transaction-modal')
    </form>
@endsection
@include('admin.components.select2-default')
@include('admin.components.sweetalert')
@include('admin.components.date-input-script')
@include('admin.accident-follow-up-repairs.scripts.follow-up-script')
{{-- @include('admin.accident-orders.scripts.accident-order-script')
@include('admin.accident-orders.scripts.accident-list-script')
@include('admin.accident-orders.scripts.accident-repair-open-script') --}}


@include('admin.components.select2-ajax', [
    'id' => 'car_id',
    'url' => route('admin.accident-orders.default-car-id'),
])

@include('admin.components.select2-ajax', [
    'id' => 'report_id',
    'modal' => '#modal-repair-accident',
    'url' => route('admin.util.select2-accident.accident-list'),
    'parent_id' => 'car_id',
])

@include('admin.components.select2-ajax', [
    'id' => 'garage_id',
    'modal' => '#modal-repair-accident',
    'url' => route('admin.util.select2-accident.garage-list'),
])
@include('admin.components.form-save', [
    'store_uri' => route('admin.accident-follow-up-repairs.store'),
])
@push('scripts')
    <script>
        $("#cradle_id").prop('disabled', true);
        $("#repair_date").prop('disabled', true);
        $("#amount_completed").prop('disabled', true);
        $("#scheduled_completion_date").prop('disabled', true);
        $("#actual_repair_date").prop('disabled', true);

        $(document).ready(function() {
            function addDaysToRepairDate(days) {
                var repairDateInput = $('#repair_date').val();
                var amountCompletedInput = $('#amount_completed').val();


                if (repairDateInput === '' || amountCompletedInput === '') {
                    return;
                }

                var repairDate = new Date(repairDateInput);
                var amountCompleted = parseInt(amountCompletedInput, 10);

                if (isNaN(repairDate)) {
                    return;
                }

                var additionalDays = amountCompleted * days;
                repairDate.setDate(repairDate.getDate() + additionalDays);

                var formattedDate = repairDate.toISOString().split('T')[0];
                $('#scheduled_completion_date').val(formattedDate);
            }

            addDaysToRepairDate(1);

            $('#repair_date, #amount_completed').on('change', function() {
                addDaysToRepairDate(1);
            });



        });
    </script>
@endpush
