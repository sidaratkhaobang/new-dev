@extends('admin.layouts.layout')
@section('page_title', __('sap_interfaces.income_account'))

@push('custom_styles')
    <style>
        .input-group-text {
            padding: 0.7rem .75rem;
            background-color: transparent;
            border-radius: 0;
            color: #6c757d;
        }
    </style>
@endpush

@section('content')
    <div class="block {{ __('block.styles') }}">
        @include('admin.components.block-header', [
           'text' => __('lang.search'),
           'block_icon_class' => 'icon-search',
           'is_toggle' => true
       ])
        <div class="block-content">
            <div class="justify-content-between">
                <form action="" method="GET" id="form-search">
                    <div class="form-group row mb-4">
                        <div class="col-sm-3">
                            <x-forms.select-option id="transfer_type" :value="$transfer_type"
                                :list="$transfer_type_list" :label="__('sap_interfaces.income_transfer_type')"/>
                        </div>
                        <div class="col-sm-3">
                            <x-forms.select-option id="transfer_sub_type" :value="$transfer_sub_type"
                                :list="$transfer_sub_type_list" :label="__('sap_interfaces.transfer_sub_type')"/>
                        </div>
                        <div class="col-sm-3">
                            <x-forms.select-option id="doc_type_id" :value="$doc_type_id"
                                :list="$document_type_list" :label="__('sap_interfaces.doc_type')"/>
                        </div>
                        <div class="col-sm-3">
                            <x-forms.select-option id="status" :value="$status"
                                :list="$status_list" :label="__('lang.status')"/>
                        </div>
                    </div>
                    <div class="form-group row mb-4">
                        <div class="col-sm-6">
                            <label class="text-start col-form-label"
                                   for="from_date">{{ __('sap_interfaces.range_date') }}</label>
                            <div class="form-group">
                                <div class="input-daterange input-group">
                                    <input type="text" class="js-flatpickr form-control flatpickr-input"
                                           id="from_date" name="from_date"
                                           value="{{ $from_date }}"
                                           placeholder="{{ __('lang.select_date') }}" data-week-start="1"
                                           data-autoclose="true"
                                           data-today-highlight="true">
                                    <div class="input-group-prepend input-group-append">
                                        <span class="input-group-text font-w600">
                                            <i class="fa fa-fw fa-arrow-right"></i>
                                        </span>
                                    </div>
                                    <input type="text" class="js-flatpickr form-control flatpickr-input"
                                           id="to_date" name="to_date" value="{{ $to_date }}"
                                           placeholder="{{ __('lang.select_date') }}" data-week-start="1"
                                           data-autoclose="true" data-today-highlight="true">
                                </div>
                            </div>
                        </div>
                        {{-- <div class="col-sm-3">
                            <x-forms.date-input id="from_date" :value="$from_date"
                                :label="__('sap_interfaces.from_date')" :optionals="['placeholder' => __('lang.select_date')]"/>
                        </div>
                        <div class="col-sm-3">
                            <x-forms.date-input id="to_date" :value="$to_date" :label="__('sap_interfaces.to_date')"
                                :optionals="['placeholder' => __('lang.select_date')]"/>
                        </div> --}}
                    </div>
                    @include('admin.components.btns.search')
                </form>
            </div>

        </div>
    </div>
    @section('block_options_list')
        <div class="block-options-item">
            @can(Actions::Manage . '_' . Resources::SAPInterfaceAR)
                <button class="btn btn-primary" onclick="openModalPrint()">{{ __('sap_interfaces.download_excel') }}</button>
            @endcan
        </div>
    @endsection
    @include('admin.income-accounts.modals.excel-modal')
    <div class="block {{ __('block.styles') }}">
        @include('admin.components.block-header', [
            'text' => __('lang.total_list'),
            'block_option_id' => '_list'
        ])
        <div class="block-content">
            <div class="table-wrap db-scroll">
                <table class="table table-striped table-vcenter">
                    <thead class="bg-body-dark">
                    <tr>
                        <th>#</th>
                        <th>{{ __('sap_interfaces.income_transfer_type') }} </th>
                        <th>{{ __('sap_interfaces.transfer_sub_type') }} </th>
                        <th>{{ __('sap_interfaces.doc_type') }} </th>
                        <th>{{ __('sap_interfaces.save_date') }} </th>
                        <th class="text-center">{{ __('lang.status') }} </th>
                        <th style="width: 100px;" class="sticky-col"></th>
                    </tr>
                    </thead>
                    <tbody>
                    @if($list->isNotEmpty())
                        @foreach ($list as $index => $d)
                            <tr>
                                <td>{{ $list->firstItem() + $index }}</td>
                                <td>{{ __('sap_interfaces.transfer_type_' . $d->transfer_type) }}</td>
                                <td>{{ __('sap_interfaces.transfer_sub_type_' . $d->transfer_sub_type) }}</td>
                                <td>{{ $d->document_type }}</td>
                                <td>{{ $d->created_at ? get_thai_date_format($d->created_at, 'd/m/Y') : null }}</td>
                                <td class="text-center">
                                    {!! $d->status ? badge_render(
                                        __('sap_interfaces.class_' . $d->status),
                                        __('sap_interfaces.status_' . $d->status),
                                    ) : null !!}
                                </td>
                                <td class="sticky-col text-center">
                                    @include('admin.components.dropdown-action', [
                                        'view_route' => route('admin.income-accounts.show', ['income_account' => $d]),
                                        'view_permission' => Actions::View . '_' . Resources::SAPInterfaceAR
                                    ])
                                </td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="7" class="text-center">" {{ __('lang.no_list') }} "</td>
                        </tr>
                    @endif

                    </tbody>
                </table>
            </div>
            {!! $list->appends(\Request::except('page'))->render() !!}
        </div>
    </div>

@endsection

@include('admin.components.sweetalert')
@include('admin.components.list-delete')
@include('admin.components.select2-default')
@include('admin.components.date-input-script')

@include('admin.income-accounts.scripts.excel-script')
@push('scripts')
    <script>
        function openModalPrint() {
            // warningAlert("ยังไม่พร้อมให้บริการ");
            $('#modal-income-excel').modal('show');
        }

        $("#export_excel").click(function () {
            var from_date = document.getElementById('from_date').value;
            var to_date = document.getElementById('to_date').value;
            $.ajax({
                xhrFields: {
                    responseType: 'blob'
                },
                type: 'POST',
                url: '{{ route('admin.sap-interfaces.export') }}',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                data: {
                    from_date: from_date,
                    to_date: to_date,
                },
                success: function (result, status, xhr) {
                    var fileName = 'file.xlsx';
                    if (from_date || to_date) {
                        fileName = from_date + '-' + to_date + '.xlsx';
                    }
                    var blob = new Blob([result], {
                        type: 'text/csv;charset=utf-8'
                    });
                    var link = document.createElement('a');
                    link.href = window.URL.createObjectURL(blob);
                    link.download = fileName;

                    document.body.appendChild(link);
                    link.click();
                    document.body.removeChild(link);
                },
                error: function (result, status, xhr) {
                    mySwal.fire({
                        title: "",
                        text: "ไม่พบข้อมูล",
                        icon: 'warning',
                        confirmButtonText: "{{ __('lang.ok') }}",
                    })
                }
            });
            $('#from_date').val('');
            $('#to_date').val('');
        });
    </script>
@endpush
