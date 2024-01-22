<form action="" method="GET" id="form-search">
    <div class="form-group row push">
        <div class="col-sm-4">
            <x-forms.input-new-line id="s" :value="$s" :label="__('lang.search_label')" :optionals="['placeholder' => __('lang.search_placeholder')]" />
        </div>
    </div>
    @include('admin.components.btns.search')
</form>
