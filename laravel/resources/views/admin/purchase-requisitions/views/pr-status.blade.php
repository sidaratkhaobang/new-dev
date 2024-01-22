{{-- Approve --}}
@if (in_array($d->status, [PRStatusEnum::CONFIRM, PRStatusEnum::CANCEL]) && $d->reject_reason == null && $d->reviewed_at != null)
    <h4>{{ __('purchase_requisitions.approve_table') }}</h4>
    <hr>
    <div class="row push mb-4">
        <div class="col-sm-3">
            <x-forms.input-new-line id="review_by" :value="$d->reviewedBy ? $d->reviewedBy->name : null" :label="__('purchase_requisitions.approve_by')" />
        </div>
        <div class="col-sm-3">
            <x-forms.input-new-line id="review_department" :value="$d->reviewedBy && $d->reviewedBy->department ? $d->reviewedBy->department->name : null" :label="__('purchase_requisitions.department')" />
        </div>
        <div class="col-sm-3">
            <x-forms.input-new-line id="reviewed_at" :value="$d->reviewed_at ? get_thai_date_format($d->reviewed_at, 'd/m/Y') : null" :label="__('purchase_requisitions.approve_at')" />
        </div>
    </div>
    <br>
@endif
{{-- end Approve --}}

{{-- Reject --}}
@if (in_array($d->status, [PRStatusEnum::REJECT, PRStatusEnum::CANCEL]) && $d->reject_reason != null)
    <h4>{{ __('purchase_requisitions.reject_table') }}</h4>
    <hr>
    <div class="row push mb-4">
        <div class="col-sm-3">
            <x-forms.input-new-line id="review_by" :value="$d->reviewedBy ? $d->reviewedBy->name : null" :label="__('purchase_requisitions.reject_by')" />
        </div>
        <div class="col-sm-3">
            <x-forms.input-new-line id="review_department" :value="$d->reviewedBy && $d->reviewedBy->department ? $d->reviewedBy->department->name : null" :label="__('purchase_requisitions.department')" />
        </div>
        <div class="col-sm-3">
            <x-forms.input-new-line id="reviewed_at" :value="$d->reviewed_at ? get_thai_date_format($d->reviewed_at, 'd/m/Y') : null" :label="__('purchase_requisitions.reject_at')" />
        </div>
    </div>
    <div class="row push mb-4">
        <div class="col-sm-12">
            <x-forms.input-new-line id="reject_reason" :value="$d->reject_reason" :label="__('purchase_requisitions.reject_reason')" />
        </div>
    </div>
    <br>
@endif
{{-- end Reject --}}

{{-- Cancel --}}
@if ($d->status == PRStatusEnum::CANCEL)
    <h4>{{ __('purchase_requisitions.cancel_table') }}</h4>
    <hr>
    <div class="row push mb-4">
        <div class="col-sm-12">
            <x-forms.input-new-line id="cancel_reason" :value="$d->cancel_reason" :label="__('purchase_requisitions.cancel_reason')" />
        </div>
    </div>
    <br>
@endif
{{-- end Cancel --}}
