<div class="btn-group">
    <div class="col-sm-12">
        <div class="dropdown dropleft">
            <button type="button" class="btn btn-sm btn-alt-secondary dropdown-toggle" id="dropdown-dropleft-dark"
                data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <i class="fa fa-ellipsis-vertical"></i>
            </button>
            <div class="dropdown-menu" aria-labelledby="dropdown-dropleft-dark">
                @can(Actions::View . '_' . Resources::ReplacementCarApprove)
                    <a class="dropdown-item" href="{{ route('admin.replacement-car-approves.show', ['replacement_car_approve' => $d]) }}"><i class="fa fa-eye me-1"></i> ดูข้อมูล</a>
                @endcan
            </div>
        </div>
    </div>
</div>
