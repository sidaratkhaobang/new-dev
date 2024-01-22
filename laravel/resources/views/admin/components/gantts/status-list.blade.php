<div>
    <ol class="breadcrumb">
        @foreach ($status_list as $item)
            <li class="pe-4 text-{{ $item->class }}"><i class="fa fa-square me-1"></i>
                {{ $item->name }}
            </li>
        @endforeach
    </ol>
</div>