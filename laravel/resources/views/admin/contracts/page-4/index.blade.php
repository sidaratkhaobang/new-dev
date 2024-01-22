@include('admin.contracts.sections.btn-tap-group')
<div class="block block-rounded">
    <div class="block-content">
        @if(isset($quotation))
            @foreach($quotation?->quotation_forms as $item)
                <div class="row">
                    <div class="col-sn-3">
                        <p>{{$item->name}}</p>
                        <ul>
                            @foreach($item?->quotation_form_check_list as $sub)
                                <li>{{$sub->name}}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endforeach
        @else
            {{ __('lang.no_list') }}
        @endif
    </div>
</div>
