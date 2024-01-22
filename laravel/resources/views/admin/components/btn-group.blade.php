@push('styles')
    <style>
        .nav-btn-custom .nav-link {
            display: inline-block;
            line-height: 1.5;
            color: #4D82F3 !important;
            text-align: center;
            vertical-align: middle;
            cursor: pointer;
            -webkit-user-select: none;
            -moz-user-select: none;
            user-select: none;
            background-color: transparent;
            padding: 0.375rem 0.75rem;
            font-size: 1rem;
            font-weight: 300 !important;
            border: 1px solid #cbd4e1;
            background-color: #FFF;
            min-width: 150px;
        }

        .nav-btn-custom .nav-item:not(:first-child) .nav-link {
            margin-left: -1px;
        }
        .nav-btn-custom .nav-item:first-child .nav-link {
            border-top-left-radius: 0.375rem;
            border-bottom-left-radius: 0.375rem;
        }

        .nav-btn-custom .nav-item:last-child .nav-link {
            border-top-right-radius: 0.375rem;
            border-bottom-right-radius: 0.375rem;
        }

        .nav-btn-custom .nav-link.active {
            color: #fff !important;
            background: #4D82F3;
        }
    </style>
@endpush

<div class="wrapper mb-4">
    <ul class="nav nav-btn-custom" role="tablist">
        @foreach ($tabs as $item)
            <li class="nav-item" role="presentation">
                <a class="nav-link {{ isset($item['active']) ? 'active' : '' }}" id="{{ $item['id'] }}-tab" data-bs-toggle="tab"
                    @if (isset($item['route'])) href="{{ $item['route'] }}" @endif
                    data-bs-target="#{{ $item['id'] }}" role="tab" aria-controls="{{ $item['id'] }}"
                    aria-selected="{{ isset($item['active']) ? 'true' : 'false' }}">{{ $item['name'] }}</a>
            </li>
        @endforeach
    </ul>
</div>
