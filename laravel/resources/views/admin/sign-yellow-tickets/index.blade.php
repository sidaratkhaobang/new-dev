@extends('admin.layouts.layout')
@section('page_title', __('sign_yellow_tickets.page_title'))
@push('custom_styles')
    <style>

    </style>
@endpush
@section('content')
    <div class="block {{ __('block.styles') }}">
        @include('admin.components.block-header', [
            'text' => __('lang.search'),
            'block_icon_class' => 'icon-search',
            'is_toggle' => true,
        ])

        <div class="block-content">
            <div class="justify-content-between mb-4">
                <form action="" method="GET" id="form-search">
                    <div class="form-group row push mb-4">
                        <div class="col-sm-3">
                            <x-forms.select-option id="car_class" :value="$car_class" :list="null" :label="__('change_registrations.car_class')"
                                :optionals="[
                                    'select_class' => 'js-select2-custom',
                                    'ajax' => true,
                                    'default_option_label' => $car_class_text,
                                ]" />
                        </div>
                        <div class="col-sm-3">
                            <x-forms.select-option id="responsible" :value="$responsible" :list="$responsible_list" :label="__('sign_yellow_tickets.responsible')" />
                        </div>
                        <div class="col-sm-3">
                            <x-forms.select-option id="car_id" :value="$car" :list="null" :label="__('change_registrations.license_plate_engine_chassis')"
                                :optionals="[
                                    'select_class' => 'js-select2-custom',
                                    'ajax' => true,
                                    'default_option_label' => $car_text,
                                ]" />
                        </div>

                        <div class="col-sm-3">
                            <x-forms.date-input id="created_at" :value="$created_at" :label="__('sign_yellow_tickets.save_date')" />
                        </div>
                    </div>
                    <div class="form-group row push mb-4">
                        <div class="col-sm-3">
                            <x-forms.select-option id="status" :value="$status" :list="$status_list"
                                :label="__('ownership_transfers.status')" />
                        </div>
                    </div>
                    @include('admin.components.btns.search')
                </form>
            </div>
        </div>
    </div>
    <div class="block {{ __('block.styles') }}">
    @section('block_options_list')
        <div class="block-options d-flex justify-content-end">
            <div class="btn-group ">
                <button type="button" class="btn btn-primary dropdown-toggle-split" aria-expanded="false"
                    style="width: 220px;"><i class="icon-edit"></i>{{ __('registers.edit_multiple') }}</button>
                <button type="button" class="btn btn-primary dropdown-toggle dropdown-toggle-split arrow"
                    style="min-width: 5px !important;" data-bs-toggle="dropdown" aria-expanded="false">
                    <span class="visually-hidden">Toggle Dropdown</span>
                </button>
                <ul class="dropdown-menu">
                    <li><button class="dropdown-item btn-request" onclick="openModalTemplateSelectCar()">ดาวน์โหลด
                            Templete</button>
                    </li>
                    <li>
                        <label for="upload" class="dropdown-item btn-request file"
                            style="cursor: pointer;">อัปโหลดไฟล์</label>
                        <input id="upload" type="file" name="file[]"
                            style="position: absolute; top: -9999px; left: -9999px; overflow: hidden;">
                    </li>
                </ul>
            </div>
            <div class="block-options-item">
                <x-btns.add-new btn-text="{{ __('sign_yellow_tickets.add_new') }}"
                    route-create="{{ route('admin.sign-yellow-tickets.create') }}" />
            </div>
        </div>
    @endsection
    @include('admin.components.block-header', [
        'text' => __('lang.total_list'),
        'block_option_id' => '_list',
    ])
    <div class="block-content">
        <div class="table-wrap db-scroll">
            <table class="table table-striped table-vcenter">
                <thead class="bg-body-dark">
                    <tr>
                        <th style="width: 30px" class="text-center"></th>
                        <th style="width: 10%;" class="text-center">#</th>
                        <th style="width: 12%;" class="text-center">@sortablelink('car.license_plate', __('ownership_transfers.license_plate'))</th>
                        <th style="width: 12%;" class="text-center">@sortablelink('car_class', __('sign_yellow_tickets.car_class'))</th>
                        <th style="width: 12%;" class="text-center">@sortablelink('lawsuit_total', __('sign_yellow_tickets.lawsuit_total'))</th>
                        <th style="width: 12%;" class="text-center">@sortablelink('car.save_date', __('sign_yellow_tickets.save_date'))</th>
                        <th style="width: 12%;" class="text-center">@sortablelink('total', __('sign_yellow_tickets.total'))</th>
                        <th style="width: 12%;" class="text-center">@sortablelink('total_pay_dlt', __('sign_yellow_tickets.total_pay_dlt'))</th>
                        <th style="width: 12%;" class="text-center">@sortablelink('total_no_pay', __('sign_yellow_tickets.total_no_pay'))</th>
                        <th style="width: 8%;" class="text-center">@sortablelink('status', __('sign_yellow_tickets.status'))</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @if (sizeof($lists) > 0)
                        @foreach ($lists as $index => $d)
                            <tr>
                                <td class="text-center toggle-table" style="width: 30px">
                                    <i class="fa fa-angle-right text-muted"></i>
                                </td>
                                <td class="text-center">{{ $lists->firstItem() + $index }}</td>
                                <td class="text-center">
                                    {{ $d->car && $d->car->license_plate ? $d->car->license_plate : '' }}</td>
                                <td class="text-center">
                                    {{ $d->car && $d->car->carClass ? $d->car->carClass->full_name : '' }}
                                </td>
                                <td class="text-center">{{ $d->lawsuit_count }}</td>
                                <td class="text-center">
                                    {{ $d->created_at ? get_thai_date_format($d->created_at, 'd/m/Y') : '' }}</td>
                                <td class="text-center">{{ number_format($d->total_amount, 2) }}</td>
                                <td class="text-center">{{ number_format($d->total_pay_dlt, 2) }}</td>
                                <td class="text-center">{{ number_format($d->total_amount - $d->total_pay_dlt, 2) }}
                                </td>
                                <td class="text-center">
                                    {!! badge_render(
                                        __('sign_yellow_tickets.status_' . $d->status . '_class'),
                                        __('sign_yellow_tickets.status_' . $d->status . '_text'),
                                    ) !!}
                                </td>
                                <td class="sticky-col text-center">
                                    <div class="dropdown dropleft">
                                        <button type="button" class="btn btn-sm btn-alt-secondary dropdown-toggle"
                                            id="dropdown-dropleft-dark" data-bs-toggle="dropdown" aria-haspopup="true"
                                            aria-expanded="false">
                                            <i class="fa fa-ellipsis-vertical"></i>
                                        </button>
                                        <div class="dropdown-menu" aria-labelledby="dropdown-dropleft-dark">
                                            @can(Actions::View . '_' . Resources::SignYellowTicket)
                                                <a class="dropdown-item"
                                                    href="{{ route('admin.sign-yellow-tickets.show', ['sign_yellow_ticket' => $d->id]) }}"><i
                                                        class="fa fa-eye me-1"></i>
                                                    {{ __('ownership_transfers.view') }}
                                                </a>
                                                @if (!in_array($d->status, [SignYellowTicketStatusEnum::SUCCESS]))
                                                    <a class="dropdown-item"
                                                        href="{{ route('admin.sign-yellow-tickets.edit', ['sign_yellow_ticket' => $d->id]) }}"><i
                                                            class="far fa-edit me-1"></i>
                                                        {{ __('lang.edit') }}
                                                    </a>
                                                @endif
                                            @endcan
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            <tr style="display: none;">
                                <td></td>
                                <td class="td-table" colspan="9">
                                    <table class="table table-striped">
                                        <thead class="bg-body-dark">
                                            <th class="text-center">{{ __('sign_yellow_tickets.incident_date') }}</th>
                                            <th class="text-center">{{ __('sign_yellow_tickets.lawsuit') }} /
                                                {{ __('sign_yellow_tickets.accident_place') }}</th>
                                            <th class="text-center">{{ __('sign_yellow_tickets.is_wrong') }}</th>
                                            <th class="text-center">{{ __('sign_yellow_tickets.responsible') }}</th>
                                            <th class="text-center">{{ __('sign_yellow_tickets.amount') }}</th>
                                            <th class="text-center">{{ __('sign_yellow_tickets.payment') }}</th>
                                            <th class="text-center">{{ __('sign_yellow_tickets.paid_date') }}</th>
                                        </thead>
                                        <tbody>
                                            @if (sizeof($d->lawsuits) > 0)
                                                @foreach ($d->lawsuits as $index => $item)
                                                    <tr>
                                                        <td style="width: 150px" class="text-center">
                                                            {{ $item['incident_date'] ? get_date_by_format($item['incident_date']) : '' }}
                                                        </td>
                                                        <td style="width: 150px" class="text-center">
                                                            {{ $item['case'] }}
                                                            <br> {{ $item['location'] }}
                                                        </td>
                                                        <td style="width: 150px" class="text-center">
                                                            {{ $item['is_mistake'] }}</td>
                                                        <td style="width: 150px" class="text-center">
                                                            {{ $item['institution'] }}</td>
                                                        <td style="width: 150px" class="text-center">
                                                            {{ number_format($item['amount'], 2) }}</td>
                                                        <td style="width: 150px" class="text-center">
                                                            {{ $item['is_payment_fine'] }}</td>
                                                        <td style="width: 150px" class="text-center">
                                                            {{ $item['payment_date'] ? get_date_by_format($item['payment_date']) : '' }}
                                                        </td>
                                                    </tr>
                                </td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td class="text-center" colspan="10">" {{ __('lang.no_list') }}
                                "
                            </td>
                        </tr>
                    @endif
                </tbody>
            </table>
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
{{-- @include('admin.prepare-new-cars.modals.edit-purchase') --}}

@endsection


@include('admin.components.select2-default')
@include('admin.components.sweetalert')
@include('admin.components.list-delete')
@include('admin.components.date-input-script')

@include('admin.components.select2-ajax', [
    'id' => 'car_id',
    'url' => route('admin.util.select2-sign-yellow-ticket.car-license-plate'),
])

@include('admin.components.select2-ajax', [
    'id' => 'car_class',
    'url' => route('admin.util.select2-sign-yellow-ticket.car-class'),
])
@push('scripts')
<script>
    $('.toggle-table').click(function() {
        $(this).parent().next('tr').toggle();
        $(this).children().toggleClass('fa fa-angle-down text-muted').toggleClass(
            'fa fa-angle-right text-muted');
    });
</script>
@endpush
