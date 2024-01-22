<h5>{{ __('car_classes.color_table_name') }}</h5>
<hr>
<div id="class-car-accessories">
    <div class="table-wrap mb-4">
        <table class="table table-striped">
            <thead class="bg-body-dark">
                <th>#</th>
                <th>{{ __('car_classes.color') }}</th>
                <th>{{ __('car_classes.standard_price') }}</th>
                <th>{{ __('car_classes.color_price') }}</th>
                <th>{{ __('car_classes.total_price') }}</th>
                <th>{{ __('car_classes.remark') }}</th>
            </thead>
            <tbody>
                @if (sizeof($car_class_color_list) > 0)
                    @foreach ($car_class_color_list as $index => $class_color)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $class_color->color_text }}</td>
                        <td>{{ $class_color->standard_price }}</td>
                        <td>{{ $class_color->color_price }}</td>
                        <td>{{ $class_color->total_price }}</td>
                        <td>{{ $class_color->remark }}</td>
                    </tr>
                    @endforeach
                @else
                <tr class="table-empty">
                    <td class="text-center" colspan="6">“ {{ __('lang.no_list').__('car_classes.color_table_name') }} “</td>
                </tr>
                @endif
            </tbody>
        </table>
    </div>
</div>
