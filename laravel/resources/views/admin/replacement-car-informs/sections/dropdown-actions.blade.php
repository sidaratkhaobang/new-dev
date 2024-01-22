<div class="btn-group">
    <div class="col-sm-12">
        <div class="dropdown dropleft">
            <button type="button" class="btn btn-sm btn-alt-secondary dropdown-toggle" id="dropdown-dropleft-dark"
                data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <i class="fa fa-ellipsis-vertical"></i>
            </button>
            <div class="dropdown-menu" aria-labelledby="dropdown-dropleft-dark">
                @can(Actions::View . '_' . Resources::ReplacementCarInform)
                    <a class="dropdown-item" href="{{ route('admin.replacement-car-informs.show', ['replacement_car_inform' => $d]) }}"><i class="fa fa-eye me-1"></i> ดูข้อมูล</a>
                @endcan
                @can(Actions::Manage . '_' . Resources::ReplacementCarInform)
                    @if (in_array($d->status, [
                        ReplacementCarStatusEnum::PENDING_INSPECT, 
                    ]))
                        <a class="dropdown-item" href="{{ route('admin.replacement-car-informs.edit', ['replacement_car_inform' => $d]) }}"><i class="far fa-edit me-1"></i> แก้ไข</a>
                    @endif
                @endcan
                {{-- @can(Actions::Manage . '_' . Resources::ReplacementCar) --}}
                    {{-- @if (in_array($item->status, [InstallEquipmentStatusEnum::WAITING])) --}}
                        {{-- <a class="dropdown-item btn-delete-row" href="javascript:void(0)" --}}
                            {{-- data-route-delete="{{ route('admin.replacement-car-informs.destroy', ['replacement_car_inform' => $d->id]) }}"><i class="fa fa-trash-alt me-1"></i> ลบ</a> --}}
                    {{-- @endif --}}
                {{-- @endcan  --}}
            </div>
        </div>
    </div>
</div>
