<div class="block {{ __('block.styles') }}">
    @include('admin.components.block-header', [
        'text' => __('car_auctions.title_car_price'),
        'block_icon_class' => 'icon-document',
    ])
    <div class="block-content">
        <div class="table-wrap">
            <table class="table table-striped">
                <thead class="bg-body-dark text-center">
                    <th>{{ __('car_auctions.car_price') }}</th>
                    <th>{{ __('car_auctions.depreciation_age') }}</th>
                    <th>{{ __('car_auctions.depreciation_month') }}</th>
                    <th>{{ __('car_auctions.age_car') }}</th>
                    <th>{{ __('car_auctions.depreciation_age_remain') }}</th>
                    <th>{{ __('car_auctions.depreciation_current') }}</th>
                    <th>{{ __('car_auctions.target') }}</th>
                    <th>{{ __('car_auctions.median_price') }}</th>
                </thead>
                <tbody>
                    <tr>
                        <td>
                            <input type="text" class="form-control col-sm-4" id="car_price" name="car_price"
                                value="{{ number_format($car->car_price, 2) }}" disabled>
                        </td>
                        <td>
                            <input type="number" class="form-control col-sm-4" id="depreciation_age_num" min="1"
                                name="depreciation_age_num" value="{{ $d->depreciation_age }}">
                            <x-forms.hidden id="depreciation_age" :value="$d->depreciation_age" />
                        </td>
                        <td>
                            <input type="text" class="form-control col-sm-4" id="depreciation_month_num"
                                name="depreciation_month_num" value="{{ number_format($d->depreciation_month, 2) }}"
                                disabled>
                            <x-forms.hidden id="depreciation_month" :value="$d->depreciation_month" />
                        </td>
                        <td>
                            <input type="text" class="form-control col-sm-4" id="month_age" name="month_age"
                                value="{{ $car->month_age }}" disabled>
                        </td>
                        <td>
                            <input type="text" class="form-control col-sm-4" id="depreciation_age_remain_num"
                                name="depreciation_age_remain_num" value="{{ $d->depreciation_age_remain }}" disabled>
                            <x-forms.hidden id="depreciation_age_remain" :value="$d->depreciation_age_remain" />
                        </td>
                        <td>
                            <input type="text" class="form-control col-sm-4" id="depreciation_current_num"
                                name="depreciation_current_num"
                                value="{{ number_format($d->depreciation_current, 2) }}" disabled>
                            <x-forms.hidden id="depreciation_current" :value="$d->depreciation_current" />
                        </td>
                        <td>
                            <input type="text" class="form-control col-sm-4 number-format" id="target_num"
                                name="target_num" value="{{ $d->target }}">
                            <x-forms.hidden id="target" :value="$d->target" />
                        </td>
                        <td>
                            <input type="text" class="form-control col-sm-4" id="median_price_num"
                                name="median_price_num" value="{{ number_format($d->median_price, 2) }}" disabled>
                            <x-forms.hidden id="median_price" :value="$d->median_price" />
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
@push('scripts')
    <script>
        function numberWithCommas(x) {
            return x.toString().replace(/\B(?<!\.\d*)(?=(\d{3})+(?!\d))/g, ",");
        }

        function depreciationAge(num) {
            var month_age = document.getElementById("month_age").value;
            console.log(month_age);
            var age = month_age;
            if (num > month_age) {
                console.log('>');
                age = num - month_age;
            }
            else {
                console.log('<');
                age = month_age - num;
            }
            $('#depreciation_age_remain_num').val(age);
            $('#depreciation_age_remain').val(age);
            $('#depreciation_age').val(num);
        }
        var depreciation_age_num = document.getElementById("depreciation_age_num").value;
        depreciationAge(depreciation_age_num);
        $("#depreciation_age_num").on("input", function() {
            depreciation_age_num = $(this).val();
            depreciationAge(depreciation_age_num);
        });

        function depreciationMonth(num) {
            var car_price = document.getElementById("car_price").value;
            car_price = parseFloat(car_price.replace(/,/g, ''));
            var depreciation = car_price;
            if (num > 0) {
                depreciation = parseFloat(parseFloat(car_price) / num).toFixed(2);
            }
            $('#depreciation_month_num').val(numberWithCommas(depreciation));
            $('#depreciation_month').val(numberWithCommas(depreciation));
            $('#depreciation_age').val(num);
        }
        var depreciation_age_num = document.getElementById("depreciation_age_num").value;
        depreciationMonth(depreciation_age_num);
        $("#depreciation_age_num").on("input", function() {
            depreciation_age_num = $(this).val();
            depreciationMonth(depreciation_age_num);
        });

        function depreciationCurrent(month, age_remain, num) {
            var current = parseFloat(parseFloat(month) * age_remain).toFixed(2);
            $('#depreciation_current_num').val(numberWithCommas(current));
            $('#depreciation_current').val(numberWithCommas(current));
            $('#depreciation_age').val(num);
        }
        var depreciation_month_num = document.getElementById("depreciation_month_num").value;
        depreciation_month_num = parseFloat(depreciation_month_num.replace(/,/g, ''));
        var depreciation_age_remain_num = document.getElementById("depreciation_age_remain_num").value;
        var depreciation_age_num = document.getElementById("depreciation_age_num").value;
        depreciationCurrent(depreciation_month_num, depreciation_age_remain_num, depreciation_age_num);
        $("#depreciation_age_num").on("input", function() {
            depreciation_age_num = $(this).val();
            var depreciation_month_num = document.getElementById("depreciation_month_num").value;
            depreciation_month_num = parseFloat(depreciation_month_num.replace(/,/g, ''));
            var depreciation_age_remain_num = document.getElementById("depreciation_age_remain_num").value;
            depreciationCurrent(depreciation_month_num, depreciation_age_remain_num, depreciation_age_num);
        });

        function medianPrice(target, current) {
            var median_price = 0;
            if (target) {
                median_price = parseFloat(parseFloat(target) + current).toFixed(2);
            }
            console.log(numberWithCommas(target));
            $('#median_price_num').val(numberWithCommas(median_price));
            $('#median_price').val(numberWithCommas(median_price));
            $('#target').val(numberWithCommas(target));
        }
        $("#target_num").on("input", function() {
            target_num = $(this).val();
            target_num = parseFloat(target_num.replace(/,/g, ''));
            var depreciation_current_num = document.getElementById("depreciation_current_num").value;
            depreciation_current_num = parseFloat(depreciation_current_num.replace(/,/g, ''));
            medianPrice(target_num, depreciation_current_num);
        });
    </script>
@endpush
