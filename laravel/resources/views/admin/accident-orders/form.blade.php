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
        @include('admin.components.creator')
        @include('admin.accident-orders.sections.car-info-selected')
        @include('admin.accident-orders.sections.accident-all')
        @include('admin.accident-orders.sections.accident-open')

        <div class="block {{ __('block.styles') }}">
            <div class="block-content">
                <div class="row push">
                    <div class="col-12 text-end">
                        <a class="btn btn-outline-secondary btn-custom-size"
                            href="{{ route('admin.accident-orders.index') }}">{{ __('lang.back') }}</a>

                        <button type="button"
                            class="btn btn-primary btn-custom-size btn-save-form ">{{ __('lang.save') }}</button>
                    </div>
                </div>
            </div>
        </div>
        @include('admin.components.transaction-modal')
    </form>
@endsection
@include('admin.accident-orders.scripts.accident-order-script')
@include('admin.accident-orders.scripts.accident-list-script')
@include('admin.accident-orders.scripts.accident-repair-open-script')
@include('admin.components.date-input-script')
@include('admin.components.select2-default')
@include('admin.components.sweetalert')
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
    'store_uri' => route('admin.accident-orders.store'),
])
@push('scripts')
    <script>
        $(document).ready(function() {
            $(document).on('click', ".toggle-table", function() {

                var tr_block = $(this).parent().next('.tr-block').toggle();
                $(tr_block).parent().next('.tr-block').toggle();
                $(this).children().toggleClass('icon-arrow-down text-muted').toggleClass(
                    'icon-arrow-up text-muted');
            })

            $('#car_id').change(function() {
                var selectedValue = $('#car_id :selected').val();
                if (selectedValue != undefined) {
                    $('#modal-history').show();
                } else {
                    $('#modal-history').hide();
                }
            });
        });

        $("#search").on('click', function(e) {
            var data = $('#report_id :selected').val();
            axios.get("{{ route('admin.accident-orders.get-accident-list') }}", {
                params: {
                    id: data,
                }
            }).then(response => {
                if (response.data.success) {
                    addAccidentRepairOpenVue.addAccidentList(response.data.data);
                }
            });
        });



        $(document).ready(function() {
            $("#to_right").click(function() {
                addAccidentRepairOpenVue.addToRight();
            });
        });

        $(document).ready(function() {
            $("#to_left").click(function() {
                addAccidentRepairOpenVue.addToLeft();
            });
        });

        $(document).on('change', ".form-check-input-each", function() {
            if ($('.form-check-input-each:checked').length == $('.form-check-input-each').length) {
                $('#selectAll').prop('checked', true);
            } else {
                $('#selectAll').prop('checked', false);
            }
        });

        $(document).on('change', ".form-check-input-each2", function() {
            if ($('.form-check-input-each2:checked').length == $('.form-check-input-each2').length) {
                $('#selectAll2').prop('checked', true);
            } else {
                $('#selectAll2').prop('checked', false);
            }
        });

        $('#selectAll').change(function() {
            var isChecked = $(this).prop('checked');
            $('.form-check-input-each').prop('checked', isChecked);
            addAccidentRepairOpenVue.selectAllLeft(isChecked);
        });


        $('#selectAll2').change(function() {
            var isChecked = $(this).prop('checked');
            $('.form-check-input-each2').prop('checked', isChecked);
            addAccidentRepairOpenVue.selectAllRight(isChecked);
        });
    </script>
@endpush
