<div class="btn-group">
    <div class="col-sm-12">
        <div class="dropdown dropleft">
            <button type="button" class="btn btn-sm btn-alt-secondary dropdown-toggle" id="dropdown-dropleft-dark"
                data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <i class="fa fa-ellipsis-vertical"></i>
            </button>
            <div class="dropdown-menu" aria-labelledby="dropdown-dropleft-dark">
                @can(Actions::View . '_' . Resources::ReplacementCar)
                    <a class="dropdown-item" href="{{ route('admin.replacement-cars.show', ['replacement_car' => $d]) }}"><i class="fa fa-eye me-1"></i> ดูข้อมูล</a>
                @endcan
                @can(Actions::Manage . '_' . Resources::ReplacementCar)
                    @if (in_array($d->status, [
                        ReplacementCarStatusEnum::PENDING_INSPECT,
                        ReplacementCarStatusEnum::PENDING,
                        ReplacementCarStatusEnum::IN_PROCESS,
                    ]))
                        <a class="dropdown-item" href="{{ route('admin.replacement-cars.edit', ['replacement_car' => $d]) }}"><i class="far fa-edit me-1"></i> แก้ไข</a>
                    @endif
                @endcan
            </div>
        </div>
    </div>
</div>
