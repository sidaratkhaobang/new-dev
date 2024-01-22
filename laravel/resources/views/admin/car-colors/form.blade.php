@extends('admin.layouts.layout')

@section('page_title', $page_title)

@section('content')
<div class="block {{ __('block.styles') }}">
    <div class="block-content">
        <form id="save-form" >
            <div class="row push mb-4">
                <div class="col-sm-2"></div>
                <div class="col-sm-4">
                    <x-forms.input-new-line id="code" :value="$d->code" :label="__('car_colors.code')" :optionals="['required' => true, 'maxlength' => 10]" />
                </div>
            </div>
            <div class="row push mb-4">
                <div class="col-sm-2"></div>
                <div class="col-sm-4">
                    <x-forms.input-new-line id="name" :value="$d->name" :label="__('car_colors.name')" :optionals="['required' => true], 'maxlength' => 100" />
                </div>
            </div>
            <x-forms.hidden id="id" :value="$d->id" />
            <x-forms.submit-group :optionals="['url' => 'admin.car-colors.index','view' => empty($view) ? null : $view ]"/>
        </form>
    </div>
</div>
@endsection

@include('admin.components.sweetalert')
@include('admin.components.form-save', [
    'store_uri' => route('admin.car-colors.store')
])

@push('scripts')
<script>
    $status = '{{ isset($view) }}';
    if($status){
    $('#code').prop('disabled', true);
    $('#name').prop('disabled', true);
    }

</script>
@endpush
