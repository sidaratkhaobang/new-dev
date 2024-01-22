@extends('admin.layouts.layout')
@section('page_title', __('lang.manage') . __('promotions.page_title'))

@section('content')
    <x-blocks.block-search>
        <form action="" method="GET" id="form-search">
            <div class="form-group row push mb-4">
                <div class="col-sm-3">
                    <label for="s" class="text-start col-form-label">{{ __('lang.search_label') }}</label>
                    <input type="text" id="s" name="s" class="form-control"
                        placeholder="{{ __('lang.search_placeholder') }}" value="{{ $s }}">
                </div>
                <div class="col-sm-3">
                    <x-forms.select-option id="name" :value="$name" :list="null" :optionals="[
                        'placeholder' => __('lang.search_placeholder'),
                        'ajax' => true,
                        'default_option_label' => $default_name,
                    ]"
                        :label="__('promotions.name')" />
                </div>
                <div class="col-sm-3">
                    <x-forms.select-option :value="$branch_id" id="branch_id" :list="$branch_list" :label="__('products.branch')" />
                </div>
                <div class="col-sm-3">
                    <x-forms.select-option :value="$type_id" id="type_id" :list="$type" :label="__('promotions.promotion_type')" />
                </div>
            </div>
            <div class="form-group row mb-4">
                <div class="col-sm-3">
                    <x-forms.date-input id="start_date" name="start_date" :value="$start_date" :label="__('promotions.start_date')"
                        :optionals="['placeholder' => __('lang.select_date')]" />
                </div>
                <div class="col-sm-3">
                    <x-forms.date-input id="end_date" name="end_date" :value="$end_date" :label="__('promotions.end_date')"
                        :optionals="['placeholder' => __('lang.select_date')]" />
                </div>
            </div>
            @include('admin.components.btns.search')
        </form>
    </x-blocks.block-search>
    <x-blocks.block-table>
        <x-slot name="options">
            @can(Actions::Manage . '_' . Resources::Promotion)
                <x-btns.add-new btn-text="{{ __('promotions.add_new') }}"
                    route-create="{{ route('admin.promotions.select-type') }}" />
            @endcan
        </x-slot>
        <x-tables.table :list="$list">
            <x-slot name="thead">
                <th>@sortablelink('code', __('promotions.code'))</th>
                <th>@sortablelink('name', __('promotions.name'))</th>
                <th>@sortablelink('branch', __('promotions.branch'))</th>
                <th>@sortablelink('start_date', __('promotions.start_date'))</th>
                <th>@sortablelink('end_date', __('promotions.end_date'))</th>
                <th>@sortablelink('promotion_type', __('promotions.promotion_type'))</th>
                <th class="text-center">@sortablelink('status', __('promotions.status'))</th>
            </x-slot>
            @foreach ($list as $index => $d)
                <tr>
                    <td>{{ $list->firstItem() + $index }}</td>
                    <td>{{ $d->code }}</td>
                    <td>{{ $d->name }}</td>
                    <td>{{ $d->branch_name }}</td>
                    <td>{{ custom_date_format($d->start_date) }}</td>
                    <td>{{ custom_date_format($d->end_date) }}</td>
                    <td>{{ __('promotions.promotion_type_' . $d->promotion_type) }}</td>
                    <td class="text-center">{!! badge_render(__('promotions.status_class_' . $d->status), __('promotions.status_' . $d->status), null) !!} </td>
                    <td class="sticky-col text-center">
                        @php
                            $dropdowns = [
                                'view_route' => route('admin.promotions.show', ['promotion' => $d]),
                                'edit_route' => route('admin.promotions.edit', ['promotion' => $d]),
                                'delete_route' => route('admin.promotions.destroy', ['promotion' => $d]),
                                'view_permission' => Actions::View . '_' . Resources::Promotion,
                                'manage_permission' => Actions::Manage . '_' . Resources::Promotion,
                            ];
                            if (strcmp($d->promotion_type, PromotionTypeEnum::PROMOTION) != 0) {
                                $dropdowns['other_route'] = route('admin.promotion-codes.index', ['promotion_id' => $d->id]);
                                $dropdowns['other_icon'] = 'fa-note-sticky';
                                $dropdowns['other_text'] = __('promotions.coupon');
                            }
                        @endphp
                        @include('admin.components.dropdown-action', $dropdowns)
                    </td>
                </tr>
            @endforeach
        </x-tables.table>
    </x-blocks.block-table>
@endsection

@include('admin.components.sweetalert')
@include('admin.components.list-delete')
@include('admin.components.select2-default')
@include('admin.components.date-input-script')

@include('admin.components.select2-ajax', [
    'id' => 'name',
    'url' => route('admin.util.select2.promotion'),
])
