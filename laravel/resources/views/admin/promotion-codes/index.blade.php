@extends('admin.layouts.layout')
@section('page_title', __('lang.manage') . ' ' . $page_title)

@section('content')
    <x-blocks.block-table>
        <x-slot name="options">
            @can(Actions::Manage . '_' . Resources::Promotion)
                <x-btns.add-new btn-text="{{ __('lang.add_new') }}"
                    route-create="{{ route('admin.promotion-codes.create', ['promotion_id' => $promotion->id]) }}" />
            @endcan
        </x-slot>
        <x-tables.table :list="$list">
            <x-slot name="thead">
                @if (in_array($promotion->promotion_type, [PromotionTypeEnum::COUPON, PromotionTypeEnum::PARTNER]))
                    <th>{{ __('promotions.coupon_code') }}</th>
                    <th class="text-center">{{ __('promotions.can_reuse') }}</th>
                    <th class="text-center">{{ __('promotions.start_date') }}</th>
                    <th class="text-center">{{ __('promotions.end_date') }}</th>
                    <th class="text-center">{{ __('promotions.is_used') }}</th>
                    <th class="text-center">{{ __('promotions.use_date') }}</th>
                @elseif (in_array($promotion->promotion_type, [PromotionTypeEnum::VOUCHER]))
                    <th>{{ __('promotions.code_voucher') }}</th>
                    <th class="text-end">{{ __('promotions.selling_price') }}</th>
                    <th class="text-center">{{ __('promotions.start_sale_date') }}</th>
                    <th class="text-center">{{ __('promotions.end_sale_date') }}</th>
                    <th class="text-center">{{ __('promotions.is_sold') }}</th>
                    <th class="text-center">{{ __('promotions.sold_date') }}</th>
                    <th class="text-center">{{ __('promotions.is_used') }}</th>
                    <th class="text-center">{{ __('promotions.use_date') }}</th>
                    <th style="width: 100px;" class="sticky-col"></th>
                @endif
            </x-slot>
            @foreach ($list as $index => $d)
                <tr>
                    <td style="width: 1px;">{{ $list->firstItem() + $index }}</td>
                    <td style="width: 200px;">{{ $d->code }}</td>
                    @if (in_array($promotion->promotion_type, [PromotionTypeEnum::VOUCHER]))
                        <td style="width: 150px;" class="text-end">
                            {{ number_format(floatval($d->selling_price), 2) }}</td>
                    @endif
                    @if (in_array($promotion->promotion_type, [PromotionTypeEnum::COUPON, PromotionTypeEnum::PARTNER]))
                        <td class="text-center">
                            <span
                                class="badge rounded-pill {{ __('promotions.badge_class_' . $d->can_reuse) }}">{{ __('promotions.can_reuse_' . $d->can_reuse) }}</span>
                        </td>
                    @endif
                    <td class="text-center">
                        {{ $d->start_sale_date ? get_thai_date_format($d->start_sale_date, 'd/m/Y') : '-' }}
                    </td>
                    <td class="text-center">
                        {{ $d->end_sale_date ? get_thai_date_format($d->end_sale_date, 'd/m/Y') : '-' }}
                    </td>
                    @if (in_array($promotion->promotion_type, [PromotionTypeEnum::VOUCHER]))
                        <td class="text-center">
                            <span
                                class="badge rounded-pill {{ __('promotions.badge_class_' . $d->is_sold) }}">{{ __('promotions.is_sold_' . $d->is_sold) }}</span>
                        </td>
                        <td class="text-center">
                            {{ $d->sold_date ? get_thai_date_format($d->sold_date, 'd/m/Y') : '-' }}
                        </td>
                    @endif
                    <td class="text-center">
                        <span
                            class="badge rounded-pill {{ __('promotions.badge_class_' . $d->is_used) }}">{{ __('promotions.is_used_' . $d->is_used) }}</span>
                    </td>
                    <td class="text-center">
                        {{ $d->use_date ? get_thai_date_format($d->use_date, 'd/m/Y') : '-' }}
                    </td>
                    @if (in_array($promotion->promotion_type, [PromotionTypeEnum::VOUCHER]))
                        <td>
                            @if (strcmp($d->is_sold, BOOL_TRUE) == 0)
                                @include('admin.components.dropdown-action', [
                                    'view_route' => route('admin.promotion-codes.edit', [
                                        'promotion_code' => $d,
                                    ]),
                                    'view_permission' => Actions::View . '_' . Resources::Promotion,
                                ])
                            @else
                                @include('admin.components.dropdown-action', [
                                    'edit_route' => route('admin.promotion-codes.edit', [
                                        'promotion_code' => $d,
                                    ]),
                                    'manage_permission' => Actions::Manage . '_' . Resources::Promotion,
                                ])
                            @endif
                        </td>
                        <td></td>
                    @else
                        <td></td>
                    @endif
                </tr>
            @endforeach
        </x-tables.table>
        <div class="row mt-3">
            <div class="col-sm-12 text-end">
                <a class="btn btn-outline-secondary"
                    href="{{ route('admin.promotions.index') }}">{{ __('lang.back') }}</a>
            </div>
        </div>
    </x-blocks.block-table>
@endsection

@include('admin.components.sweetalert')
@include('admin.components.list-delete')

@push('scripts')
    <script>
        $('#promotion_name').prop('disabled', true);
        $('#promotion_code').prop('disabled', true);

        $('.btn-revoke-used').on('click', function() {
            axios.delete(route_delete).then(response => {
                if (response.data.success) {
                    mySwal.fire({
                        title: "{{ __('lang.delete_success') }}",
                        text: "{{ __('lang.deleted_message') }}",
                        icon: 'success',
                        confirmButtonText: "{{ __('lang.ok') }}"
                    }).then(value => {
                        if (response.data.redirect) {
                            window.location.href = response.data.redirect;
                        } else {
                            window.location.reload();
                        }
                    });
                } else {
                    mySwal.fire({
                        title: "{{ __('lang.delete_fail') }}",
                        text: response.data.message,
                        icon: 'error',
                        confirmButtonText: "{{ __('lang.ok') }}",
                    }).then(value => {
                        if (value) {
                            //
                        }
                    });
                }
            });
        });
    </script>
@endpush
