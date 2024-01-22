@extends('admin.layouts.layout')
@section('page_title', __('long_term_rentals.spec_equipment'))
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
          'is_toggle' => true,
      ])
        {{--        <div class="block-header">--}}
        {{--            <h3 class="block-title">{{ __('long_term_rentals.total_items') }}</h3>--}}
        {{--        </div>--}}
        <div class="block-content">
            <div class="justify-content-between">
                <form action="" method="GET" id="form-search">
                    <div class="form-group row push mb-4">
                        <div class="col-sm-3">
                            <label for="s" class="text-start col-form-label">{{ __('lang.search_label') }}</label>
                            <input type="text" id="s" name="s" class="form-control"
                                   placeholder="{{ __('lang.search_placeholder') }}" value="{{ $s }}">
                        </div>
                        <div class="col-sm-3">
                            <x-forms.select-option id="worksheet_no" :value="$worksheet_id" :list="$worksheet_list"
                                                   :label="__('long_term_rentals.worksheet_no')"/>
                        </div>
                        <div class="col-sm-3">
                            <x-forms.select-option id="customer" :value="$customer_id" :list="$customer_list"
                                                   :label="__('long_term_rentals.customer')"/>
                        </div>
                        <div class="col-sm-3">
                            <x-forms.select-option id="lt_rental_type" :value="$lt_rental_type"
                                                   :list="$lt_rental_type_list"
                                                   :label="__('long_term_rentals.job_type')"/>
                        </div>
                    </div>
                    <div class="form-group row push mb-4">
                        <div class="col-sm-3">
                            <label class="text-start col-form-label"
                                   for="from_offer_date">{{ __('long_term_rentals.offer_date') }}</label>
                            <div class="form-group">
                                <div class="input-daterange input-group" data-date-format="mm/dd/yyyy"
                                     data-week-start="1" data-autoclose="true" data-today-highlight="true">
                                    <input type="text" class="js-flatpickr form-control flatpickr-input"
                                           id="from_offer_date" name="from_offer_date" value="{{ $from_offer_date }}"
                                           placeholder="{{ __('lang.select_date') }}" data-week-start="1"
                                           data-autoclose="true" data-today-highlight="true">
                                    <div class="input-group-prepend input-group-append">
                                        <span class="input-group-text font-w600">
                                            <i class="fa fa-fw fa-arrow-right"></i>
                                        </span>
                                    </div>
                                    <input type="text" class="js-flatpickr form-control flatpickr-input"
                                           id="to_offer_date" name="to_offer_date" value="{{ $to_offer_date }}"
                                           placeholder="{{ __('lang.select_date') }}" data-week-start="1"
                                           data-autoclose="true" data-today-highlight="true">
                                </div>
                            </div>
                        </div>
                    </div>
                    @include('admin.components.btns.search')
                </form>
            </div>
        </div>
    </div>


    <div class="block {{ __('block.styles') }}">
        @include('admin.components.block-header', [
            'text' => __('transfer_cars.total_items'),
            'block_icon_class' => 'icon-document',
        ])
        <div class="block-content">
            <div class="table-wrap db-scroll">
                <table class="table table-striped table-vcenter">
                    <thead class="bg-body-dark">
                    <tr>
                        <td style="width: 1px;">#</td>
                        <th>@sortablelink('worksheet_no', __('long_term_rentals.worksheet_no'))</th>
                        <th>@sortablelink('customer_name', __('long_term_rentals.customer'))</th>
                        <th>@sortablelink('rental_type', __('long_term_rentals.job_type'))</th>
                        <th>@sortablelink('offer_date', __('long_term_rentals.offer_date'))</th>
                        <th class="text-center">@sortablelink('spec_status', __('lang.status'))</th>
                        <th style="width: 100px;" class="sticky-col"></th>
                    </tr>
                    </thead>
                    <tbody>
                    @if(!empty($lists->count()))
                        @foreach ($lists as $index => $d)
                            <tr>
                                <td>{{ $lists->firstItem() + $index }}</td>
                                <td>{{ $d->worksheet_no }}</td>
                                <td>{{ $d->customer_name }}</td>
                                <td>{{ $d->rental_type }}</td>
                                <td>{{ $d->offer_date ? get_thai_date_format($d->offer_date, 'd/m/Y') : null }}</td>
                                <td class="text-center"> {!! badge_render(
                                    __('long_term_rentals.spec_status_class_' . $d->spec_status),
                                    __('long_term_rentals.spec_status_' . $d->spec_status),
                                ) !!}
                                </td>
                                <td class="sticky-col text-center" style="width: 100px;">
                                    @if (in_array($d->spec_status, [SpecStatusEnum::PENDING_REVIEW, SpecStatusEnum::CONFIRM]))
                                        @include('admin.components.dropdown-action', [
                                            'view_route' => route('admin.long-term-rental.specs.show', [
                                                'rental' => $d,
                                                'accessory_controller' => true,
                                            ]),
                                            'view_permisision' => Actions::View . '_' . Resources::LongTermRentalSpecsAccessory
                                        ])
                                    @else
                                        @include('admin.components.dropdown-action', [
                                            'view_route' => route('admin.long-term-rental.specs.show', [
                                                'rental' => $d,
                                                'accessory_controller' => true,
                                            ]),
                                            'edit_route' => route(
                                                'admin.long-term-rental.specs.accessories.edit',
                                                [
                                                    'rental' => $d,
                                                    // 'accessory_controller' => true,
                                                ]
                                            ),
                                            'view_permission' => Actions::View . '_' . Resources::LongTermRentalSpecsAccessory,
                                            'manage_permission' => Actions::Manage . '_' . Resources::LongTermRentalSpecsAccessory,
                                        ])
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td class="text-center" colspan="11">" {{ __('lang.no_list') }} "</td>
                        </tr>
                    @endif
                    </tbody>
                </table>
            </div>
            {!! $lists->appends(\Request::except('page'))->render() !!}
        </div>
    </div>

@endsection

@include('admin.components.select2-default')
@include('admin.components.sweetalert')
@include('admin.components.list-delete')
@include('admin.components.date-input-script')
