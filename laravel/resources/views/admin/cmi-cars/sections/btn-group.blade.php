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
        <li class="nav-item" role="presentation">
            <a class="nav-link active" id="first-tab" data-bs-toggle="tab"
                data-bs-target="#first" role="tab" aria-controls="first"
                aria-selected="true">{{ __('cmi_cars.cmi_info') }}</a>
        </li>
        <li class="nav-item" role="presentation">
            <a class="nav-link" id="second-tab" data-bs-toggle="tab"
                data-bs-target="#second" role="tab" aria-controls="second"
                aria-selected="false" tabindex="-1">{{ __('cmi_cars.coverage_info') }}</a>
        </li>
    </ul>
</div>
