{{-- Purchaser --}}
<h4>{{ __('purchase_requisitions.data_purchaser_table') }}</h4>
<hr>
<div class="row push mb-4">
    <div class="col-sm-3">
        <x-forms.input-new-line id="purchaser" :value="$d->createdBy ? $d->createdBy->name : get_user_name()" :label="__('purchase_requisitions.purchaser')" />
    </div>
    <div class="col-sm-3">
        <x-forms.input-new-line id="department" :value="$d->createdBy && $d->createdBy->department ? $d->createdBy->department->name : get_department_name()" :label="__('purchase_requisitions.department')" />
    </div>
</div>
{{-- end Purchaser --}}
<br>
@if (in_array($d->status, [PRStatusEnum::CONFIRM, PRStatusEnum::REJECT, PRStatusEnum::CANCEL]))
    @include('admin.purchase-requisitions.views.pr-status')
@endif
{{-- PR --}}
<h4>{{ __('purchase_requisitions.data_pr_table') }}</h4>
<hr>
<div class="row push mb-4">
    <div class="col-sm-3">
        <x-forms.input-new-line id="pr_no" :value="$d->pr_no" :label="__('purchase_requisitions.pr_no')" />
    </div>
    <div class="col-sm-3">
        <x-forms.input-new-line id="request_date" name="request_date" :value="$d->request_date" :label="__('purchase_requisitions.request_date')" />
    </div>
    <div class="col-sm-3">
        <x-forms.select-option :value="$d->parent_id" id="parent_id" :list="null" :label="__('purchase_requisitions.pr_parent')"
            :optionals="[
                'ajax' => true,
                'default_option_label' => $parent_no,
            ]" />
    </div>
    <div class="col-sm-3">
        @if (Route::is('*.edit') || Route::is('*.create'))
            <x-forms.date-input id="require_date" name="require_date" :value="$d->require_date" :label="__('purchase_requisitions.require_date')"
                :optionals="['required' => true]" />
        @else
            <x-forms.input-new-line id="require_date" name="require_date" :value="$d->require_date" :label="__('purchase_requisitions.require_date')" />
        @endif
    </div>
</div>
<div class="row push mb-4">
    <div class="col-sm-3">
        @if (Route::is('*.edit') || Route::is('*.create'))
            <x-forms.select-option id="rental_type" :value="$d->rental_type" :list="$rental_type_list" :label="__('purchase_requisitions.rental_type')"
                :optionals="['required' => true]" />
        @else
            <x-forms.select-option id="rental_type" name="rental_type" :value="$d->rental_type" :list="$rental_type_list"
                :label="__('purchase_requisitions.rental_type')" />
        @endif

    </div>
    <div class="col-sm-6">
        <x-forms.input-new-line id="remark" :value="$d->remark" :label="__('purchase_requisitions.remark')" />
    </div>
</div>
{{-- end PR --}}
<br>
{{-- Rental --}}
<div class="row" id="rental_select"
    @if (in_array($d->rental_type, [RentalTypeEnum::SHORT, RentalTypeEnum::LONG])) style="display: block"
    @else style="display: none" @endif>
    <div class="col-12">
        <h4>{{ __('purchase_requisitions.data_rental_table') }}</h4>
        <hr>
        <div class="row push mb-4">
            <div class="col-sm-3">
                <x-forms.select-option id="reference_id" :value="$d->reference ? $d->reference->id : null" :list="null" :label="__('purchase_requisitions.rental_no')"
                    :optionals="[
                        'ajax' => true,
                        'default_option_label' => $reference_name,
                    ]" />
            </div>
            <div class="col-sm-3" id="short_rental_1"
                @if ($d->rental_type != \App\Enums\RentalTypeEnum::LONG) style="display: block" @else style="display: none" @endif>
                <x-forms.input-new-line id="rental_refer" :value="$d->rental_refer" :label="__('purchase_requisitions.rental_refer')" />
            </div>
            <div class="col-sm-3" id="short_rental_2"
                @if ($d->rental_type != \App\Enums\RentalTypeEnum::LONG) style="display: block" @else style="display: none" @endif>
                <x-forms.input-new-line id="contract_refer" :value="$d->contract_refer" :label="__('purchase_requisitions.contract_refer')" />
            </div>

            <div class="col-sm-3" id="long_rental_1"
                @if ($d->rental_type === \App\Enums\RentalTypeEnum::LONG) style="display: block" @else style="display: none" @endif>
                <x-forms.input-new-line id="job_type" :value="$d->reference ? __('long_term_rentals.job_type_' . $d->reference->job_type) : null" :label="__('long_term_rentals.job_type')" />
            </div>
            <div class="col-sm-3" id="long_rental_2"
                @if ($d->rental_type === \App\Enums\RentalTypeEnum::LONG) style="display: block" @else style="display: none" @endif>
                <x-forms.input-new-line id="rental_duration" :value="$d->reference ? $d->reference->rental_duration : null" :label="__('long_term_rentals.rental_duration')" />
            </div>
            <div class="col-sm-3">
                <x-forms.input-new-line id="customer_type" :value="$d->customer_type ? __('customers.type_' . $d->customer_type) : null" :label="__('purchase_requisitions.customer_type')" />
            </div>
        </div>
        <div class="row push mb-4">
            <div class="col-sm-3">
                <x-forms.input-new-line id="customer_name" :value="$d->reference ? $d->reference->customer_name : null" :label="__('purchase_requisitions.customer')" />
            </div>
            <div class="col-sm-3" id="short_rental_3"
                @if ($d->rental_type === \App\Enums\RentalTypeEnum::SHORT) style="display: block" @else style="display: none" @endif>
                <x-forms.input-new-line id="approve_ref_no" :value="$d->approve_refer" :label="__('purchase_requisitions.approve_ref_no')" />
            </div>
            <div class="col-sm-3" id="short_rental_4"
                @if (in_array($d->rental_type,[RentalTypeEnum::SHORT])) style="display: block" @else style="display: none" @endif>
                @if (Route::is('*.show'))
                    <x-forms.view-image :id="'approve_images'" :label="__('purchase_requisitions.approve_file')" :list="$approve_images_files" />
                @else
                    <x-forms.upload-image :id="'approve_images'" :label="__('purchase_requisitions.approve_file')" />
                @endif
            </div>
        </div>
    </div>
</div>
{{-- end Rental --}}

{{-- Replacement Section --}}
<div class="row" id="replacement_section" 
    @if (in_array($d->rental_type, [RentalTypeEnum::REPLACEMENT])) style="display: block"
    @else style="display: none" @endif>
    <div class="col-12">
        <h4>{{ __('purchase_requisitions.replacement_section') }}</h4>
        <hr>
        <div class="row push mb-4">
            <div class="col-sm-3">
                <x-forms.input-new-line id="replacement_ref_no" :value="$d->approve_refer" :label="__('purchase_requisitions.approve_ref_no')" />
            </div>
            <div class="col-sm-3">
                @if (Route::is('*.show'))
                    <x-forms.view-image :id="'replacement_approve_files'" :label="__('purchase_requisitions.approve_file')" :list="$replacement_approve_files" />
                @else
                    <x-forms.upload-image :id="'replacement_approve_files'" :label="__('purchase_requisitions.approve_file')" />
                @endif
            </div>
        </div>
    </div>
</div>