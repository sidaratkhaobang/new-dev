<div class="btn-group">
    <div class="col-sm-12">
        <div class="dropdown dropleft">
            <button type="button" class="btn btn-sm btn-alt-secondary dropdown-toggle" id="dropdown-dropleft-dark"
                data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <i class="fa fa-ellipsis-vertical"></i>
            </button>
            <div class="dropdown-menu" aria-labelledby="dropdown-dropleft-dark">

                @can(Actions::View . '_' . Resources::InstallEquipment)
                    <a class="dropdown-item" href="{{ route('admin.install-equipments.show', ['install_equipment' => $item]) }}"><i class="fa fa-eye me-1"></i> ดูข้อมูล</a>
                @endcan
            
                @can(Actions::Manage . '_' . Resources::InstallEquipment)
                    @if (in_array($item->status, [
                        InstallEquipmentStatusEnum::PENDING_REVIEW, 
                        InstallEquipmentStatusEnum::CONFIRM,
                        InstallEquipmentStatusEnum::WAITING, 
                        InstallEquipmentStatusEnum::INSTALL_IN_PROCESS, 
                        InstallEquipmentStatusEnum::OVERDUE,
                        InstallEquipmentStatusEnum::DUE,
                        // InstallEquipmentStatusEnum::INSTALL_COMPLETE,
                    ]))
                        <a class="dropdown-item" href="{{ route('admin.install-equipments.edit', ['install_equipment' => $item]) }}"><i class="far fa-edit me-1"></i> แก้ไข</a>
                    @endif
                @endcan
                @can(Actions::Manage . '_' . Resources::InstallEquipment)
                    @if (in_array($item->status, [InstallEquipmentStatusEnum::PENDING_REVIEW]))
                        <a class="dropdown-item btn-delete-row" href="javascript:void(0)"
                            data-route-delete="{{ route('admin.install-equipments.destroy', ['install_equipment' => $item->id]) }}"><i class="fa fa-trash-alt me-1"></i> ลบ</a>
                    @endif
                @endcan 
            </div>
        </div>
    </div>
</div>
