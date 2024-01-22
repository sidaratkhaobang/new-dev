@push('styles')
    <style>
        .active {
            color: #4D82F3 !important;
            background-color: #E5EDFE !important;
            border-color: #4D82F3 !important;
        }

        .inactive {
            color: #94A3B8 !important;
            background-color: #F6F8FC !important;
            border-color: #CBD4E1 !important;
        }

        .active-complete {
            color: #6f9c40 !important;
            background-color: #dfecd1 !important;
            border-color: #6f9c40 !important;
        }
    </style>
@endpush
<div class="row mb-4">
    <div class="col-sm-12 float-center">
        <div class="d-flex justify-content-center">
            <div class="btn-group bg-white" role="group">
                <button type="button"
                    class="btn btn-outline-primary pe-none
                        {{ in_array($d->status, [MFlowStatusEnum::DRAFT]) ? 'active' : 'inactive' }}
                        {{ in_array($d->status, [MFlowStatusEnum::PENDING, MFlowStatusEnum::IN_PROCESS, MFlowStatusEnum::COMPLETE])
                            ? 'active-complete'
                            : 'inactive' }}">
                    @if (in_array($d->status, [MFlowStatusEnum::PENDING, MFlowStatusEnum::IN_PROCESS, MFlowStatusEnum::COMPLETE]))
                        <i class="fa fa-check"></i>
                    @endif
                    บันทึกข้อมูลค้างชำระ
                </button>
                <button type="button"
                    class="btn btn-outline-primary pe-none
                    {{ in_array($d->status, [MFlowStatusEnum::PENDING]) ? 'active' : 'inactive' }}
                    {{ in_array($d->status, [MFlowStatusEnum::IN_PROCESS, MFlowStatusEnum::COMPLETE]) ? 'active-complete' : 'inactive' }}">
                    @if (in_array($d->status, [MFlowStatusEnum::IN_PROCESS, MFlowStatusEnum::COMPLETE]))
                        <i class="fa fa-check"></i>
                    @endif
                    บันทึกข้อมูลแจ้งชำระ
                </button>
                <button type="button"
                    class="btn btn-outline-primary pe-none
                    {{ in_array($d->status, [MFlowStatusEnum::IN_PROCESS]) ? 'active' : 'inactive' }}
                    {{ in_array($d->status, [MFlowStatusEnum::COMPLETE]) ? 'active-complete' : 'inactive' }}">
                    @if (in_array($d->status, [MFlowStatusEnum::COMPLETE]))
                        <i class="fa fa-check"></i>
                    @endif
                    บันทึกข้อมูลการชำระ
                </button>
            </div>
        </div>
    </div>
</div>
