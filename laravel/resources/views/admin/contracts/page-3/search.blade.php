<div class="block-header">
    <h4><i class="fa fa-file-lines"></i> {{ __('ค้นหา') }}</h4>
</div>
<div class="justify-content-between mb-4">
    <form action="" method="GET" id="form-search">
        <div class="mb-4 form-group row">
            <div class="col-sm-3">
                <x-forms.select-option id="customer_type" :value="null" :list="null" :label="__('ทะเบียนรถ')" />
            </div>
            <div class="col-sm-3">
                <x-forms.select-option id="customer_name" :value="null" :list="null" :label="__('หมายเลขตัวถัง')" />
            </div>
            <div class="col-sm-3">
                <x-forms.select-option id="branch_id" :value="null" :list="null" :label="__('เลขเครื่องยนต์')" />
            </div>
            <div class="col-sm-3">
                <x-forms.select-option id="status" :value="null" :list="null" :label="__('รุ่น')" />
            </div>
        </div>
    </form>
</div>


