@extends('admin.layouts.layout')
@section('page_title', 'ปฏิทินตารางงานพนักงานขับรถ')
@push('custom_styles')
    <style>
        .input-group-text {
            padding: 0.7rem .75rem;
            background-color: transparent;
            border-radius: 0;
            color: #6c757d;
        }

        .grey-text {
            color: #858585;
        }

        .size-text {
            font-size: 14px;
        }

        .nav-link {
            color: #343a40;
        }

        .nav-tabs-alt .nav-link.active,
        .nav-tabs-alt .nav-item.show .nav-link {
            color: #0665d0;
        }

        .fc-event {
            cursor: pointer;
        }

        .label {
            float: left;
            height: 20px;
            width: 20px;
            margin-bottom: 15px;
            clear: both;
            border-radius: 50%;
            margin-right: 10px;
            margin-top: 2px;
        }

        .head-label {
            margin-right: 20px;
        }
        
    </style>
@endpush
@section('content')
    <div class="container-fluid">
        <div class="block {{ __('block.styles') }}">
            <div class="block-header ">
                <h3 class="block-title" style="width: 70%;">แสดงงานพนักงานขับรถทั้งหมดในระบบ</h3>
                <h3 class="block-title" style="width: 30%;">สถานะของงาน</h3>
            </div>
            <div class="block block-fx-pop">
                <div class="block-content block-content-full">
                    {{-- d-flex align-items-start justify-content-end --}}
                    <div class="row push mt-2 d-flex ">
                        <form action="" method="GET" id="" class="">
                            <div class="row push">
                                <div class="col-sm-3 ">
                                    <x-forms.select-option id="driver_id" :value="$driver_id" :list="null"
                                        :label="__('driving_jobs.driver_name')" :optionals="[
                                            'ajax' => true,
                                            'default_option_label' => $driver_name,
                                        ]" />
                                </div>
                                <div class="col-sm-auto align-items-end d-flex gap-3">
                                    <a href="{{ URL::current() }}"
                                        class="btn btn-secondary btn-clear-search">{{ __('lang.clear_search') }}</a>
                                    <button type="submit" class="btn btn-primary">{{ __('lang.search') }}</button>
                                </div>
                                <div class="col d-flex align-items-start justify-content-end">
                                    <div class="col-auto mt-auto head-label">
                                        <span class="label" style="background-color: #3c90df;"></span>
                                        <span>รอดำเนินการ</span>
                                    </div>
                                    <div class="col-auto mt-auto head-label">
                                        <span class="label" style="background-color: #e69f17;"></span>
                                        <span>อยู่ระหว่างดำเนินการ</span>
                                    </div>
                                    <div class="col-auto mt-auto head-label">
                                        <span class="label" style="background-color: #6f9c40;"></span>
                                        <span>เสร็จสิ้น</span>
                                    </div>
                                    <div class="col-auto mt-auto head-label">
                                        <span class="label" style="background-color: #e04f1a;"></span>
                                        <span>ยกเลิก</span>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="block block-fx-pop">
                        <div class="block-content block-content-full">
                            <div id="calendar"></div>
                        </div>
                        @include('admin.driving-jobs.modals.job-detail')
                    </div>
                </div>
            </div>
        </div>
    @endsection
    @push('scripts')
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.5/index.global.min.js'></script>
        <script>
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            var eventsUrl = "{{ route('admin.driving-jobs.calendar') }}";
            document.addEventListener('DOMContentLoaded', function() {
                var calendarEl = document.getElementById('calendar');
                var calendar = new FullCalendar.Calendar(calendarEl, {
                    locale: 'th',
                    displayEventTime: false,
                    events: @json($rental),
                    dayMaxEventRows: true,
                    views: {
                        timeGrid: {
                            dayMaxEventRows: 1
                        }
                    },
                    headerToolbar: {
                        left: '',
                        center: 'title',
                        right: 'prev,today,next',
                    },
                    buttonText: {
                        prev: 'ก่อนหน้า',
                        next: 'ถัดไป',
                        today: 'วันนี้',
                    },
                    moreLinkContent: function(args) {
                        return '+' + args.num + ' เพิ่มเติม';
                    },
                    eventDataTransform: function(event) {
                        if (event.status == "{{ DrivingJobStatusEnum::PENDING }}") {
                            event.color = "#3c90df";
                        } else if (event.status == "{{ DrivingJobStatusEnum::IN_PROCESS }}") {
                            event.color = "#e69f17";
                        } else if (event.status == "{{ DrivingJobStatusEnum::COMPLETE }}") {
                            event.color = "#6f9c40";
                        } else if (event.status == "{{ DrivingJobStatusEnum::CANCEL }}") {
                            event.color = "#e04f1a";
                        }
                        return event;
                    },
                    eventClick: function(event) {
                        $('.fc .fc-popover').hide();
                        $('#modal-rental').modal("show")
                        axios.get("{{ route('admin.driving-jobs.calendar-ajax') }}", {
                            params: {
                                id: event.event.id,
                            }
                        }).then(response => {
                            if (response.data) {
                                $("#worksheet_no").empty();
                                $("#driver_name").empty();
                                $("#job_type").empty();
                                $("#parent_id").empty();
                                $("#self_drive_type").empty();
                                $("#license_plate").empty();
                                $("#start_date").empty();
                                $("#end_date").empty();
                                $("#origin_name").empty();
                                $("#destination_name").empty();
                                $("#cus_del").empty();
                                $("#license_plate_chassis").empty();
                                $("#customer_name").empty();
                                $("#worksheet_no").html(response.data.worksheet_no);
                                $("#driver_name").html(response.data.driver_name);
                                $("#job_type").html(response.data.job_type_th);
                                $("#ref_no").html(response.data.ref_no ? response.data.ref_no :
                                    '-');
                                $("#self_drive_type").html(response.data.self_drive_type);
                                $("#license_plate").html(response.data.license_plate);
                                $("#start_date").html(response.data.start_date);
                                $("#end_date").html(response.data.end_date);
                                $("#origin_name").html(response.data.origin_name);
                                $("#destination_name").html(response.data.destination_name);
                                $("#cus_del").html(response.data.label_name_customer);
                                $("#license_plate_chassis").html(response.data
                                    .label_name_license_plate);
                                if (response.data.customer_tel) {
                                    $("#customer_name").html(response.data.customer_name + " (" +
                                        response.data.customer_tel + ")");
                                } else {
                                    $("#customer_name").html(response.data.customer_name);
                                }
                            }
                        });
                    }
                });
                calendar.render();
            });
        </script>
    @endpush
    @include('admin.components.select2-default')
    @include('admin.components.sweetalert')
    @include('admin.components.list-delete')
    @include('admin.components.date-input-script')
    @include('admin.components.select2-ajax', [
        'id' => 'driver_id',
        'url' => route('admin.util.select2.driver'),
    ])
