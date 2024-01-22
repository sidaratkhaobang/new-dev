@extends('admin.layouts.layout')

@section('page_title', $page_title . ' ' . $d->worksheet_no)
@section('page_title_sub')
    @if (isset($d->spec_status))
        {!! badge_render(
            __('long_term_rentals.spec_status_class_' . $d->spec_status),
            __('long_term_rentals.spec_status_' . $d->spec_status),
            null,
        ) !!}
    @endif
@endsection
@section('history')
    @include('admin.components.btns.history')
@endsection

@push('custom_styles')
    <style>
        .seperator {
            font-size: xxx-large;
            color: #CBD4E1;
            vertical-align: sub;
            font-weight: 100;
        }

        .flex-container {
            padding: 0;
            margin: 0;
            list-style: none;
            display: flex;
        }

        .space-between {
            justify-content: space-evenly;
        }

        .flex-item {
            padding: 5px;
            width: 100%;
            margin: 5px;
            color: #4D4D4D;
            font-size: 16px;
            text-align: center;
        }

        .text-p {
            color: #000000;
            font-weight: 700;
        }

        .badge-customs {
            padding: 5px 7px 5px 7px;
            border-radius: 100px;
            font-size: 14px;
            line-height: 20px;
            min-width: 0px;
        }

        .badge-bg-primary-custom {
            background: #dbe6fd;
            color: #4D82F3;
        }

        .block .block-content .btn {
            min-width: 0px;
        }
    </style>
@endpush

@section('block_options_btn')
    <div class="block-options-item">
        <a class="btn btn-sm btn-primary" href="{{ route('admin.long-term-rentals.show', ['long_term_rental' => $d->id]) }}">
            <i class="fa fa-link me-1"></i>&nbsp;
            เลขที่ใบเช่า {{ $d->worksheet_no }} </a>
    </div>
@endsection
@section('block_options_tor')
    @if (!empty($tor_files))
        <div class="block-options-item">
            <a class="btn btn-sm btn-primary" href="{{ $tor_files[0]['url'] }}" target="_blank">
                <i class="fa fa-link me-1"></i>&nbsp;{{ $tor_files[0]['name'] }}</a>
        </div>
    @endif
@endsection
@section('block_options_car')
    @if (!isset($view_only))
        <button type="button" class="btn btn-primary" onclick="addBomCar()"><i
                class="fa fa-plus-circle me-1"></i>{{ __('long_term_rentals.bom') }}</button>
        <button type="button" class="btn btn-primary" onclick="addManualCar()"><i
                class="fa fa-plus-circle me-1"></i>{{ __('long_term_rentals.add_manually') }}</button>
    @endif
@endsection

@section('content')
    @include('admin.components.creator')
    <form id="save-form">
        @include('admin.long-term-rental-specs.sections.rental-new')

        @include('admin.long-term-rental-specs.sections.tor-document')

        <div class="block {{ __('block.styles') }}">
            @include('admin.components.block-header', [
                'text' => __('long_term_rentals.car_accessory_detail'),
                'block_icon_class' => 'icon-document',
                'block_option_id' => '_car',
            ])
            <div class="block-content">
                @include('admin.long-term-rental-specs.sections.tor-line')
            </div>
        </div>

        @include('admin.long-term-rental-specs.modals.tor-line-modal')

        <div class="block {{ __('block.styles') }}">
            <div class="block-content">
                <x-forms.hidden id="id" :value="$d->id" />
                <x-forms.hidden id="status" :value="$d->status" />
                <div class="row">
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
            </div>
        </div>
    </form>
@endsection

@include('admin.components.select2-default')
@include('admin.components.date-input-script')
@include('admin.components.sweetalert')
@include('admin.components.form-save', [
    'store_uri' => route('admin.long-term-rental.specs.store'),
])
@include('admin.long-term-rental-specs.scripts.tor-line-script')

@include('admin.components.select2-ajax', [
    'id' => 'bom_id',
    'modal' => '#modal-tor-line',
    'url' => route('admin.util.select2-rental.lt-rental-by-bom'),
])

@push('scripts')
    <script>
        $(".btn-save-review").on("click", function() {
            let storeUri = "{{ route('admin.long-term-rental.specs.store') }}";
            var formData = new FormData(document.querySelector('#save-form'));
            var spec_status = $(this).attr('data-status');
            formData.append('spec_status', spec_status);
            saveForm(storeUri, formData);
        });

        function deleteCarTor(tor_id) {
            mySwal.fire({
                title: "{{ __('lang.delete_data') }}",
                text: "{{ __('lang.delete_message_confirm') }}",
                icon: 'warning',
                showCancelButton: true,
                customClass: {
                    confirmButton: 'btn btn-danger m-1',
                    cancelButton: 'btn btn-secondary m-1'
                },
                confirmButtonText: "{{ __('lang.ok') }}",
                cancelButtonText: "{{ __('lang.cancel') }}",
                html: false,
                preConfirm: e => {
                    return new Promise(resolve => {
                        setTimeout(() => {
                            resolve();
                        }, 50);
                    });
                }
            }).then(result => {
                if (result.value) {
                    var lt_rental_id = document.getElementById('id').value;
                    route_delete = "{{ route('admin.long-term-rental.specs.delete-tor') }}";
                    var data = {
                        lt_rental_id: lt_rental_id,
                        lt_rental_tor_id: tor_id,
                    }

                    axios.post(route_delete, data).then(response => {
                        if (response.data.success) {
                            mySwal.fire({
                                title: "{{ __('lang.store_success_title') }}",
                                text: "{{ __('lang.store_success_message') }}",
                                icon: 'success',
                                confirmButtonText: "{{ __('lang.ok') }}"
                            }).then(value => {
                                window.location.reload();
                            })
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
            }).catch(error => {
                mySwal.fire({
                    title: "{{ __('lang.delete_fail') }}",
                    text: error.response.data.message,
                    icon: 'error',
                    confirmButtonText: "{{ __('lang.ok') }}",
                }).then(value => {
                    if (value) {
                        //
                    }
                });
            });
        }
    </script>
@endpush
