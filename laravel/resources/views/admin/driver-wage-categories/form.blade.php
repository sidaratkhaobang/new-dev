@extends('admin.layouts.layout')

@section('page_title', $page_title)

@section('content')
    <div class="block {{ __('block.styles') }}">
        <div class="block-content">
            <form id="save-form">
                <div class="row push mb-4">
                    <div class="col-sm-4">
                        <x-forms.input-new-line id="name" :value="$d->name" :label="__('driver_wage_categories.name')" :optionals="['required' => true]" />
                    </div>
                    <div class="col-sm-1"></div>
                    <div class="col-sm-4">
                        <x-forms.radio-inline id="status" :value="$d->status" :list="$listStatus" :label="__('driver_wage_categories.status')" :optionals="['required' => true ,'input_class' => 'col-sm-6 input-pd']" />
                    </div>
                </div>             
                <x-forms.hidden id="id" :value="$d->id" />
   
                <x-forms.submit-group  :optionals="['url' => 'admin.driver-wage-categories.index','view' => empty($view) ? null : $view]" 
                    
                />
                {{-- @endif --}}
            </form>
        </div>
    </div>
@endsection

@include('admin.components.select2-default')
@include('admin.components.sweetalert')
@include('admin.components.form-save', [
    'store_uri' => route('admin.driver-wage-categories.store'),
])

@push('scripts')
<script>
    $status = '{{ isset($view) }}';
    if($status){
    $('#name').prop('disabled', true);
    $("input[type=radio]").attr('disabled', true);
    }

</script>
@endpush