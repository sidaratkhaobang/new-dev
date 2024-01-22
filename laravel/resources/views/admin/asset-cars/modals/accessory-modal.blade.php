<x-modal :id="'accessory'" :title="'ข้อมูลอุปกรณ์เสริม'">
    <div class="table-wrap">
        <table class="table table-striped">
            <thead class="bg-body-dark">
                <th>#</th>
                <th>{{ __('replacement_cars.accessory') }}</th>
                <th>{{ __('replacement_cars.amount') }}</th>
                <th>{{ __('lang.remark') }}</th>
            </thead>
            <tbody>
                @if (sizeof($asset_accessory) > 0)
                    @foreach ($asset_accessory as $index => $item)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $item->accessory_name }}</td>
                            <td>{{ $item->amount }}</td>
                            <td>{{ $item->remark }}</td>
                        </tr>
                    @endforeach
                @else
                    <tr>
                        <td class="text-center" colspan="4">" {{ __('lang.no_list') }} "</td>
                    </tr>
                @endif
            </tbody>
        </table>
    </div>
    <x-slot name="footer">
        <button type="button" class="btn btn-secondary btn-clear-search"
            data-bs-dismiss="modal">{{ __('lang.back') }}</button>
    </x-slot>
</x-modal>
