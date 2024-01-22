@extends('admin.layouts.layout')

@section('page_title', $page_title)

@section('content')
<div class="block {{ __('block.styles') }}">
    <div class="block-content">
        <form id="save-form" >
            <div class="row push mb-4">
                <div class="col-sm-6">
                    <x-forms.input-new-line id="name" :value="$d->name" :label="__('sections.name')" :optionals="['required' => true]" />
                </div>
                <div class="col-sm-6">
                    <x-forms.select-option id="department_id" :value="$d->department_id" :list="$user_department_lists"
                        :label="__('sections.department_name')" :optionals="[
                            'required' => true
                        ]" />
                </div>
            </div>
            <x-forms.hidden id="id" :value="$d->id" />
            <x-forms.submit-group :optionals="['url' => 'admin.sections.index' ,'view' => empty($view) ? null : $view]"/>
        </form>
    </div>
</div>
@endsection

@include('admin.components.sweetalert')
@include('admin.components.form-save', [
    'store_uri' => route('admin.sections.store')
])


@push('scripts')
<script>
    $status = '{{ isset($view) }}';
    if($status){
        $('#name').prop('disabled', true);
        $('#department_id').prop('disabled', true);
    }

</script>
@endpush
