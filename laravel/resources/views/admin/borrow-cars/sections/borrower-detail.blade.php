{{-- Detail --}}
<div class="block {{ __('block.styles') }}" id="borrower" style="display: none">
    <div class="block-content">
<h4 class="borrower-topic">{{ __('borrow_cars.borrower_detail') }}</h4>
<hr class="borrower-topic">
<div class="for-employee" style="display: none">
    <div class="row push mb-4">
        <div class="col-sm-3">
            <x-forms.input-new-line id="contact_employee" :value="$d->contact ? $d->contact : get_user_name()" :label="__('borrow_cars.fullname')" />
        </div>
        <div class="col-sm-3">
            <x-forms.input-new-line id="branch" :value="$d->branch && $d->branch->name ? $d->branch->name : get_branch_name()" :label="__('transfer_cars.branch')" />
        </div>
        <div class="col-sm-3">
            <x-forms.input-new-line id="department" :value="$d->createdBy && $d->createdBy->department ? $d->createdBy->role->name : get_department_name()" :label="__('borrow_cars.department')" />
        </div>
        <div class="col-sm-3">
            <x-forms.input-new-line id="role" :value="$d->createdBy && $d->createdBy->role ? $d->createdBy->role->name : get_role_name()" :label="__('transfer_cars.role')" />
        </div>
    </div>
    <div class="row push mb-4">
        <div class="col-sm-3">
            <x-forms.input-new-line id="tel_employee" :value="$d->tel" :label="__('borrow_cars.tel')" :optionals="['required' => true]"/>
        </div>
        <div class="col-sm-3">
            <x-forms.radio-inline id="is_driver_employee" :value="$d->is_driver" :list="$need_driver_list" :label="__('transfer_cars.is_need_driver')"
                :optionals="['required' => true]" />
        </div>
    </div>
    <div class="row push mb-4" id="driver_need_employee">
        <div class="col-sm-6">
            <x-forms.input-new-line id="place_employee" :value="$d->place" :label="__('borrow_cars.place')" :optionals="['required' => true]"/>
        </div>
        <div class="col-sm-3">
            @if (Route::is('*.edit') || Route::is('*.create'))
                <x-forms.date-input id="delivery_date_employee" name="delivery_date_employee" :value="$d->delivery_date" :label="__('borrow_cars.delivery_date')"
                    :optionals="['required' => true]" />
            @else
                <x-forms.input-new-line id="delivery_date_employee" name="delivery_date_employee" :value="get_thai_date_format($d->delivery_date, 'd/m/Y')" :label="__('transfer_cars.delivery_date')" />
            @endif
        </div>
    </div>
</div>

<div class="for-other" style="display: none">
    <div class="row push mb-4">
        <div class="col-sm-3">
            <x-forms.input-new-line id="contact_other" :value="$d->contact" :label="__('borrow_cars.fullname')" :optionals="['required' => true]"/>
        </div>
        <div class="col-sm-3">
            <x-forms.input-new-line id="tel_other" :value="$d->tel" :label="__('borrow_cars.tel')" :optionals="['required' => true]"/>
        </div>
        <div class="col-sm-3">
            <x-forms.radio-inline id="is_driver_other" :value="$d->is_driver" :list="$need_driver_list" :label="__('transfer_cars.is_need_driver')"
                :optionals="['required' => true]" />
        </div>
    </div>
    <div class="row push mb-4" id="driver_need_other">
        <div class="col-sm-6">
            <x-forms.input-new-line id="place_other" :value="$d->place" :label="__('borrow_cars.place')" :optionals="['required' => true]"/>
        </div>
        <div class="col-sm-3">
            @if (Route::is('*.edit') || Route::is('*.create'))
                <x-forms.date-input id="delivery_date_other" name="delivery_date_other" :value="$d->delivery_date" :label="__('borrow_cars.delivery_date')"
                    :optionals="['required' => true]" />
            @else
                <x-forms.input-new-line id="delivery_date_other" name="delivery_date_other" :value="get_thai_date_format($d->delivery_date, 'd/m/Y')" :label="__('transfer_cars.delivery_date')" />
            @endif
        </div>
    </div>
</div>




</div>
</div>
