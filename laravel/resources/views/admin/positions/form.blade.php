@extends('admin.layouts.layout')

@section('page_title', $page_title)

@section('content')
<x-blocks.block>
    <form id="save-form">
        <div class="row push">
            <div class="col-sm-4">
                <x-forms.input-new-line id="name" :value="$d->name" :label="__('positions.name')" :optionals="['required' => true]" />
            </div>
            <div class="col-sm-1"></div>
            <div class="col-sm-4">
                <x-forms.radio-inline id="status" :value="$d->status" :list="$listStatus" :label="__('lang.status')" :optionals="['required' => true ,'input_class' => 'col-sm-6 input-pd']" />
            </div>
        </div>
        <x-forms.hidden id="id" :value="$d->id" />
        <x-forms.submit-group  :optionals="['url' => 'admin.positions.index','view' => empty($view) ? null : $view]" />
    </form>
</x-blocks.block>
@endsection

@include('admin.components.select2-default')
@include('admin.components.sweetalert')
@include('admin.components.form-save', [
    'store_uri' => route('admin.positions.store'),
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