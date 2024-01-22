<div class="btn-group">
    <div class="col-sm-12">
        <div class="dropdown dropleft">
            <button type="button" class="btn btn-sm btn-alt-secondary dropdown-toggle" id="dropdown-dropleft-dark"
                data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <i class="fa fa-ellipsis-vertical"></i>
            </button>
            <div class="dropdown-menu" aria-labelledby="dropdown-dropleft-dark">
                    <a class="dropdown-item" href="javascript:void(0)" v-on:click="edit(index)"><i class="far fa-edit me-1"></i> แก้ไข</a>
                    <a class="dropdown-item btn-delete-row" href="javascript:void(0)" v-on:click="remove(index)"><i
                        class="fa fa-trash-alt me-1"></i> ลบ</a>
            </div>
        </div>
    </div>
</div>
