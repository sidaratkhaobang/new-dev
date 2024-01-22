<div class="row gx-3 gy-3 mb-3">

    @foreach ($request_list as $index => $request_type)
        <div class="col-12 col-sm-3 col-md-2 col-lg-2">
            <x-forms.radio-block id="request_type_id_{{ $index }}" name="request_type_id" value="{{ $request_type->id }}" selected="{{ $d->type }}" >
                <span class="block-title" >{{ $request_type->name }}</span>
                {{-- <div class="block-img-wrap" >
                    <img src="{{ $service_type->image_url }}" alt="..." class="block-img">
                </div> --}}
            </x-forms.radio-block>
        </div>
    @endforeach
</div>