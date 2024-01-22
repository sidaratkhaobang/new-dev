<div class="justify-content-between">
    <form action="" method="GET" id="form-search">
        <div class="mb-4 form-group row">
            <div class="col-sm-3">
                <x-forms.select-option id="customer_type" :value="$customer_type" :list="$customer_type_list" :label="__('check_credit.index.search.customer_type')" />
            </div>
            <div class="col-sm-3">
                <x-forms.select-option id="customer_name" :value="$customer_name" :list="$customer_name_list" :label="__('check_credit.index.search.customer_name')" />
            </div>
            <div class="col-sm-3">
                <x-forms.select-option id="branch_id" :value="$branch_id" :list="$branch_list" :label="__('check_credit.index.search.branche_id')" />
            </div>
            <div class="col-sm-3">
                <x-forms.select-option id="status" :value="$status" :list="$status_list" :label="__('check_credit.index.search.status')" />
            </div>
        </div>
        @include('admin.components.btns.search')
    </form>
</div>
