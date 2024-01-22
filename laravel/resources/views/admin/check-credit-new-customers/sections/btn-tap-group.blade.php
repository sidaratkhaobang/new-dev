<div class="row items-push mb-4">
    <div class="col-sm-8">
        <div class="btn-group" role="group">
            <a type="button" href="{{ route('admin.check-credit-new-customers.index') }}"
               class="btn btn-outline-primary btn-custom-size {{ in_array(Route::currentRouteName(), ['admin.check-credit-new-customers.index']) ? 'active' : '' }}">
                {{ __('ตรวจสอบเครดิต') }}
            </a>
            <a type="button" href="#"
               class="btn btn-outline-primary btn-custom-size {{ in_array(Route::currentRouteName(), ['']) ? 'active' : '' }}">
                {{ __('สัญญาทั้งหมด') }}
            </a>
        </div>
    </div>
</div>
