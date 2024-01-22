<div class="block {{ __('block.styles') }}">
    {{-- @section('block_options_list')
    <div class="block-options">
    </div>
@endsection --}}

    @include('admin.components.block-header', [
        'text' => __('ownership_transfers.optional_document_remark'),
        'block_option_id' => '_list',
        'is_toggle' => true,
    ])
    <div class="block-content">
        <div class="row mb-4">
            <div class="col-sm-3">
                @if (isset($view))
                    <x-forms.view-image :id="'optional_files'" :label="__('registers.optional_files')" :list="$optional_files" />
                @else
                    <x-forms.upload-image :id="'optional_files'" :label="__('registers.optional_files')" />
                @endif
            </div>
            <div class="col-sm-9">
                <x-forms.input-new-line id="remark" :value="$d->remark" :label="__('registers.remark')" />
            </div>

        </div>
    </div>
</div>
