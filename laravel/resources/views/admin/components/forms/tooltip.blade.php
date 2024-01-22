<span class="js-bs-tooltip" data-toggle="tooltip" data-placement="top" title="{{ $title }}">
    @if ($limit)
        {{ str_limit($title, $limit) }}
    @else
        {{ $title }}
    @endif
</span>
