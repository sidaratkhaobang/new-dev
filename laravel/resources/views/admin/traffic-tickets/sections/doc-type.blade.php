<x-blocks.block :title="__('traffic_tickets.doc_type')" id="doc_block">
    <div class="row  gx-3 gy-3 ">
        @foreach ($doc_type_list as $item)
        <div class="col-4 col-sm-3">
            <x-forms.radio-block id="{{ $item->id }}" name="document_type" value="{{ $item->id }}" 
                selected="{{ $d->document_type }}" >
                <span class="block-title p-3">{{ $item->name }}</span>
            </x-forms.radio-block>
        </div>
        @endforeach
    </div>
</x-blocks.block>
