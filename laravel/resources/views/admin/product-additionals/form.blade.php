@extends('admin.layouts.layout')

@section('page_title', $page_title)

@section('content')
    <x-blocks.block>
        <form id="save-form">
            <div class="row push">
                <div class="col-sm-6">
                    <x-forms.input-new-line id="name" :value="$d->name" :label="__('product_additionals.name')" :optionals="['required' => true]" />
                </div>
                <div class="col-sm-6">
                    <x-forms.input-new-line id="price" :value="number_format($d->price,2)" :label="__('product_additionals.price')" :optionals="['required' => true,'input_class' => 'number-format col-sm-4']" />
                </div>
            </div>
            <div class="row push">
                <div class="col-sm-6">
                    <x-forms.radio-inline id="is_stock" :value="$d->is_stock" :list="$yes_no_list" :label="__('product_additionals.is_stock')" />
                </div>
                <div class="col-sm-6" id="stock_amount_show"
                    @if (strcmp($d->is_stock, BOOL_TRUE)== 0) style="display: block" @else style="display: none" @endif>
                    <x-forms.input-new-line id="amount" :value="$d->amount" :label="__('product_additionals.amount')" :optionals="['type' => 'number']" />
                </div>
            </div>

            <x-forms.hidden id="id" :value="$d->id" />
            <x-forms.submit-group :optionals="['url' => 'admin.product-additionals.index', 'view' => empty($view) ? null : $view]" />
        </form>
    </x-blocks.block>
@endsection

@include('admin.components.select2-default')
@include('admin.components.sweetalert')
@include('admin.components.form-save', [
    'store_uri' => route('admin.product-additionals.store'),
])

@push('scripts')
    <script>
        $status = '{{ isset($view) }}';
        if ($status) {
            $('#name').prop('disabled', true);
            $('#price').prop('disabled', true);
            $('input[name="is_stock"]').prop('disabled', true);;
            $('#amount').prop('disabled', true);
        }

        $('input[name="is_stock"]').on("click", function() {
            var is_stock = $('input[name="is_stock"]:checked').val();
            if (is_stock === '{{ BOOL_TRUE }}') {
                document.getElementById("stock_amount_show").style.display = "block"
            } else {
                document.getElementById("stock_amount_show").style.display = "none"
                $('#amount').val('');
            }
        });
    </script>
@endpush
