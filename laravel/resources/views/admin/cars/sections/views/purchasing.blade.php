<h4>{{ __('cars.po_detail') }}</h4>
<hr>
<div class="row push mb-5">
    <div class="row push mb-4">
        <div class="col-sm-3">
            <label class="text-start col-form-label">{{ __('cars.pr_no') }}</label>
            <a href="{{ route('admin.purchase-requisitions.index') }}">11283748</a>
        </div>
        <div class="col-sm-3">
            <label class="text-start col-form-label">{{ __('cars.po_no') }}</label>
            <a href="{{ route('admin.purchase-orders.index') }}">11283748</a>
        </div>
        <div class="col-sm-3">
            <x-forms.select-option :value="$d->car_type_id" id="car_type_id" :list="null" :label="__('cars.car_type')"
                :optionals="['ajax' => true]" />
        </div>
        <div class="col-sm-3">
            <x-forms.date-input id="order_date" :value="$d->order_date" :label="__('cars.order_date')" />
        </div>
    </div>

    <div class="row push mb-4">
        <div class="col-sm-3">
            <label class="text-start col-form-label">{{ __('cars.leasing_no') }}</label>
            <a href="#">11283748</a>
        </div>
        <div class="col-sm-3">
            <label class="text-start col-form-label">{{ __('cars.booking_ref') }}</label>
            <a href="{{ route('admin.purchase-orders.index') }}">11283748</a>
        </div>
        <div class="col-sm-3">
            <label class="text-start col-form-label">{{ __('cars.contact_ref') }}</label>
            <a href="{{ route('admin.purchase-orders.index') }}">11283748</a>
        </div>
        <div class="col-sm-3">
            <x-forms.date-input id="delivery_date" :value="$d->delivery_date" :label="__('cars.delivery_date')" />
        </div>
    </div>
    <div class="row push mb-4">
        <div class="col-sm-6">
            <x-forms.input-new-line id="creditor" :value="$d->creditor" :label="__('cars.customer')" />
        </div>
    </div>
</div>
