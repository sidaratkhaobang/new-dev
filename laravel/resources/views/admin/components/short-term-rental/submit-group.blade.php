<div class="row mt-2">
    <div class="{{ $input_class }}">
        <a class="btn btn-danger btn-custom-size {{ $input_class_clear }}"
            style="color: white">{{ __('lang.cancel') }}</a>

        @if (isset($return_list))
            <div class="btn-group">
                <button type="button" class="btn btn-outline-secondary dropdown-toggle-split" aria-expanded="false"><i
                        class="fa fa-arrow-rotate-left"></i> {{ __('short_term_rentals.back') }}</button>
                <button type="button" class="btn btn-outline-secondary dropdown-toggle dropdown-toggle-split arrow"
                    style="min-width: 5px !important;" data-bs-toggle="dropdown" aria-expanded="false">
                </button>
                <ul class="dropdown-menu">
                    @foreach ($return_list as $key => $item)
                        @if ($key < $step)
                            <li>
                                <a href="{{ $item['url'] }}" class="dropdown-item">
                                    <i class="{{ $item['icon_name'] }} mt-1"></i> {{ $item['text'] }}</a>
                            </li>
                        @endif
                    @endforeach
                </ul>
            </div>
        @endif
        @if (!isset($status))
            @if (isset($isdraft) && $isdraft)
                <button type="button" class="btn btn-secondary btn-custom-size btn-save-form" data-draft="true">
                    <i class="{{ $icon_draft_class_name }} mt-1"></i> {{ $btn_draft_name }}</button>
            @endif
        @endif
        <button type="button" class="btn btn-primary btn-custom-size {{ $input_class_submit ?? 'btn-save-form' }}"
            data-status="{{ $data_status }}"> <i class="{{ $icon_class_name }} mt-1"></i>
            {{ $status ? __('lang.save') : $btn_name }}</button>
    </div>
</div>
