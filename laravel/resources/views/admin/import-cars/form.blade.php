@extends('admin.layouts.layout')

@section('page_title', $page_title)
@push('styles')
    <style>
        .tag-field {
            display: flex;
            flex-wrap: wrap;
            /* height: 50px; */
            padding: 3px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }

        .form-control.js-tag-input {
            border: none;
            transition: none;
        }

        input {
            border: 0;
            outline: 0;
        }

        .tag {
            display: flex;
            align-items: center;
            height: 30px;
            margin-right: 5px;
            margin-bottom: 1px;
            padding: 0 8px;
            color: #fff;
            background: #0665d0;
            border-radius: 6px;
            cursor: pointer;
        }

        .tag-close {
            display: inline-block;
            margin-left: 0;
            width: 0;
            transition: 0.2s all;
            overflow: hidden;
        }

        .tag:hover .tag-close {
            margin-left: 10px;
            width: 10px;
        }
    </style>
@endpush

@section('content')
    <div class="block {{ __('block.styles') }}">
        <div class="block-content">
            <form id="save-form">
                @include('admin.import-cars.sections.purchaser')
                @include('admin.import-cars.sections.car-detail')
                @include('admin.import-cars.sections.car-detail-form')
                <x-forms.hidden id="import_id" name="import_id" :value="$import_car->id" />
                <div class="row push">
                    <div class="col-sm-12 text-end">
                        @auth
                            <a class="btn btn-secondary" href="{{ route('admin.import-cars.index') }}">{{ __('lang.back') }}</a>
                        @endauth
                        @if (empty($view))
                            {{-- <button type="button"
                                class="btn btn-primary btn-save-review" data-status="{{ \App\Enums\ImportCarStatusEnum::PENDING_REVIEW }}">{{ __('lang.save_draft') }}</button> --}}
                            <button type="button" class="btn btn-primary btn-save-review"
                                data-status="{{ \App\Enums\ImportCarStatusEnum::SENT_REVIEW }}">{{ __('lang.save') }}</button>
                        @endif

                    </div>
                </div>

            </form>
        </div>
    </div>
@endsection

@include('admin.components.select2-default')
@include('admin.components.sweetalert')
@include('admin.components.form-save', [
    'store_uri' => route('admin.import-cars.store'),
])
@include('admin.import-cars.scripts.import-cars-script')
@include('admin.import-cars.scripts.input-tag')
@include('admin.components.date-input-script')

