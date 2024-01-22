<div class="row push mb-1">
    <div class="col-sm-3 mt-2">
        <h4>{{ __('purchase_orders.purchaser_detail') }}</h4>
    </div>
    @auth
        @if (empty($view))
            @can(Actions::Manage . '_' . Resources::ImportCar)
                <div class="col-sm-9">
                    <div class="text-end"><button type="button" onclick="openShareDealerModal()" class="btn btn-primary"><i
                                class="fa fa-arrow-up-from-bracket"></i>&nbsp; {{ __('import_cars.share_with_dealer') }}</button>
                    </div>
                    @include('admin.import-cars.modals.import-share-dealer')
                </div>
            @endcan
        @endif
    @endauth
</div>
<hr>
<div class="row push mb-5">
    <div class="col-sm-3">
        <x-forms.input-new-line id="requester_name" :value="$pr_detail && $pr_detail->createdBy && $pr_detail->createdBy->name ? $pr_detail->createdBy->name : null" :label="__('purchase_orders.requester_name')" />
    </div>
    <div class="col-sm-3">
        <x-forms.input-new-line id="requester_department" :value="$pr_detail && $pr_detail->createdBy && $pr_detail->createdBy->department && $pr_detail->createdBy->department->name 
        ? $pr_detail->createdBy->department->name 
        : null" :label="__('import_cars.requester_department')" />
    </div>
</div>

<h4>{{ __('import_cars.approver_detail') }}</h4>
<hr>
<div class="row push mb-5">
    <div class="col-sm-3">
        <x-forms.input-new-line id="approver" :value="$d->reviewer ? $d->reviewer->name : null" :label="__('import_cars.approver')" />
    </div>
    <div class="col-sm-3">
        <x-forms.input-new-line id="approve_department" :value="$d->reviewer && $d->reviewer->department ? $d->reviewer->department->name : null" :label="__('import_cars.approve_department')" />
    </div>
    <div class="col-sm-3">
        <x-forms.input-new-line id="approve_date" :value="$d->reviewed_at ? get_thai_date_format($d->reviewed_at, 'd/m/Y') : '-'" :label="__('import_cars.approve_date')" />
    </div>
</div>

<h4>{{ __('purchase_orders.purchase_order_detail') }}</h4>
<hr>
<div class="row push mb-5">
    <div class="col-sm-3">
        <x-forms.input-new-line id="purchase_order_no" :value="$d->po_no" :label="__('purchase_orders.purchase_order_no')" />
    </div>
    <div class="col-sm-3">
        <x-forms.input-new-line id="purchase_order_date" :value="$d->request_date ? get_thai_date_format($d->request_date, 'd/m/Y') : null" :label="__('purchase_orders.purchase_order_date')" />
    </div>

    <div class="col-sm-3">
        <x-forms.input-new-line id="need_date" :value="$d->require_date ? get_thai_date_format($d->require_date, 'd/m/Y') : null" :label="__('import_cars.need_date')" />
    </div>
    <div class="col-sm-3">
        <x-forms.input-new-line id="remark" :value="$d->po_remark" :label="__('lang.remark')" />
    </div>
</div>
<div class="row push mb-5">
    <div class="col-sm-9">
        <x-forms.input-new-line id="creditor_id" :value="$d->creditor ? $d->creditor->name : null" :label="__('purchase_orders.dealer')" />
    </div>
    <div class="col-sm-3">
        <x-forms.date-input id="request_dealer_date" :value="$d->request_dealer_date" :label="__('import_cars.request_dealer_date')" />
    </div>
</div>
