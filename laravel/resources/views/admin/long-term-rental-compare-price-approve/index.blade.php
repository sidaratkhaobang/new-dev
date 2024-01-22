@extends('admin.layouts.layout')
@section('page_title', __('long_term_rentals.compare_price_approve'))

@section('content')
    <div class="block {{ __('block.styles') }}">
        <div class="block-header">
            <h3 class="block-title">{{ __('user_departments.total_items') }}</h3>
        </div>
        <div class="block-content">
            <div class="justify-content-between mb-4">
                <form action="" method="GET" id="form-search">
                    <div class="form-group row push mb-4">
                        <div class="col-sm-3">
                            <label for="s" class="text-start col-form-label">{{ __('lang.search_label') }}</label>
                            <input type="text" id="s" name="s" class="form-control"
                                placeholder="{{ __('lang.search_placeholder') }}" value="{{ $s }}">
                        </div>
                        <div class="col-sm-3">
                            <x-forms.select-option id="worksheet_id" :value="$worksheet_id" :list="null" :optionals="[
                                'placeholder' => __('lang.search_placeholder'),
                                'ajax' => true,
                                'default_option_label' => $worksheet_name,
                            ]"
                                :label="__('short_term_rentals.rental_no')" />
                        </div>
                        <div class="col-sm-3">
                            <x-forms.select-option id="status" :value="$status" :list="$compare_price_status_list" :label="__('lang.status')" />
                        </div>
                    </div>
                    @include('admin.components.btns.search')
                </form>
            </div>
            <div class="table-wrap db-scroll">
                <table class="table table-striped table-vcenter">
                    <thead class="bg-body-dark">
                        <tr>
                            <th style="width: 1px;">#</th>
                            <th>{{ __('long_term_rentals.sheet_no') }}</th>
                            <th>{{ __('lang.status') }}</th>
                            <th style="width: 100px;" class="sticky-col"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($list as $index => $d)
                            <tr>
                                <td>{{ $list->firstItem() + $index }}</td>
                                <td>{{ $d->worksheet_no }}</td>
                                <td>
                                    {!! badge_render(
                                        __('long_term_rentals.compare_price_class_' . $d->comparison_price_status),
                                        __('long_term_rentals.compare_price_status_' . $d->comparison_price_status),
                                    ) !!}
                                </td>
                                <td class="sticky-col text-center">
                                    @include('admin.components.dropdown-action', [
                                        'view_route' => route('admin.long-term-rental.compare-price-approve.show', ['rental' => $d])
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

@include('admin.components.sweetalert')
@include('admin.components.list-delete')
@include('admin.components.select2-default')

@include('admin.components.select2-ajax', [
    'id' => 'worksheet_id',
    'url' => route('admin.util.select2-rental.lt-rental-for-compare-price-approve'),
])
