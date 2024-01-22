@extends('admin.layouts.layout')
@section('page_title', __('product_prices.page_title'))

@section('content')
    <div class="block {{ __('block.styles') }}">
        <div class="block-header">
            <h3 class="block-title">{{ __('lang.total_list') }}</h3>
            <div class="block-options">
                <div class="block-options-item">
                    @can(Actions::Manage . '_' . Resources::Product)
                    <x-btns.add-new btn-text="{{ __('product_prices.add_new') }}"
                        route-create="{{ route('admin.product-prices.create', ['product_id' => $product_id]) }}" />
                    @endcan
                </div>
            </div>
        </div>
        <div class="block-content">
            <div class="justify-content-between mb-4">
                <div class="row push mb-4">
                    <div class="col-sm-6">
                        <x-forms.input-new-line id="product_name" :value="$product->name" :label="__('products.name')" />
                    </div>
                    <div class="col-sm-6">
                        <x-forms.input-new-line id="product_sku" :value="$product->sku" :label="__('products.sku')" />
                    </div>
                </div>
            </div>
            <div class="table-wrap db-scroll">
                <table class="table table-striped table-vcenter">
                    <thead class="bg-body-dark">
                        <tr>
                            <th>#</th>
                            <th>@sortablelink('name', __('product_prices.name'))</th>
                            <th>@sortablelink('price', __('product_prices.price'))</th>
                            <th>@sortablelink('status', __('lang.status'))</th>
                            <th style="width: 100px;" class="sticky-col"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($list as $index => $d)
                            <tr>
                                <td>{{ $list->firstItem() + $index }}</td>
                                <td>{{ $d->name }}</td>
                                <td>{{ number_format($d->price,2) }}</td>
                                <td>
                                    {!! badge_render(__('lang.status_class_' . $d->status), __('lang.status_' . $d->status)) !!}
                                </td>
                                <td class="sticky-col text-center">
                                    <div class="btn-group">
                                        <div class="col-sm-12">
                                            <div class="dropdown dropleft">
                                                <button type="button" class="btn btn-sm btn-alt-secondary dropdown-toggle"
                                                    id="dropdown-dropleft-dark" data-bs-toggle="dropdown"
                                                    aria-haspopup="true" aria-expanded="false">
                                                    <i class="fa fa-ellipsis-vertical"></i>
                                                </button>
                                                <div class="dropdown-menu" aria-labelledby="dropdown-dropleft-dark">
                                                    @can(Actions::View . '_' . Resources::Product)
                                                    <a class="dropdown-item"
                                                        href="{{ route('admin.product-prices.show', ['product_price' => $d]) }}">
                                                        <i class="fa fa-eye me-1"></i> ดูข้อมูล</a>
                                                    @endcan
                                                    @can(Actions::Manage . '_' . Resources::Product)
                                                        <a class="dropdown-item"
                                                            href="{{ route('admin.product-prices.edit', ['product_price' => $d, 'product_id' => $product_id]) }}"><i
                                                                class="far fa-edit me-1"></i> แก้ไข</a>
                                                        <a class="dropdown-item btn-delete-row" href="javascript:void(0)"
                                                            data-route-delete="{{ route('admin.product-prices.destroy', ['product_price' => $d]) }}"><i
                                                                class="fa fa-trash-alt me-1"></i> ลบ</a>
                                                    @endcan
                                                </div>
                                            </div>
                                        </div>
                                    </div>
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

@include('admin.components.select2-default')
@include('admin.components.sweetalert')
@include('admin.components.list-delete')

@push('scripts')
    <script>
        $('#product_name').prop('disabled', true);
        $('#product_sku').prop('disabled', true);
    </script>
@endpush
