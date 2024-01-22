@extends('admin.layouts.layout')
@section('page_title', __('short_term_rentals.page_title'))
@section('content')
    <x-blocks.block-search>
        <form action="" method="GET" id="form-search">
            {{--            <x-forms.hidden id="filter_index" :value="$filter_index"/>--}}
            <div class="form-group row push">
                <div class="col-sm-3">
                    <label for="s" class="text-start col-form-label">{{ __('lang.search_label') }}</label>
                    <input type="text" id="s" name="s" class="form-control"
                           placeholder="{{ __('lang.search_placeholder') }}" value="{{ $s }}">
                </div>
                <div class="col-sm-3">
                    <x-forms.select-option id="branch_id" :value="$branch_id" :list="$branch_lists"
                                           :optionals="['placeholder' => __('lang.search_placeholder')]"
                                           :label="__('short_term_rentals.branch')"/>
                </div>
                <div class="col-sm-3">
                    <x-forms.select-option id="worksheet_id" :value="$worksheet_id" :list="$worksheet_lists"
                                           :optionals="['placeholder' => __('lang.search_placeholder')]"
                                           :label="__('short_term_rentals.rental_no')"/>
                </div>
                <div class="col-sm-3">
                    <x-forms.select-option id="customer_id" :value="$customer_id" :list="$customer_lists"
                                           :label="__('short_term_rentals.customer')"/>
                </div>
            </div>
            <div class="form-group row push">
                <div class="col-sm-3">
                    <x-forms.select-option id="service_type_id" :value="$service_type_id" :list="$service_type_lists"
                                           :label="__('short_term_rentals.service_type')"/>
                </div>
                <div class="col-sm-3">
                    <x-forms.date-range :label="__('short_term_rentals.rental_date')" start-id="from_date"
                                        :start-value="$from_date" end-id="to_date" :end-value="$to_date"/>
                </div>
                <div class="col-sm-3">
                    <x-forms.select-option id="status" :value="$status_id" :list="$status_list"
                                           :label="__('short_term_rentals.status')"/>
                </div>
            </div>
            @include('admin.components.btns.search')
        </form>
    </x-blocks.block-search>
    <x-blocks.block>
        {{--        <x-filter-block :filterlist="$filterlist">--}}

        {{--        </x-filter-block>--}}
    </x-blocks.block>
    <x-blocks.block-table>
        @can(Actions::Manage . '_' . Resources::ShortTermRental)
            <x-slot name="options">
                <div class="block-options-item">
                    <a class="btn btn-light me-2" href="{{ route('admin.short-term-rentals.calendar') }}"><i
                                class="far fa-calendar-days "></i>&nbsp;
                        {{ __('short_term_rentals.view_reserve') }}
                    </a>
                    <x-btns.add-new btn-text="{{ __('short_term_rentals.add_new') }}"
                                    route-create="{{ route('admin.short-term-rental.service-types.create') }}"/>
                </div>
            </x-slot>
        @endcan

        <div class="table-wrap db-scroll">
            <table class="table table-striped table-vcenter">
                <thead class="bg-body-dark">
                <tr>
                    <th style="width: 1px;"></th>
                    <th style="width: 1px;">#</th>
                    <th style="width: 15%;">@sortablelink('worksheet_no', __('short_term_rentals.rental_no'))</th>
                    <th style="width: 15%;">@sortablelink('branch_name', __('short_term_rentals.branch'))</th>
                    <th style="width: 30%;">@sortablelink('customer_name', __('short_term_rentals.customer'))</th>
                    <th style="width: 15%;">@sortablelink('service_type_name', __('short_term_rentals.service_type'))</th>
                    <th style="width: 15%;"
                        class="text-center">@sortablelink('created_at', __('short_term_rentals.rental_date'))</th>
                    <th style="width: 10%;"
                        class="text-center">@sortablelink('status', __('short_term_rentals.status'))</th>
                    {{-- <th style="width: 13%;">@sortablelink('quotation', __('short_term_rentals.quotation'))</th> --}}
                    <th style="width: 1px;" class="sticky-col text-center"></th>
                </tr>
                </thead>
                <tbody>
                @if (sizeof($lists) > 0)
                    @foreach ($lists as $_index => $d)
                        <tr>
                            <td class="text-center toggle-table" style="width: 30px">
                                <i class="fa fa-angle-right text-muted"></i>
                            </td>
                            <td>{{ $lists->firstItem() + $_index }}</td>
                            <td>{{ $d->worksheet_no }}</td>
                            <td>{{ $d->branch_name }}</td>
                            <td>{{ $d?->customer?->name }}</td>
                            <td>{{ $d->service_type_name }}</td>
                            <td class="text-center">{{ custom_date_format($d->created_at) }}</td>
                            <td>
                                {!! badge_render(__('short_term_rentals.class_' . $d->status), __('short_term_rentals.status_' . $d->status)) !!}
                            </td>
                            {{-- <td><a href="{{ route('admin.quotations.pdf', ['reference_id' => $d->id, 'reference_type' => $model]) }}" target="_blank">{{ $d->qt_no }}</a></td> --}}

                            <td class="sticky-col text-center">
                                @if (!($d->status == \App\Enums\RentalStatusEnum::CANCEL))
                                    @include('admin.components.dropdown-action', [
                                    'view_route' => route('admin.short-term-rentals.show', [
                                    'short_term_rental' => $d,
                                    ]),
                                    'edit_route' => route('admin.short-term-rentals.edit', [
                                    'short_term_rental' => $d,
                                    ]),
                                    'delete_route' => route('admin.short-term-rentals.destroy', [
                                    'short_term_rental' => $d,
                                    ]),
                                    'view_permission' => Actions::View . '_' . Resources::ShortTermRental,
                                    'manage_permission' =>
                                    Actions::Manage . '_' . Resources::ShortTermRental,
                                    ])
                                @else
                                    @include('admin.components.dropdown-action', [
                                    'view_route' => route('admin.short-term-rentals.show', [
                                    'short_term_rental' => $d,
                                    ]),
                                    'view_permission' => Actions::View . '_' . Resources::ShortTermRental,
                                    ])
                                @endif
                            </td>
                        </tr>
                        <tr style="display: none;">
                            <td></td>
                            <td class="td-table" colspan="8">
                                <table class="table table-striped">
                                    <thead class="bg-body-dark">
                                    <th style="width: 1px;">#</th>
                                    <th style="width: 20%">ประเภทบิล</th>
                                    <th style="width: 20%" class="text-end">จำนวนเงินรวมทั้งสิ้น</th>
                                    <th style="width: 45%" class="text-center">{{ __('lang.status') }}</th>
                                    <th style="width: 100px;" class="sticky-col text-center"></th>
                                    </thead>
                                    <tbody>
                                    @if (sizeof($d->quotations) > 0)
                                        @foreach ($d->quotations as $index => $item)
                                            <tr>
                                                <td>{{ $index + 1 }}</td>
                                                <td style="width: 100px">
                                                    {{ __('short_term_rentals.qt_type_' . $item->qt_type) }}
                                                </td>
                                                <td style="width: 150px" class="text-end">
                                                    {{ number_format($item['total'], 2) }}
                                                </td>
                                                <td class="text-center">{!! badge_render(
                                        __('short_term_rentals.class_' . $item->status),
                                        __('short_term_rentals.status_' . $item->status),
                                        ) !!}
                                                </td>
                                                <td class="sticky-col text-center">
                                                    @if (!($d->status == \App\Enums\RentalStatusEnum::CANCEL))
                                                        @include(
                                                        'admin.components.dropdown-action',
                                                        [
                                                        'view_route' => route(
                                                        'admin.short-term-rental.alter.view-bill',
                                                        [
                                                        'rental_id' => $item->reference_id,
                                                        ]),
                                                        'edit_route' => route(
                                                        'admin.short-term-rental.alter.edit-bill',
                                                        [
                                                        'rental_id' => $item->reference_id,
                                                        ]),
                                                        'delete_route' => route(
                                                        'admin.short-term-rentals.destroy',
                                                        [
                                                        'short_term_rental' => $d,
                                                        ]),
                                                        'view_permission' =>
                                                        Actions::View .
                                                        '_' .
                                                        Resources::ShortTermRental,
                                                        'manage_permission' =>
                                                        Actions::Manage .
                                                        '_' .
                                                        Resources::ShortTermRental,
                                                        ]
                                                        )
                                                    @else
                                                        @include('admin.components.dropdown-action', [
                                                        'view_route' => route(
                                                        'admin.short-term-rentals.show', ['rental_id' => $d]),
                                                        'view_permission' => Actions::View . '_' . Resources::ShortTermRental,
                                                        ])
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    @else
                                        <tr>
                                            <td class="text-center" colspan="10">" {{ __('lang.no_list') }} "</td>
                                        </tr>
                                    @endif
                                    </tbody>
                                </table>
                            </td>
                        </tr>
                    @endforeach
                @else
                    <tr>
                        <td class="text-center" colspan="9">" {{ __('lang.no_list') }} "</td>
                    </tr>
                @endif
                </tbody>
            </table>
        </div>
        {!! $lists->appends(\Request::except('page'))->render() !!}
    </x-blocks.block-table>
@endsection

@include('admin.components.select2-default')
@include('admin.components.sweetalert')
@include('admin.components.list-delete')
@include('admin.components.date-input-script')
@include('admin.components.list-delete')

@push('scripts')
    <script>
        $('.toggle-table').click(function () {
            $(this).parent().next('tr').toggle();
            $(this).children().toggleClass('fa fa-angle-down text-muted').toggleClass('fa fa-angle-right text-muted');
        });
    </script>
@endpush