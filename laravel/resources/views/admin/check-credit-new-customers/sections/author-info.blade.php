<div class="block {{ __('block.styles') }}">
    @include('admin.components.block-header', [
        'text' => __('check_credit.form.section_info'),
    ])
    <div class="block-content">
        <div class="row">
            <div class="col-sm-3 mb-2">
                <x-forms.label id="worksheet_no" page="view" :value="$d->worksheet_no" :label="__('check_credit.form.worksheet_no')" />
            </div>
            <div class="col-sm-3 mb-2">
                <x-forms.label id="author_name" :value="$d->createBy->name ?? Auth::user()->name" :label="__('check_credit.form.author_name')" />
            </div>
            <div class="col-sm-3 mb-2">
                <x-forms.label id="create_date" :value="get_date_time_by_format($d->created_at ?? carbon())" :label="__('check_credit.form.create_date')" />
            </div>
        </div>
    </div>
</div>
