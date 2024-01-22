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
