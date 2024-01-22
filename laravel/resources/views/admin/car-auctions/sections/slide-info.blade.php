<div class="block {{ __('block.styles') }}">
    @include('admin.components.block-header', [
        'text' => __('car_auctions.title_forklift'),
        'block_icon_class' => 'icon-document',
    ])
    <div class="block-content">
        <div class="row push mb-4">
            @foreach ($link_list as $key => $item)
                <div class="col-sm-3">
                    <label class="text-start col-form-label"
                        for="{{ $key }}">{{ __('car_auctions.' . $key) }}</label>
                    <br>
                    <a class="fw-bolder mb-0" href="{{ $item['link'] ?? null }}" target="_blank">
                        <i class="si si-link text-dark"></i> <strong>{{ $item['worksheet_no'] ?? null }}</strong>
                    </a>
                </div>
            @endforeach
        </div>
    </div>
</div>
