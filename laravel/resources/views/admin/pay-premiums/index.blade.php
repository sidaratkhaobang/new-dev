@extends('admin.layouts.layout')
@section('page_title', __('pay_premiums.page_title'))

@section('content')
    <div class="block {{ __('block.styles') }}">
        @include('admin.components.block-header', [
            'text' => __('lang.search'),
            'block_icon_class' => 'icon-search',
            'is_toggle' => true,
        ])
        <div class="block-content">
            <div class="justify-content-between">
                <form action="" method="GET" id="form-search">
                    <div class="form-group row push mb-4">
                        <div class="col-sm-3">
                            <x-forms.select-option id="worksheet_no" :value="null" :list="null" :label="__('pay_premiums.worksheet_no')" />
                        </div>
                        <div class="col-sm-3">
                            <x-forms.select-option id="type" :value="null" :list="null" :label="__('pay_premiums.type')" />
                        </div>
                        <div class="col-sm-3">
                            <x-forms.select-option id="insurance_company" :value="null" :list="null" :label="__('pay_premiums.insurance_company')" />
                        </div>
                        <div class="col-sm-3">
                            <x-forms.select-option id="status" :value="null" :list="null" :label="__('lang.status')" />
                        </div>
                    </div>
                    @include('admin.components.btns.search')
                </form>
            </div>

        </div>
    </div>

    <div class="block {{ __('block.styles') }}">
        @include('admin.components.block-header', [
            'text' => __('lang.total_list'),
            'block_option_id' => '_list',
        ])
        <div class="block-content">
            <div class="table-wrap db-scroll">
                <table class="table table-striped table-vcenter">
                    <thead class="bg-body-dark">
                        <tr>
                            <th>#</th>
                            <th>{{ __('pay_premiums.worksheet_no') }} </th>
                            <th>{{ __('pay_premiums.type') }} </th>
                            <th>{{ __('pay_premiums.insurance_company') }} </th>
                            <th>{{ __('pay_premiums.coverage_start_date') }} </th>
                            <th>{{ __('pay_premiums.coverage_end_date') }} </th>
                            <th class="text-center">{{ __('lang.status') }} </th>
                            <th style="width: 100px;" class="sticky-col"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @if (sizeof($list) > 0)
                            <tr>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                            </tr>
                        @else
                            <tr>
                                <td colspan="8" class="text-center">" {{ __('lang.no_list') }} "</td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
            {{-- {!! $list->appends(\Request::except('page'))->render() !!} --}}
        </div>
    </div>

@endsection

@include('admin.components.sweetalert')
@include('admin.components.list-delete')
@include('admin.components.select2-default')
@include('admin.components.date-input-script')

@push('scripts')
    <script></script>
@endpush
