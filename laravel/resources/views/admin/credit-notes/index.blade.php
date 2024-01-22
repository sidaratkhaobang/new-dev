@extends('admin.layouts.layout')
@section('page_title', __('credit_notes.page_title'))

@section('content')
    <div class="block {{ __('block.styles') }}">
        @include('admin.components.block-header',[
            'text' =>   __('lang.search'),
            'block_icon_class' => 'icon-search',
            'is_toggle' => true
        ])
        <div class="block-content">
            <div class="justify-content-between">
                <form action="" method="GET" id="form-search">
                    <div class="form-group row mb-4">
                        <div class="col-sm-3">
                            <x-forms.select-option id="credit_note_id" :value="null" :list="null" :label="__('credit_notes.credit_note_no')"
                            :optionals="['ajax' => true, 'default_option_label' => $credit_note_no]" />
                        </div>
                        <div class="col-sm-3">
                            <x-forms.select-option id="invoice_id" :value="null" :list="null" :label="__('credit_notes.invoice_no')"
                            :optionals="['ajax' => true, 'default_option_label' => $invoice_no]" />
                        </div>
                        <div class="col-sm-3">
                            <x-forms.select-option id="customer_id" :value="null" :list="null" :label="__('credit_notes.customer_code_name')" 
                            :optionals="['ajax' => true, 'default_option_label' => $customer_name]" />
                        </div>
                        <div class="col-sm-3">
                            <x-forms.select-option id="status" :value="$status" :list="$status_list" :label="__('lang.status')" />
                        </div>
                    </div>
                    @include('admin.components.btns.search')
                </form>
            </div>
        </div>
    </div>

    <div class="block {{ __('block.styles') }}">
        @include('admin.components.block-header', [
            'text' => __('locations.total_items') ,

        ])
        <div class="block-content">
            <div class="table-wrap db-scroll">
                <table class="table table-striped table-vcenter">
                    <thead class="bg-body-dark">
                    <tr>
                        <th style="width: 1px;">#</th>
                        <th style="width: 25%;">{{ __('credit_notes.credit_note_no') }}</th>
                        <th style="width: 25%;">{{ __('credit_notes.invoice_no') }}</th>
                        <th style="width: 25%;">{{ __('credit_notes.customer_code_name') }}</th>
                        <th class="text-center" style="width: 20%;">{{ __('lang.status') }}</th>
                        <th style="width: 100px;" class="sticky-col"></th>
                    </tr>
                    </thead>
                    <tbody>
                        @if (sizeof($list) > 0)
                            @foreach ($list as $index => $d)
                                <tr>
                                    <td>{{ $list->firstItem() + $index }}</td>
                                    <td>{{ $d->credit_note }}</td>
                                    <td>{{ $d->invoice_no }}</td>
                                    <td>{{ $d->customer_name }}</td>
                                    <td class="text-center">{!! badge_render(__('credit_notes.class_' . $d->status), __('credit_notes.status_' . $d->status)) !!} </td>
                                    <td class="sticky-col text-center">
                                        <x-tables.dropdown 
                                            :view-route="route()" 
                                            :edit-route="route()" 
                                            :delete-route="route()"
                                            :view-permission="true" 
                                            :manage-permission="true">
                                        </x-tables.dropdown>
                                    </td>
                                </tr>
                            @endforeach
                        @else
                            <tr class="table-empty">
                                <td class="text-center" colspan="6">" {{ __('lang.no_list') }} "</td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
            {!! $list->appends(\Request::except('page'))->render() !!}
        </div>
    </div>
@endsection

@include('admin.components.select2-default')
@include('admin.components.sweetalert')
@include('admin.components.list-delete')
@include('admin.components.date-input-script')
