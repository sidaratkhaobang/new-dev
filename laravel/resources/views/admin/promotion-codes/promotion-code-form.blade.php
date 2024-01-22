@extends('admin.layouts.layout')

@section('page_title', $page_title)

@section('content')
    <form id="save-form">
        <x-blocks.block :title="__('promotions.section_title_main')" :optionals="['is_toggle' => false]">
            <div class="row push mb-4">
                <div class="col-sm-3">
                    <x-forms.input-new-line id="code" :value="$d->code" :label="__('promotions.promotion_code')" />
                </div>
                <div class="col-sm-3">
                    <x-forms.input-new-line id="price" :value="number_format(floatval($d->selling_price), 2)" :label="__('promotions.selling_price')" />
                </div>
                <div class="col-sm-3">
                    <x-forms.date-input id="start_sale_date" name="start_sale_date" :value="$d->start_sale_date" :label="__('promotions.start_date')" />
                </div>
                <div class="col-sm-3">
                    <x-forms.date-input id="end_sale_date" name="end_sale_date" :value="$d->end_sale_date" :label="__('promotions.end_sale_date')" />
                </div>
            </div>
            <div class="row push mb-4">
                <div class="col-sm-3">
                    <x-forms.radio-inline id="is_sold" :value="$d->is_sold" :list="$is_sold_list" :label="__('promotions.is_sold')" />
                </div>
                <div class="col-sm-3">
                    <x-forms.date-input id="sold_date" name="sold_date" :value="$d->sold_date" :label="__('promotions.sold_date')" />
                </div>
                <div class="col-sm-3">
                    <x-forms.radio-inline id="is_used" :value="$d->is_used" :list="$is_used_list" :label="__('promotions.is_used')" />
                </div>
                <div class="col-sm-3">
                    <x-forms.date-input id="use_date" name="use_date" :value="$d->use_date" :label="__('promotions.use_date')" />
                </div>
            </div>
        </x-blocks.block>
        <x-blocks.block>
            <x-forms.hidden id="id" :value="$d->id" />
            <div class="row">
                <div class="col-sm-12 text-end">
                    <a class="btn btn-outline-secondary"
                        href="{{ route('admin.promotion-codes.index', ['promotion_id' => $promotion_id]) }}">{{ __('lang.back') }}</a>
                    @if (strcmp($d->is_sold, BOOL_FALSE) == 0)
                        <button type="button" class="btn btn-primary btn-save-form">{{ __('lang.save') }}</button>
                    @endif
                </div>
            </div>
        </x-blocks.block>
    </form>
@endsection

@include('admin.components.select2-default')
@include('admin.components.sweetalert')
@include('admin.components.form-save', [
    'store_uri' => route('admin.promotion-codes.promotion-code-update'),
])
@include('admin.components.date-input-script')

@push('scripts')
    <script>
        $('#code').prop('disabled', true);
        $('#price').prop('disabled', true);
        $('#start_sale_date').prop('disabled', true);
        $('#end_sale_date').prop('disabled', true);
        $('[name="is_used"]').prop('disabled', true);
        $('#use_date').prop('disabled', true);

        $status = '{{ $d->is_sold }}';
        if ($status === '{{ BOOL_TRUE }}') {
            $('input[name="is_sold"]').prop('disabled', true);
            $('#sold_date').prop('disabled', true);
        }
    </script>
@endpush
