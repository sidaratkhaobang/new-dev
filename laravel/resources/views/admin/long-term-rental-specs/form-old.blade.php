@extends('admin.layouts.layout')

@section('page_title', $page_title)
@section('history')
    @include('admin.components.btns.history')
@endsection
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

        .form-progress-bar .form-progress-bar-header {
            text-align: left;

        }

        .form-progress-bar .form-progress-bar-steps {
            margin: 30px 0 10px 0;
            /* display: flex;
                            justify-content: center;
                            align-items: center; */
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
    </style>
@endpush
@section('content')
{{-- @if (isset($approve_line_list) && $approve_line_list) --}}
        {{-- @include('admin.components.step-progress') --}}
    {{-- @endif --}}
    <x-approve.step-approve :configenum="ConfigApproveTypeEnum::LT_SPEC_ACCESSORY" :id="$d->id" :model="get_class($d)" />

    <div class="block {{ __('block.styles') }}">
        <div class="block-header">
            <h3 class="block-title">ข้อมูลใบเช่ายาว: {{ $d->worksheet_no }}
                @if ($d->spec_status == SpecStatusEnum::REJECT)
                    {!! badge_render(
                        __('long_term_rentals.spec_status_class_' . $d->spec_status),
                        __('long_term_rentals.spec_status_' . $d->spec_status),
                    ) !!}
                @endif
            </h3>
        </div>
        <div class="block-content">
            <form id="save-form">
                @include('admin.long-term-rental-specs.sections.rental-detail')
                @include('admin.long-term-rental-specs.sections.upload')
                @include('admin.long-term-rental-specs.sections.car-accessory')

                @if (!isset($view_only))
                    <div class="row">
                        <div class="col-md-12 text-end">
                            @if (!isset($accessory_controller))
                            @if (strcmp($d->spec_status, SpecStatusEnum::PENDING_CHECK) !== 0)
                                @can(Actions::Manage . '_' . Resources::LongTermRentalSpec)
                                    <a href="{{ route('admin.long-term-rental.specs.tor.create', ['rental' => $d->id]) }}"
                                        class="btn btn-primary">{{ __('lang.add') }}</a>
                                @endcan
                            @endif
                            @endif
                        </div>
                    </div>
                @endif
                {{-- @if (strcmp($d->spec_status, SpecStatusEnum::PENDING_CHECK) === 0)
                    @include('admin.long-term-rental-specs.sections.car-check')
                    @include('admin.long-term-rental-specs.modals.dealer-modal')
                @endif --}}

                <x-forms.hidden id="id" :value="$d->id" />
                <x-forms.hidden id="status" :value="$d->status" />
                <div class="row push mt-4">
                    <div class="col-sm-12 text-end">
                        <a class="btn btn-secondary" href="{{ $redirect_route }}">{{ __('lang.back') }}</a>
                        @if (!isset($view_only))
                            @can(Actions::Manage . '_' . Resources::LongTermRentalSpec)
                                <button type="button" class="btn btn-info btn-save-form">{{ __('lang.save_draft') }}</button>
                                <button type="button" class="btn btn-primary btn-save-review"
                                    data-status="{{ SpecStatusEnum::PENDING_CHECK }}">{{ __('lang.save') }}</button>
                            @endcan
                        @endif
                    </div>
                </div>
            </form>
        </div>
    </div>
    @include('admin.components.transaction-modal')
@endsection
{{-- @include('admin.long-term-rental-specs.modals.share-dealer') --}}
@include('admin.components.select2-default')
@include('admin.components.sweetalert')
@include('admin.components.date-input-script')
@include('admin.components.form-save', [
    'store_uri' => route('admin.long-term-rental.specs.store'),
])
@include('admin.long-term-rental-specs.scripts.car-script')
@include('admin.long-term-rental-specs.scripts.accessory-script')
@include('admin.long-term-rental-specs.scripts.bom-car-script')
@if (strcmp($d->spec_status, SpecStatusEnum::PENDING_CHECK) === 0)
    @include('admin.long-term-rental-specs.scripts.input-tag')
@endif


@include('admin.components.select2-ajax', [
    'id' => 'car_class_field',
    'modal' => '#modal-car-accessory',
    'url' => route('admin.util.select2.car-class'),
])

@include('admin.components.select2-ajax', [
    'id' => 'car_color_field',
    'modal' => '#modal-car-accessory',
    'url' => route('admin.util.select2.car-colors'),
])

@include('admin.components.select2-ajax', [
    'id' => 'accessory_field',
    'modal' => '#modal-car-accessory',
    'url' => route('admin.util.select2.accessories-type-accessory'),
])

@include('admin.components.select2-ajax', [
    'id' => 'bom_id',
    'modal' => '#modal-bom-car',
    'url' => route('admin.util.select2-rental.lt-rental-by-bom'),
])

@include('admin.components.select2-ajax', [
    'id' => 'creditor_id_field',
    'modal' => '#modal-dealer',
    'url' => route('admin.util.select2.dealers'),
])

@push('scripts')
    <script>
        $('#worksheet_no').prop('disabled', true);
        $('#job_type').prop('disabled', true);
        $('#rental_duration').prop('disabled', true);
        $('#remark').prop('disabled', true);
        $('#customer_type').prop('disabled', true);
        $('#customer_id').prop('disabled', true);
        $('#customer_name').prop('disabled', true);
        $('#offer_date').prop('disabled', true);

        var view_only = '{{ isset($view_only) ? true : false }}';
        if (view_only) {
            $('input[name="tor_line_check_input[]"]').prop('disabled', true);
            $("input[type=radio]").attr('disabled', true);
            $("input[type=checkbox]").attr('disabled', true);
            $("#reason_delivery").prop('disabled', true);
        }

        // $('#list').remove();
        $('.toggle-table').parent().next('tr').toggle();
        $('.toggle-table').click(function() {
            $(this).parent().next('tr').toggle();
            $(this).children().toggleClass('fa fa-angle-down text-muted').toggleClass(
                'fa fa-angle-right text-muted');
        });

        $(".btn-save-review").on("click", function() {
            let storeUri = "{{ route('admin.long-term-rental.specs.store') }}";
            var formData = new FormData(document.querySelector('#save-form'));
            var spec_status = $(this).attr('data-status');
            formData.append('spec_status', spec_status);
            saveForm(storeUri, formData);
        });

        function addBom() {
            bomCarVue.removeAll();
            $("#bom_id").val('').change();
            $("#modal-bom-car").modal("show");
        }

        function addDealer() {
            $("#modal-dealer").modal("show");
        }

        function saveDealer() {
            var lt_rental_id = document.getElementById('id').value;
            var dealer_id = document.getElementById('creditor_id_field').value;
            var data = {
                dealer_id: dealer_id,
                lt_rental_id: lt_rental_id,
            };
            var updateUri = "{{ route('admin.long-term-rental.specs.store-dealer') }}";
            axios.post(updateUri, data).then(response => {
                if (response.data.success) {
                    $("#modal-dealer").modal("hide");
                    location.reload();
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
            }).catch(error => {
                warningAlert('{{ __('lang.required_field_inform') }}');
            });

        }

        function clearModalData() {
            $('#dealer_id').val('');
            var $tags = document.querySelector('.js-tags');
            $tags.innerHTML = '<input placeholder="ระบุข้อมูล..." class="js-tag-input">';
        }


        $(".btn-share-modal").click(function() {
            var tags = $(this).data('email');
            var dealer_id = $(this).data('dealer');
            $('#dealer_id').val(dealer_id);
            tags = [tags];
            var $tags = document.querySelector('.js-tags');
            if (tags.length > 0 && tags[0] != '') {
                render(tags, $tags);
            }
            $("#modal-share-dealer").modal("show");
        });

        // Close modal event listener
        $('#modal-share-dealer').on('hidden.bs.modal', function() {
            clearModalData();
        });

        function sendMail() {
            var id = document.getElementById("id").value;
            var dealer_id = document.getElementById("dealer_id").value;
            var $tags = [];
            var $tags = document.querySelector('.js-tags');

            showLoading();
            axios.get("{{ route('admin.long-term-rental.specs.send-email') }}", {
                params: {
                    id: id,
                    dealer_id: dealer_id,
                    tags: tags
                }
            }).then(response => {
                hideLoading();
                $("#modal-share-dealer").modal("hide");
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

        $('.copy_link').click(function(e) {
            e.preventDefault();
            var id = document.getElementById("id").value;
            var dealer_id = document.getElementById("dealer_id").value;
            var copyText = window.location.host + '/long-term-rental-vendor/specs/edit/' + id + '/' + dealer_id;
            console.log(copyText);
            document.addEventListener('copy', function(e) {
                e.clipboardData.setData('text/plain', copyText);
                e.preventDefault();
            }, true);

            document.execCommand('copy');
            copyAlert('คัดลอกแล้ว');
        });
    </script>
@endpush
