@extends('admin.layouts.layout')
@section('page_title', __('change_registrations.page_title'))
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
                    <div class="form-group row mb-4">
                        <div class="col-sm-3">
                            <x-forms.select-option id="request_id" :value="$request_id" :list="$request_list" :label="__('change_registrations.request_type')" />
                        </div>
                        <div class="col-sm-6">
                            <x-forms.select-option id="car_id" :value="$car" :list="null" :label="__('change_registrations.license_plate_engine_chassis')"
                                :optionals="[
                                    'select_class' => 'js-select2-custom',
                                    'ajax' => true,
                                    'default_option_label' => $car_text,
                                ]" />
                        </div>
                        <div class="col-sm-3">
                            <x-forms.select-option id="leasing" :value="$leasing" :list="null" :label="__('change_registrations.leasing')"
                                :optionals="[
                                    'select_class' => 'js-select2-custom',
                                    'ajax' => true,
                                    'default_option_label' => $leasing_text,
                                ]" />
                        </div>
                    </div>
                    <div class="form-group rowmb-4">
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
        <div class="block-options">
            @can(Actions::Manage . '_' . Resources::ChangeRegistration)
                <div class="block-options-item">
                    <button type="button" class="btn btn-primary" onclick="openModalAvanceSelectCar()"><i
                            class="icon-menu-money"></i>
                        {{ __('ownership_transfers.withdraw_avance') }}</button>
                </div>
                <div class="block-options-item">
                    <button type="button" class="btn btn-primary" onclick="openModalFaceSheetSelectCar()"><i
                            class="icon-printer"></i>
                        {{ __('registers.face_sheet') }}</button>
                </div>
                <div class="btn-group" style="position: sticky; min-width:270px;">
                    <button type="button" class="btn btn-primary dropdown-toggle-split" aria-expanded="false"
                        style="width: 220px;"><i
                            class="icon-info-circle"></i>{{ __('ownership_transfers.request_document') }}</button>
                    <button type="button" class="btn btn-primary dropdown-toggle dropdown-toggle-split arrow"
                        style="min-width: 5px !important;" data-bs-toggle="dropdown" aria-expanded="false">
                        <span class="visually-hidden">Toggle Dropdown</span>
                    </button>

                    <ul class="dropdown-menu">
                        <li>
                            <button class="dropdown-item btn-request" onclick="openModalTemplateSelectCar()">
                                ดาวน์โหลด
                                Templete
                            </button>
                        </li>
                        <li>
                            <label for="upload" class="dropdown-item btn-request file"
                                style="cursor: pointer;">อัปโหลดไฟล์</label>
                            <input id="upload" type="file" name="file[]"
                                style="position: absolute; top: -9999px; left: -9999px; overflow: hidden;">
                        </li>
                    </ul>
                </div>

                <div class="btn-group" style="position: sticky;left: 100%; min-width:270px;">
                    <button type="button" class="btn btn-primary dropdown-toggle-split" aria-expanded="false"
                        style="width: 220px;"><i class="icon-edit"></i>{{ __('registers.edit_multiple') }}
                    </button>
                    <button type="button" class="btn btn-primary dropdown-toggle dropdown-toggle-split arrow"
                        style="min-width: 5px !important;" data-bs-toggle="dropdown" aria-expanded="false">
                        <span class="visually-hidden">Toggle Dropdown</span>
                    </button>

                    <ul class="dropdown-menu">
                        <li>
                            <button class="dropdown-item btn-request" onclick="openModalTemplateSelectCar()">
                                ดาวน์โหลด
                                Templete
                            </button>
                        </li>
                        <li>
                            <label for="upload" class="dropdown-item btn-request file"
                                style="cursor: pointer;">อัปโหลดไฟล์</label>
                            <input id="upload" type="file" name="file[]"
                                style="position: absolute; top: -9999px; left: -9999px; overflow: hidden;">
                        </li>
                    </ul>
                </div>
            @endcan
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

                        <th style="width: 10%;">#</th>
                        <th style="width: 12%;">@sortablelink('worksheet_no', __('ownership_transfers.leasing'))</th>
                        <th style="width: 12%;">@sortablelink('hirePurchase.contract_no', __('ownership_transfers.contract_no'))</th>
                        <th style="width: 12%;">@sortablelink('car.license_plate', __('ownership_transfers.license_plate'))</th>
                        <th style="width: 12%;">@sortablelink('car.engine_no', __('ownership_transfers.engine_no'))</th>
                        <th style="width: 12%;">@sortablelink('request_type', __('change_registrations.request_type'))</th>
                        <th style="width: 8%;">@sortablelink('status', __('ownership_transfers.status'))</th>
                        <th></th>
                        {{-- <th style="width: 100px;" class="sticky-col">{{ __('lang.tools') }}</th> --}}
                    </tr>
                </thead>
                <tbody>
                    @if (!empty($lists->count()))
                        @foreach ($lists as $index => $d)
                            <tr>
                                <td>{{ $lists->firstItem() + $index }}</td>
                                <td></td>
                                <td>{{ $d->hirePurchase && $d->hirePurchase->contract_no ? $d->hirePurchase->contract_no : '' }}
                                </td>
                                <td>{{ $d->car && $d->car->license_plate ? $d->car->license_plate : '' }}</td>
                                <td>{{ $d->car && $d->car->engine_no ? $d->car->engine_no : '' }}</td>
                                <td>{{ $d->type ? __('change_registrations.request_type_' . $d->type . '_text') : '' }}
                                </td>

                                <td>
                                    {!! badge_render(
                                        __('change_registrations.status_' . $d->status . '_class'),
                                        __('change_registrations.status_' . $d->status . '_text'),
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
                                            @can(Actions::View . '_' . Resources::ChangeRegistration)
                                                <a class="dropdown-item"
                                                    href="{{ route('admin.change-registrations.show', ['change_registration' => $d]) }}"><i
                                                        class="fa fa-eye me-1"></i>
                                                    {{ __('change_registrations.view') }}
                                                </a>
                                                @if ($d?->status !== ChangeRegistrationStatusEnum::SUCCESS)
                                                    <a class="dropdown-item"
                                                        href="{{ route('admin.change-registrations.edit', ['change_registration' => $d]) }}"><i
                                                            class="far fa-edit me-1"></i>
                                                        {{ __('lang.edit') }}
                                                    </a>
                                                @endif
                                            @endcan

                                        </div>
                                    </div>
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
@include('admin.prepare-new-cars.modals.edit-purchase')

@endsection


@include('admin.components.select2-default')
@include('admin.components.sweetalert')
@include('admin.components.list-delete')
@include('admin.components.date-input-script')

@include('admin.components.select2-ajax', [
    'id' => 'contract_no',
    'url' => route('admin.util.select2-ownership-transfer.contract-no'),
])

@include('admin.components.select2-ajax', [
    'id' => 'car_id',
    'url' => route('admin.util.select2-change-registration.car-license-plate'),
])

@include('admin.components.select2-ajax', [
    'id' => 'leasing',
    'url' => route('admin.util.select2-ownership-transfer.leasing-list'),
])
@push('scripts')
<script></script>
@endpush
