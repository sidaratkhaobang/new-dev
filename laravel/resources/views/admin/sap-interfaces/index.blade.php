@extends('admin.layouts.layout')
@section('page_title', __('lang.manage') . __('sap_interfaces.page_title'))

@push('custom_styles')
    <style>
        .mt-30 {
            margin-top: 35px;
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
            <div class="justify-content-between mb-4">
                <form action="" method="GET" id="form-search">
                    <div class="form-group row push mb-4">
                        <div class="col-sm-3">
                            <x-forms.select-option id="account_type" :value="$account_type_id" :list="$account_list"
                                                   :label="__('sap_interfaces.account_type')"/>
                        </div>
                        <div class="col-sm-3">
                            <x-forms.select-option id="transfer_type" :value="$transfer_type_id"
                                                   :list="$transfer_type_list"
                                                   :label="__('sap_interfaces.transfer_type')"/>
                        </div>
                        <div class="col-sm-3">
                            <x-forms.select-option id="transfer_sub_type" :value="$transfer_sub_type_id"
                                                   :list="$transfer_sub_type_list"
                                                   :label="__('sap_interfaces.transfer_sub_type')"/>
                        </div>
                        {{--                        <div class="col-sm-3 text-end mt-4">--}}
                        {{--                            <a href="{{ route('admin.sap-interfaces.index') }}"--}}
                        {{--                               class="btn btn-secondary btn-clear-search">{{ __('lang.clear_search') }}</a>--}}
                        {{--                            <button type="submit" class="btn btn-primary">{{ __('lang.search') }}</button>--}}
                        {{--                        </div>--}}
                    </div>
                    <div class="row mt-4">
                        <div class="col-sm-12 text-end">
                            <a href="{{ route('admin.sap-interfaces.index') }}"
                               class="btn btn-outline-secondary btn-clear-search btn-custom-size me-1"><i
                                    class="fa fa-rotate-left"></i> {{ __('lang.clear_search') }}</a>
                            <button type="submit" class="btn btn-primary btn-custom-size"><i
                                    class="fa fa-magnifying-glass"></i> {{ __('lang.search') }}</button>
                        </div>
                    </div>

                </form>

            </div>

        </div>
    </div>
    <div class="block {{ __('block.styles') }}">
        @include('admin.components.block-header', [
     'text' => __('sap_interfaces.export_excel'),
     'block_icon_class' => 'icon-export'
 ])
        <div class="block-content">
{{--            <div class="row mt-3">--}}
{{--                <div class="col-sm-6">--}}
{{--                    <h5>{{ __('sap_interfaces.export_excel') }}</h5>--}}
{{--                </div>--}}
{{--            </div>--}}
            <div class="form-group row mb-4">
                <div class="col-sm-3">
                    <x-forms.date-input id="from_date" name="from_date" :value="null"
                                        :label="__('sap_interfaces.from_date')"
                                        :optionals="['placeholder' => __('lang.select_date')]"/>
                </div>
                <div class="col-sm-3">
                    <x-forms.date-input id="to_date" name="to_date" :value="null"
                                        :label="__('sap_interfaces.to_date')"
                                        :optionals="['placeholder' => __('lang.select_date')]"/>
                </div>
                <div class="col-sm-6 d-flex justify-content-end align-items-end">
                    @can(Actions::View . '_' . Resources::SapInterface)
                        <div>
                            <button class="btn btn-primary mt-30"
                                    id="export_excel">{{ __('sap_interfaces.download_excel') }}</button>
                        </div>
                    @endcan
                </div>
            </div>
        </div>
    </div>
    <div class="block {{ __('block.styles') }}">
        @include('admin.components.block-header', [
      'text' => __('lang.total_list'),
  ])
        <div class="block-content">
            <div class="table-wrap db-scroll">
                <table class="table table-striped table-vcenter">
                    <thead class="bg-body-dark">
                    <tr>
                        <th>#</th>
                        <th>{{ __('sap_interfaces.account_type') }} </th>
                        <th>{{ __('sap_interfaces.transfer_type') }} </th>
                        <th>{{ __('sap_interfaces.transfer_sub_type') }} </th>
                        <th>{{ __('sap_interfaces.save_date') }} </th>
                        <th style="width: 100px;" class="sticky-col"></th>
                    </tr>
                    </thead>
                    <tbody>
                    @if(!$list->isEmpty())
                        @foreach ($list as $index => $d)
                            <tr>
                                <td>{{ $list->firstItem() + $index }}</td>
                                <td>{{ $d->account_type }}</td>
                                <td>{{ __('sap_interfaces.transfer_type_' . $d->transfer_type) }}</td>
                                <td>{{ __('sap_interfaces.transfer_sub_type_' . $d->transfer_sub_type) }}</td>
                                <td>{{ get_thai_date_format($d->created_at, 'd-m-Y') }}</td>
                                <td class="sticky-col text-center">
                                    @include('admin.components.dropdown-action', [
                                        'view_route' => route('admin.sap-interfaces.show', [
                                            'sap_interface' => $d,
                                        ]),
                                        'view_permission' => Actions::View . '_' . Resources::SapInterface
                                    ])
                                </td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="11" class="text-center">" {{ __('lang.no_list') }} "</td>
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


@push('scripts')
    <script>
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
