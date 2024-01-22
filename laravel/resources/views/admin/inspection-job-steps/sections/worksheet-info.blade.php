<div class="row push">
    <div class="col-sm-3">
        <x-forms.input-new-line id="work_type" :value="$form_detail->name" :label="__('inspection_cars.work_type')" />
    </div>
    <div class="col-sm-3">
        <x-forms.input-new-line id="worksheet_no" :value="$inspection_job->worksheet_no" :label="__('inspection_cars.worksheet_no')" />
    </div>
    <div class="col-sm-3">
        {{-- TODOU check if necessary --}}
        <x-forms.input-new-line id="open_worksheet" :value="$inspection_job->open_date
            ? get_thai_date_format($inspection_job->open_date, 'd/m/Y')
            : ''" :label="__('inspection_cars.open_worksheet')" />
    </div>
    <div class="col-sm-3">
        <x-forms.input-new-line id="inspection_type" :value="__('inspection_cars.transfer_' . $inspection_job->inspection_type)" :label="__('inspection_cars.inspection_type')" />
    </div>
</div>
<div class="row">
    @if ($creditor)
        <div class="col-sm-6">
            <x-forms.input-new-line id="creditor" :value="$creditor ? $creditor->creditor->name : ''" :label="__('inspection_cars.creditor')" />
        </div>
    @else
        <div class="col-sm-6">
            <x-forms.input-new-line id="customer" :value="$rental ? $rental->customer_name : ''" :label="__('inspection_cars.customer')" />
        </div>
    @endif

    @if ($inspection_job->transfer_type == STATUS_ACTIVE)
        <div class="col-sm-3">
            <x-forms.date-input id="inspection_must_date" :value="$inspection_job->inspection_must_date" :label="__('inspection_cars.in_must_date')" />
        </div>
    @else
        <div class="col-sm-3">
            <x-forms.date-input id="inspection_must_date" :value="$inspection_job->inspection_must_date" :label="__('inspection_cars.out_must_date')" />
        </div>
    @endif
</div>