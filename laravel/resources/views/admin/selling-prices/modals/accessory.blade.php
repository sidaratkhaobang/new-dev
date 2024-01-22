<div class="modal fade" id="accessory-modal" aria-labelledby="accessory-modal" aria-hidden="false">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-popout">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="accessory-modal-label">{{ __('replacement_cars.accessory_detail') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-4">
                    <div class="table-wrap">
                        <table class="table table-striped">
                            <thead class="bg-body-dark">
                                <th>#</th>
                                <th>{{ __('replacement_cars.accessory') }}</th>
                                <th>{{ __('replacement_cars.amount') }}</th>
                                <th>{{ __('lang.remark') }}</th>
                            </thead>
                            <tbody>
                                @if (sizeof($car_accessory) > 0)
                                    @foreach ($car_accessory as $index => $item)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td>{{ $item->name }}</td>
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
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-clear-search"
                    data-bs-dismiss="modal">{{ __('lang.back') }}</button>
            </div>
        </div>
    </div>
</div>