@push('scripts')
    <script>
        $status = '{{ isset($view) }}';

        if ($status) {
            $('.engine').prop('disabled', true);
            $('.chassis').prop('disabled', true);
            $('.installation_completed_date').prop('disabled', true);
            $('.installation_completed_date').css("background-color", "#e9ecef");
            $('.delivery_date').css("background-color", "#e9ecef");
            $('.delivery_date').prop('disabled', true);
            $('.delivery_place').prop('disabled', true);

        }

        $('#requester_name').prop('disabled', true);
        $('#requester_department').prop('disabled', true);
        $('#approver').prop('disabled', true);
        $('#approve_department').prop('disabled', true);
        $('#approve_date').prop('disabled', true);
        $('#approve_date').css("background-color", "#e9ecef");
        $('#purchase_order_no').prop('disabled', true);
        $('#purchase_order_date').prop('disabled', true);
        $('#purchase_order_date').css("background-color", "#e9ecef");
        $('.delivery_date_pending').css("background-color", "#e9ecef");
        // $('.border-date-invalid').css("border-color", "#e04f1a");
        $('.delivery_date_pending').prop('disabled', true);
        $('#need_date').css("background-color", "#e9ecef");
        $('#need_date').prop('disabled', true);
        $('#remark').prop('disabled', true);
        $('#creditor_id').prop('disabled', true);
        $('#name').prop('disabled', true);
        $('#name2').prop('disabled', true);
        $('#car_entry').prop('disabled', true);
        $('#car_inspection').prop('disabled', true);
        $('.delivery_place_modal').prop('disabled', true);
        $('.delivery_date_modal').css("background-color", "#e9ecef");
        $('.delivery_date_modal').prop('disabled', true);

        var count_table = "{{ sizeof($purchase_requisition_cars) }}";
        var sectorArray = [];
        for (var i = 0; i < count_table; i++) {
            sectorArray.push(i);
        }

        for (var s in sectorArray) {
            var sector = sectorArray[s];
            (function(sec) {
                $('#sub-table-' + sec).hide();
                $('#arrow-' + sec).on("click", function(e) {
                    if ($('#sub-table-' + sec).hasClass('hidden')) {
                        $('#sub-table-' + sec).show();
                        $('#sub-table-' + sec).removeClass('hidden');
                        $('#arrow-' + sec).removeClass('fa-angle-right');
                        $('#arrow-' + sec).addClass('fa-angle-down');

                    } else {
                        $('#sub-table-' + sec).hide();
                        $('#sub-table-' + sec).addClass('hidden');
                        $('#arrow-' + sec).addClass('fa-angle-right');
                    }
                });


            }(sector))
        }

        $(".btn-save-review").on("click", function() {
            var loggedIn = "{{{ (Auth::user()) ? Auth::user() : null }}}";
            let storeUri = "{{ route('admin.import-cars.store') }}";
            let storeUriDealer = "{{ route('import-car-dealers.store') }}";
            var formData = new FormData(document.querySelector('#save-form'));
            var status = $(this).attr('data-status');
            formData.append('status', status);
            if (loggedIn) {
                console.log('1');
                saveForm(storeUri, formData);
            } else {
                console.log('2');
                saveForm(storeUriDealer, formData);
            }
        });


        $('.copy_link').click(function(e) {
            e.preventDefault();
            var copyText = window.location.host + '/import-car-dealers/' + "{{ $import_car->id }}" + '/edit';
            console.log(copyText);
            document.addEventListener('copy', function(e) {
                e.clipboardData.setData('text/plain', copyText);
                e.preventDefault();
            }, true);

            document.execCommand('copy');
            var status_update = '{{ImportCarStatusEnum::SENT_REVIEW}}';
            $.ajax({
                type: 'GET',
                url: "{{ route('admin.import-cars.updateStatus', $import_car->id) }}",
                data: {
                    status_update: status_update
                },
                success: function(data) {
                    // addImportCarVue.test(data.success);
                    copyAlert('คัดลอกแล้ว');
                    console.log(data.success);
                }
            });
        });

        function openShareDealerModal() {
            var tags = @if($d->creditor && $d->creditor->email) [ @json($d->creditor->email) ] @else [] @endif;
            var $tags = document.querySelector('.js-tags');
            if (tags.length > 0) {
                render(tags, $tags);
            }
            $("#modal-import-cars").modal("show");
        }

        function sendMail() {
            var id = document.getElementById("import_id").value;
            var $tags = document.querySelector('.js-tags');
            showLoading();
            axios.get("{{ route('admin.import-cars.send-email') }}", {
                params: {
                    id: id,
                    tags: tags
                }
            }).then(response => {
                hideLoading();
                $("#modal-import-cars").modal("hide");
                if (response.data.success) {
                    mySwal.fire({
                        title: "{{ __('lang.store_success_title') }}",
                        text: 'ส่ง E-mail เรียบร้อยแล้ว',
                        icon: 'success',
                        confirmButtonText: "{{ __('lang.ok') }}"
                    }).then(value => {
                        if (value) {
                            //
                        }
                    });
                } else {
                    mySwal.fire({
                        title: "{{ __('lang.store_error_title') }}",
                        text: response.data.message,
                        icon: 'error',
                        confirmButtonText: "{{ __('lang.ok') }}",
                    }).then(value => {
                        if (value) {
                            //
                        }
                    });
                }
            });
        }
    </script>
@endpush
