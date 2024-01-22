<div class="btn-group" id={{ $id }}>
    <div class="col-sm-12">
        <div class="dropdown dropleft">
            <button type="button" class="btn btn-sm btn-alt-secondary dropdown-toggle" id="dropdown-dropleft-dark"
                data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <i class="fa fa-ellipsis-vertical"></i>
            </button>
            <div class="dropdown-menu" aria-labelledby="dropdown-dropleft-dark">
                @if ($view_route)
                    @can($view_permission)
                        <a class="dropdown-item" href="{{ $view_route }}"><i class="fa fa-eye me-1"></i> ดูข้อมูล</a>
                    @endcan
                @endif
                @if ($edit_route)
                    @can($manage_permission)
                        <a class="dropdown-item" href="{{ $edit_route }}"><i class="far fa-edit me-1"></i> แก้ไข</a>
                    @endcan
                @endif
                {{ $slot }}
                @if ($delete_route)
                    @can($manage_permission)
                        <a class="dropdown-item btn-delete-row" href="javascript:void(0)"
                            data-route-delete="{{ $delete_route }}"><i class="fa fa-trash-alt me-1"></i> ลบ</a>
                    @endcan
                @endif
                {{ $end_slot ?? null }}
            </div>
        </div>
    </div>
</div>
