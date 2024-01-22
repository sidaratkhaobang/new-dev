<div class="btn-group">
    <div class="col-sm-12">
        <div class="dropdown dropleft">
            <button type="button" class="btn btn-sm btn-alt-secondary dropdown-toggle" id="dropdown-dropleft-dark"
                data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <i class="fa fa-ellipsis-vertical"></i>
            </button>
            <div class="dropdown-menu" aria-labelledby="dropdown-dropleft-dark">
                @can(Actions::View . '_' . Resources::InsuranceCarCmiRenew)
                    <a class="dropdown-item" href="{{ route('admin.insurance-cmi-renew.show', ['insurance_cmi_renew' => $d]) }}"><i class="fa fa-eye me-1"></i> ดูข้อมูล</a>
                @endcan
                @can(Actions::Manage . '_' . Resources::InsuranceCarCmiRenew)
                    @if (in_array($d->status, [
                        InsuranceStatusEnum::PENDING,
                        InsuranceStatusEnum::IN_PROCESS,
                    ]))
                        <a class="dropdown-item" href="{{ route('admin.insurance-cmi-renew.edit', ['insurance_cmi_renew' => $d]) }}"><i class="far fa-edit me-1"></i> แก้ไข</a>
                    @endif
                @endcan
            </div>
        </div>
    </div>
</div>
