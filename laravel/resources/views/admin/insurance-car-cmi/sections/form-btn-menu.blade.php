@push('styles')
    <style>
        .active{
            color: white !important;
        }
    </style>
@endpush
<div class="row items-push mb-4 ">
    <div class="col-sm-8">
        <div class="btn-group bg-white" role="group">
                <a type="button"
                   href="{{$route_edit_redirect?? route('admin.insurance-car.index')}}"
                   class="btn btn-outline-primary
                    {{ in_array(Route::currentRouteName(), ['admin.insurance-car-cmi.edit']) ? 'active' : '' }}"
                   style="color: black;">
                    {{ __('insurance_car.cmi_info') }}
                </a>
                <a type="button"
                   href="{{$route_remark_redirect??route('admin.insurance-car.index')}}"
                   class="btn btn-outline-primary
                    {{ in_array(Route::currentRouteName(), ['admin.insurance-car-cmi.remark']) ? 'active' : '' }}"
                   style="color: black;"
                >
                    {{ __('insurance_car.coverage_info') }}
                </a>
        </div>
    </div>
</div>
