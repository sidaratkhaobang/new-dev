@extends('admin.layouts.layout')

@section('page_title', $page_title)

@section('content')
    <div class="block {{ __('block.styles') }}">
        <div class="block-content">
            <form id="save-form">
                <div class="row push mb-4">
                    <div class="col-sm-3">
                        <x-forms.input-new-line id="name" :value="$d->name" :label="__('driver_wages.name')" :optionals="['required' => true]" />
                    </div>
                    <div class="col-sm-3">
                        <x-forms.select-option id="service_type_id" :value="$d->service_type_id" :list="$service_type_list" :label="__('driver_wages.service_type')" />
                    </div>
                    <div class="col-sm-3">
                        <x-forms.select-option id="driver_wage_category_id" :value="$d->driver_wage_category_id" :list="$wage_category_list" :label="__('driver_wages.wage_type')" :optionals="['required' => true]"  />
                    </div>
                    <div class="col-sm-1">
                        <x-forms.input-new-line id="seq" :value="$d->seq" :label="__('driver_wages.seq')" :optionals="['required' => true,'type' => 'number','min' => 0]" />
                    </div>
                    <div class="col-sm-2">
                        <x-forms.radio-inline id="is_standard" :value="$d->is_standard" :list="$listType" :label="__('driver_wages.type')" :optionals="['required' => true]" />
                    </div>
                </div>
                <div class="row push mb-4">
                    <div class="col-sm-3">
                        <x-forms.radio-inline id="wage_cal_type" :value="$d->wage_cal_type" :list="$listWageType" :label="__('driver_wages.wage_type_cal')" :optionals="['required' => true,'input_class' => ' input-pd' ]" />
                    </div>
                    <div class="col-sm-3">
                        <x-forms.radio-inline id="wage_cal_day" :value="$d->wage_cal_day" :list="$listWageDay" :label="__('driver_wages.wage_day_cal')" :optionals="['required' => true ,'input_class' => 'input-pd']" />
                    </div>
                    <div class="col-sm-4">
                        <x-forms.radio-inline id="wage_cal_time" :value="$d->wage_cal_time" :list="$listWageTime" :label="__('driver_wages.wage_time_cal')" :optionals="['required' => true ,'input_class' => 'input-pd']" />
                    </div>
                </div>
                <div class="row push mb-4">
                    <div class="col-sm-4">
                        <x-forms.radio-inline id="status" :value="$d->status" :list="$listStatus" :label="__('driver_wage_categories.status')" :optionals="['required' => true ,'input_class' => 'col-sm-6 input-pd']" />
                    </div>
                </div>
                <div class="row push mb-4">
                    <div class="col-sm-4">
                        <x-forms.radio-inline id="is_special_wage" :value="$d->is_special_wage" :list="$listSpecialWageStatus" :label="__('driver_wages.special_wage')" :optionals="['required' => true ,'input_class' => 'col-sm-6 input-pd']" />
                    </div>
                </div>
                <div class="row push col-md-6 mb-4" id="display_wage_list" style="display: none">
                    <x-forms.select-option id="wage_list[]" :value="$wage" :list="$wage_list" :label="__('driver_wages.special_wage_cal')" :optionals="['multiple' => true]" />
                </div>
                <x-forms.hidden id="id" :value="$d->id" />
                <x-forms.submit-group  :optionals="['url' => 'admin.driver-wages.index','view' => empty($view) ? null : $view]"/>
                {{-- @endif --}}
            </form>
        </div>
    </div>
@endsection

@include('admin.components.select2-default')
@include('admin.components.sweetalert')
@include('admin.components.form-save', [
    'store_uri' => route('admin.driver-wages.store'),
])

@push('scripts')
<script>
        $('.form-control').prop('disabled' , {{ Route::is('*.show') }});
        $('.form-check-input').prop('disabled' , {{ Route::is('*.show') }});
        $("input[type=radio]").attr('disabled', {{ Route::is('*.show') }});
        $('[name="wage_list[]"]').attr('disabled', {{ Route::is('*.show') }});

        validateDisplayInputWageList()

        $("input[name='is_special_wage']").on('change',function(){
            validateDisplayInputWageList()
        });

        function validateDisplayInputWageList() {
            const check_val = $("input[name='is_special_wage']:checked").val();
            if(check_val === '{{STATUS_ACTIVE}}') {
                $('#display_wage_list').show();
            } else {
                $('#display_wage_list').hide();
                $('[name="wage_list[]"]').val([]).trigger('change');
            }
        }

        @if(Route::is('*.show'))
        $('input').removeAttr('name');
        @endif
</script>
@endpush
