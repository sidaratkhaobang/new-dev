<div class="block {{ __('block.styles') }}">
    {{-- @section('block_options_list')
    <div class="block-options">
    </div>
@endsection --}}

    @include('admin.components.block-header', [
        'text' => __('tax_renewals.return_register_book_detail'),
        'block_option_id' => '_list',
        'is_toggle' => true,
    ])
    <div class="block-content">
        <div class="row mb-4">
            <div class="col-sm-3">
                <x-forms.date-input id="return_registration_book_date" :value="$d->return_registration_book_date" :label="__('tax_renewals.return_registration_book_date')" :optionals="['required' => true]" />
            </div>
        </div>

    </div>
</div>