@extends('admin.layouts.layout')
@section('page_title', $page_title)

@section('content')

    <div id="export-excel">
        {{--    Search Section --}}
        <div class="block {{ __('block.styles') }}">
            @include('admin.components.block-header', [
                    'text' => __('lang.search'),
                    'block_icon_class' => 'icon-search',
                    'is_toggle' => true,
                ])
            <div id="" class="block-content">
                <div class="justify-content-between mb-4">
                    <div class="form-group row push mb-4">
                        <div class="col-sm-3">
                            <x-forms.select-option id="lot_no" :value="$lot_no" :list="$lot_no_list"
                                                   :optionals="['placeholder' => __('lang.search_placeholder')]"
                                                   :label="__('finance_request.search_lot_no')"/>
                        </div>
                        <div class="col-sm-3">
                            <x-forms.select-option id="rental" :value="$rental" :list="$rental_list"
                                                   :optionals="['placeholder' => __('lang.search_placeholder')]"
                                                   :label="__('finance_request.search_rental')"/>
                        </div>
                        <div class="col-sm-3">
                            <x-forms.date-input id="date_create" :value="$date_create"
                                                :label="__('finance_request.search_date_create')"/>
                        </div>
                        <div class="col-sm-3">
                            <x-forms.select-option id="status" :value="$status" :list="$status_list"
                                                   :optionals="['placeholder' => __('lang.search_placeholder')]"
                                                   :label="__('finance_request.search_status')"/>
                        </div>
                    </div>
                    <div class="row mt-4">
                        <div class="col-sm-12 text-end">
                            <a class="btn btn-outline-secondary btn-clear-search btn-custom-size me-1"><i
                                    class="fa fa-rotate-left"></i> {{ __('lang.clear_search') }}</a>
                            <button type="button" class="btn btn-primary btn-custom-size"><i
                                    class="fa fa-magnifying-glass"></i> {{ __('lang.search') }}</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        {{--    Table Section --}}
        <div class="block {{ __('block.styles') }}">

            <div class="block-content">
                <p>จำนวนรถทั้งหมด <span>5</span> คัน</p>
                <div class="table-wrap db-scroll">
                    <table class="table table-striped table-vcenter">
                        <thead class="bg-body-dark">
                        <tr>
                            <th>
                                #
                            </th>
                            <th>
                                {{ __('finance_request.search_lot_no') }}
                            </th>
                            <th>
                                {{__('finance_request.search_rental')}}
                            </th>
                            <th>
                                {{__('finance_request.car_total')}}
                            </th>
                            <th>
                                {{__('finance_request.search_status')}}
                            </th>
                            <th>

                            </th>
                        </tr>
                        </thead>
                        <tbody>
                        @if(!$list->isEmpty())
                            <tr>
                                <td>

                                </td>
                                <td>

                                </td>
                                <td>

                                </td>
                                <td>

                                </td>
                                <td>

                                </td>
                                <td>

                                </td>
                            </tr>
                        @else
                            <tr>
                                <td class="text-center" colspan="11">" {{ __('lang.no_list') }} "</td>
                            </tr>
                        @endif
                        </tbody>
                    </table>
                </div>
                {{--            {!! $list->appends(\Request::except('page'))->render() !!}--}}

                <div class="row push mt-4">
                    <div class="text-end">
                        <button type="button" class="btn btn-outline-secondary btn-custom-size"
                                onclick="window.history.back();">{{ __('lang.back') }}</button>
                        @if(!isset($view))
                            <button type="button"
                                    @click="alertWaiting"
                                    class="btn btn-primary btn-custom-size btn-excel-download">{{ __('finance_request.btn_download_excel') }}</button>
                        @endif
                    </div>
                </div>


            </div>
        </div>
    </div>

@endsection

@include('admin.finance-request.scripts.script-export-excel')
@include('admin.components.select2-default')
@include('admin.components.sweetalert')
@include('admin.components.date-input-script')
@include('admin.components.select2-ajax', [
    'id' => 'rental',
    'url' => route('admin.util.select2-finance.creditor-leasing-list'),
])
@include('admin.components.select2-ajax', [
    'id' => 'lot_no',
    'url' => route('admin.util.select2-finance.get-lot'),
])


