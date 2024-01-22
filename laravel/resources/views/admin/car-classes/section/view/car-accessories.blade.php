<h5>{{ __('car_classes.accessories_table_name') }} </h5>
<hr>
<div id="class-car-accessories">
    <div class="table-wrap mb-4">
        <table class="table table-striped">
            <thead class="bg-body-dark">
                <th>#</th>
                <th>{{ __('car_classes.accessories') }}</th>
                {{-- <th>{{ __('car_classes.class') }}</th> --}}
                <th>{{ __('car_classes.remark') }}</th>
            </thead>
            <tbody>
                @if (sizeof($class_accessory_list) > 0)
                    @foreach ($class_accessory_list as $index => $class_accessory)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $class_accessory->accessory_text }}</td>
                        {{-- <td>{{ $class_accessory->accessory_version_text }}</td> --}}
                        <td>{{ $class_accessory->remark }}</td>
                    </tr>
                    @endforeach
                @else
                <tr class="table-empty">
                    <td class="text-center" colspan="5">“ {{ __('lang.no_list').__('car_classes.accessories_table_name') }} “</td>
                </tr>
                @endif
            </tbody>
        </table>
    </div>
</div>
