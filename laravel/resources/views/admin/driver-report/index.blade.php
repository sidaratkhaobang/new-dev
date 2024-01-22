@extends('admin.layouts.layout')
@section('page_title', __('สรุปรายจ่ายพนักงานขับรถ'))
@push('custom_styles')
    <style>
        .block-header {
            padding: 0;
        }
    </style>
@endpush
@section('content')
    <div class="block {{ __('block.styles') }}">
        <div class="block-content">
            <div class="block-header">
                <h4><i class="fa fa-file-lines"></i> {{ __('รายการทั้งหมด') }}</h4>
            </div>
            <div class="justify-content-between mb-4">
                <form action="" method="GET" id="form-search">
                    <div class="mb-4 form-group row">
                        <div class="col-sm-3">
                            <x-forms.select-option id="driver_id" :value="$driver_id" :list="$driver_list" :label="__('ชื่อพนักงาน')" />
                        </div>
                    </div>
                    @include('admin.components.btns.search')
                </form>
            </div>
        </div>
    </div>

    <div class="block {{ __('block.styles') }}">
        <div class="block-content">
            <div class="table-wrap db-scroll">
                <table class="table table-striped table-vcenter">
                    <thead class="bg-body-dark">
                    <tr>
                        <th>@sortablelink('name', __('ชื่อพนักงาน'))</th>
                        <th>@sortablelink('income', __('รายจ่ายสุทธิ'))</th>
                        <th></th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach ($list as $item)
                        <tr>
                            <td>{{$item->name}}</td>
                            <td>{{$item->summary_wage_job}}</td>
                            <td class="sticky-col text-center">
                                @include('admin.components.dropdown-action', [
                                    'view_route' => route('admin.driver-report.show', ['driver_report' => $item]),
                                    'view_permission' => Actions::View . '_' . Resources::DriverReport,
                                ])
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
            {!! $list->appends(\Request::except('page'))->render() !!}
        </div>
    </div>
@endsection

@include('admin.components.select2-default')
@include('admin.components.date-input-script')
@include('admin.components.sweetalert')
@include('admin.components.list-delete')
