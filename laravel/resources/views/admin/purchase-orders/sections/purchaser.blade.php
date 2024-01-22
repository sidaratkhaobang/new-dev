<h4>{{ __('purchase_orders.purchaser_detail') }}</h4>
<hr>
<div class="row push mb-5">
    <div class="col-sm-3">
        <x-forms.input-new-line id="requester_name" :value="($purchase_requisition->createdBy) ? $purchase_requisition->createdBy->name : null" :label="__('purchase_orders.requester_name')" />
    </div>
    <div class="col-sm-3">
        <x-forms.input-new-line id="department" :value="($purchase_requisition->createdBy && $purchase_requisition->createdBy->department) ? $purchase_requisition->createdBy->department->name : null" :label="__('purchase_orders.department')" />
    </div>
</div>

@if (in_array($d->status, [\App\Enums\POStatusEnum::CONFIRM]))
<h4>{{ __('purchase_orders.approval_detail') }}</h4>
<hr>
<div class="row push mb-5">
    <div class="col-sm-3">
        <x-forms.input-new-line id="reviewer_name" :value="($d->reviewer)? $d->reviewer->name : null" :label="__('purchase_orders.approve_reviewer_name')" />
    </div>
    <div class="col-sm-3">
        <x-forms.input-new-line id="reviewer_department" :value="($d->reviewer && $d->reviewer->department)? $d->reviewer->department->name : null" :label="__('purchase_orders.department')" />
    </div>
    <div class="col-sm-3">
        <x-forms.input-new-line id="review_at" :value="$d->reviewed_at ? get_thai_date_format($d->reviewed_at, 'd/m/Y') : '-'" :label="__('purchase_orders.approve_at')" />
    </div>
</div>
@endif
@if (in_array($d->status, [\App\Enums\POStatusEnum::REJECT]))
<h4>{{ __('purchase_orders.disapproval_detail') }}</h4>
<hr>
<div class="row push">
    <div class="col-sm-3">
        <x-forms.input-new-line id="reviewer_name" :value="($d->reviewer)? $d->reviewer->name : null" :label="__('purchase_orders.disapprove_reviewer_name')" />
    </div>
    <div class="col-sm-3">
        <x-forms.input-new-line id="reviewer_department" :value="($d->reviewer && $d->reviewer->department)? $d->reviewer->department->name : null" :label="__('purchase_orders.department')" />
    </div>
    <div class="col-sm-3">
        <x-forms.input-new-line id="review_at" :value="$d->reviewed_at ? get_thai_date_format($d->reviewed_at, 'd/m/Y') : null" :label="__('purchase_orders.disapprove_at')" />
    </div>
</div>
<div class="row push mb-5">
    <div class="col-sm-6">
        <x-forms.input-new-line id="reason" :value="$d->reason" :label="__('purchase_orders.disapprove_reason')" />
    </div>
</div>
@endif
@if ($d->status == \App\Enums\POStatusEnum::CANCEL)
    <h4>{{ __('purchase_orders.cancel_table') }}</h4>
    <hr>
    <div class="row push mb-4">
        <div class="col-sm-6">
            <x-forms.input-new-line id="cancel_reason" :value="$d->reason" :label="__('purchase_orders.cancel_reason')" />
        </div>
    </div>
    <br>
@endif

<h4>{{ __('purchase_orders.purchase_order_detail') }}</h4>
<hr>
<div class="row push mb-5">
    <div class="col-sm-3">
        <x-forms.input-new-line id="purchase_order_no" name="purchase_order_no" :value="$d->po_no" :label="__('purchase_orders.purchase_order_no')" />
    </div>
    <div class="col-sm-3">
        <x-forms.input-new-line id="request_date" name="request_date" :value="$d->request_date ? get_thai_date_format($d->request_date, 'd/m/Y') : null" :label="__('purchase_orders.purchase_order_date')" />
    </div>
    <div class="col-sm-3">
        @if (Route::is('*.edit') || Route::is('*.create'))
            <x-forms.date-input id="require_date" name="require_date" :value="$d->require_date" :label="__('purchase_orders.delivery_date')" />
        @else
            <x-forms.input-new-line id="require_date" name="require_date" :value="$d->require_date ? get_thai_date_format($d->require_date, 'd/m/Y') : null" :label="__('purchase_orders.delivery_date')" />
        @endif
    </div>
    <div class="col-sm-3">
        <x-forms.input-new-line id="remark" name="remark" :value="$d->remark" :label="__('lang.remark')" />
    </div>
</div>
<div class="row push mb-5">
    <div class="col-sm-3">
        <x-forms.input-new-line id="time_of_delivery" name="time_of_delivery" :value="$d->time_of_delivery" :label="__('purchase_orders.time_of_delivery')" />
    </div>
    <div class="col-sm-3">
        <x-forms.select-option id="payment_condition"  :value="$d->payment_condition" :list="$payment_condition_list" :label="__('purchase_orders.payment')"/>
    </div>
</div>

<h4>{{ __('purchase_orders.purchase_requisition_reference') }}</h4>
<hr>
<div class="row push mb-5">
    <div class="col-sm-3">
        <x-forms.input-new-line id="purchase_requisition_no" :value="$purchase_requisition->pr_no" :label="__('purchase_orders.purchase_requisition_no')" />
    </div>
    <div class="col-sm-3">
        <x-forms.input-new-line id="purchase_requisition_date" :value="$purchase_requisition->request_date ? get_thai_date_format($purchase_requisition->request_date, 'd/m/Y') : null" :label="__('purchase_orders.purchase_requisition_date')" />
    </div>
    <div class="col-sm-3">
        <x-forms.input-new-line id="delivery_date" :value="$purchase_requisition->request_date ? get_thai_date_format($purchase_requisition->require_date, 'd/m/Y') : null"  :label="__('purchase_orders.delivery_date')" />
    </div>
    <div class="col-sm-3">
        <x-forms.input-new-line id="rental_type" :value="__('purchase_requisitions.rental_type_' . $purchase_requisition->rental_type)" :label="__('purchase_orders.car_type')" />
    </div>
    <div class="col-sm-6">
        <x-forms.input-new-line id="purchase_requisition_remark" :value="$purchase_requisition->remark" :label="__('lang.remark')" />
    </div>
</div>
