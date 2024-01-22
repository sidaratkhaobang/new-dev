<div class="block {{ __('block.styles') }}">
    <div class="block-content">
        <div class="row push my-auto">
            <div class="col-auto">
                <div class="row push ">
                    <div class="col-sm-3">
                        <img src="{{ asset('images/user/user.png') }}" alt="Profile Image"
                            style=" width:70px; height:70px;">
                    </div>
                </div>
            </div>
            <div class="col-sm-10">
                <div class="row push ">
                    <div class="col-sm-1">
                        <span>{{ __('repairs.user_by') }}</span>
                    </div>
                    <div class="col-sm-3">
                        <b>{{ $d->createdBy ? $d->createdBy->name : get_user_name() }}</b>
                    </div>

                    <div class="col-sm-1">
                        <span>{{ __('repairs.user_role') }}</span>
                    </div>
                    <div class="col-sm-3">
                        <b>{{ $d->createdBy && $d->createdBy->role ? $d->createdBy->role->name : get_role_name() }}</b>
                    </div>
                </div>
                <div class="row push ">
                    <div class="col-sm-1">
                        <span>{{ __('repairs.user_date') }}</span>
                    </div>
                    <div class="col-sm-3">
                        <b>{{ $d->created_at ? get_thai_date_format($d->created_at, 'd/m/Y H:i') : get_thai_date_format(null, 'd/m/Y H:i') }}</b>
                    </div>
                    <div class="col-sm-1">
                        <span>{{ __('repairs.user_branch') }}</span>
                    </div>
                    <div class="col-sm-3">
                        <b>{{ $d->createdBy && $d->createdBy->branch ? $d->createdBy->branch->name : get_branch_name() }}</b>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
