<label class="{{ $label_class }}" for="{{ $id }}">
    {{ $label }}</label>
<br>
@if (!empty($list))
    @foreach ($list as $item)
        @if (!empty($item['url']))
            <a href="{{ $item['url'] }}" target="_blank">
                {{ $item['file_name'] }}
            </a>
            <br>
        @endif
    @endforeach
@endif
