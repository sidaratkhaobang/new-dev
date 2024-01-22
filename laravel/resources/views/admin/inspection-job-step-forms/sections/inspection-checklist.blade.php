<div class="table-wrap db-scroll">
    <table class="table table-striped table-vcenter">
        <thead class="bg-body-dark">
            <tr>
                <th style="width: 5px;"></th>
                <th>@sortablelink('seq', __('inspection_cars.inspection_seq'))</th>
            </tr>
        </thead>
        @if (count($list) > 0)
        <tbody id="step_table">
            @foreach ($list as $index => $d)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $d->name }}</td>
                </tr>
                <tr>
                    <td></td>
                    <td>
                        <div class="table-wrap db-scroll">
                            <table class="table table-striped table-vcenter">
                                <thead class="bg-body-dark">
                                    <tr>
                                        <th style="width: 40%;" ></th>
                                        <th style="width: 15%;" > <input type="checkbox" id="check_all-{{ $index }}"
                                                class="form-check-input check_all" value="1" />
                                            @sortablelink('seq', __('inspection_cars.have'))</th>
                                        <th style="width: 15%;" > <input type="checkbox" id="check_all2-{{ $index }}"
                                                class="form-check-input check_all" value="0" />
                                            @sortablelink('seq', __('inspection_cars.no_have'))</th>
                                        <th style="width: 30%;" >@sortablelink('seq', __('inspection_cars.remark'))</th>
                                    </tr>
                                </thead>
                                @if (count($d->subseq) > 0)
                                <tbody id="step_table">
                                    @foreach ($d->subseq as $index2 => $d2)
                                        <tr>
                                            <td>{{ $d2->name2 }}</td>
                                            <td>
                                                <div class="form-check d-inline-block">
                                                    <input type="radio" id="radio"
                                                        class="form-check-input radio-{{ $index }} radio-{{ $index }}-{{ $index2 }}"
                                                        name="data[{{ $d2->id }}][radio]"
                                                        @if ($d2->is_pass == 1) checked @endif
                                                        value="1">
                                                </div>
                                            </td>
                                            <td>
                                                <div class="form-check d-inline-block">
                                                    <input type="radio" id="radio2"
                                                        class="form-check-input radio2-{{ $index }} radio2-{{ $index }}-{{ $index2 }}"
                                                        name="data[{{ $d2->id }}][radio]"
                                                        @if ($d2->is_pass == 0 && $d2->is_pass != '') checked @endif
                                                        value="0">
                                                </div>
                                            </td>
                                            <td>
                                                <input class="form-control" type="text"
                                                    name="data[{{ $d2->id }}][remark]"
                                                    value="{{ $d2->remark }}" placeholder="หมายเหตุ" />
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                @else
                                <tbody>
                                    <tr class="table-empty">
                                        <td class="text-center" colspan="5">{{ __('lang.no_list') }}</td>
                                    </tr>
                                </tbody>
                                @endif
                            </table>
                        </div>
                    </td>
                </tr>
            @endforeach
        </tbody>
        @else
        <tbody>
            <tr class="table-empty">
                <td class="text-center" colspan="3">“
                    {{ __('lang.no_list') }} “</td>
            </tr>
        </tbody>
        @endif
    </table>
</div>

@push('scripts')
    <script>
        var list = @json($list);
        list.forEach((item, index) => {
            item.subseq.forEach((item2, index2) => {

                $('#check_all-' + index).click(function() {

                    if ($(this).is(':checked')) {
                        $('.radio-' + index + '-' + index2).prop('checked', true);
                        $('#check_all2-' + index).prop('checked', false);
                    } else {
                        $('.radio-' + index + '-' + index2).prop('checked', false);
                    }
                });
                $('#check_all2-' + index).click(function() {

                    if ($(this).is(':checked')) {
                        $('.radio2-' + index + '-' + index2).prop('checked', true);
                        $('#check_all-' + index).prop('checked', false);
                    } else {
                        $('.radio2-' + index + '-' + index2).prop('checked', false);
                    }
                });

                $('.radio-' + index + '-' + index2).click(function() {
                    if ($(this).prop("checked")) {
                        $("#check_all-" + index).prop("checked", $('.radio-' + index).length === $(
                            '.radio-' + index + ':checked').length);
                        $("#check_all2-" + index).prop("checked", false);
                    }
                });

                $('.radio2-' + index + '-' + index2).click(function() {
                    if ($(this).prop("checked")) {
                        $("#check_all2-" + index).prop("checked", $('.radio2-' + index).length ===
                            $('.radio2-' + index + ':checked').length);
                        $("#check_all-" + index).prop("checked", false);
                    }
                });

            });
            var allPass = item.subseq.every(subitem => subitem.is_pass == 1);
            var allNotPass = item.subseq.every(subitem => subitem.is_pass == 0);
            if (allPass) {
                $('#check_all-' + index).prop('checked', true);
            }
            if (allNotPass) {
                $('#check_all2-' + index).prop('checked', true);
            }
        });
    </script>
@endpush
