@extends('admin.layouts.layout')

@section('page_title', $page_title)

@section('content')
    <div class="block {{ __('block.styles') }}">
        <div class="block-content">
            <form id="save-form">
                <div class="row push mb-4">
                    <div class="col-sm-4">
                        <x-forms.input-new-line id="version" :value="$d->version" :label="__('pdpas.version')" :optionals="['required' => true, 'maxlength' => 10]" />
                    </div>
                </div>
                <div class="row push mb-4">
                    <div class="col-sm-12">
                        <x-forms.text-area-new-line id="description_th" :value="$d->description_th" :label="__('pdpas.description_th')" :optionals="['required' => true]" />
                    </div>           
                </div>
                <div class="row push mb-4">
                    <div class="col-sm-12">
                        <x-forms.text-area-new-line id="description_en" :value="$d->description_en" :label="__('pdpas.description_en')" :optionals="['required' => true]" />
                    </div>           
                </div>
                <div class="row push mb-4">
                    <div class="col-sm-4">
                        <x-forms.select-option id="consent_type" :value="$d->consent_type" :list="$consent_type_list" :label="__('pdpas.consent_type')" :optionals="['required' => true]" />
                    </div>
                </div>
                <x-forms.hidden id="id" :value="$d->id" />
   
                <x-forms.submit-group  :optionals="['url' => 'admin.pdpa-managements.index','view' => empty($view) ? null : $view]" 
                    
                />
                {{-- @endif --}}
            </form>
        </div>
    </div>
@endsection

@include('admin.components.select2-default')
@include('admin.components.sweetalert')
@include('admin.components.form-save', [
    'store_uri' => route('admin.pdpa-managements.store'),
])

@push('scripts')
<script>
    $status = '{{ isset($view) }}';
    if($status){
    $('#version').prop('disabled', true);
    $('#description_th').prop('disabled', true);
    $('#description_en').prop('disabled', true);
    $('#consent_type').prop('disabled', true);
    }

</script>
@endpush