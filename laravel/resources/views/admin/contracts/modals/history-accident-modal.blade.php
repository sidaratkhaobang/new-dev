@push('custom_styles')
    <style>
        .body-add-btn {
            display: flex;
            flex-direction: column-reverse;
            justify-content: flex-start;
            align-items: flex-end;
        }
    </style>
@endpush
<div class="modal fade" id="modal-history-accident" tabindex="-1" aria-labelledby="modal-edit-contract" aria-hidden="false">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-popout" style="width:1250px;">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="wage-job-modal-label">{{ __('ประวัติอุบัติเหตุ') }} <span id="modal-title"></span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="save-form">
                <x-forms.hidden id="contract_id" :value="null"/>
                <div class="modal-body">
                    <div class="row mt-2">
                        @include('admin.contracts.table.table-history-accident')
                    </div>
                </div>
            </form>
            <div class="modal-footer"></div>
        </div>
    </div>
</div>
