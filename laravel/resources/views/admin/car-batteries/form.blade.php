@extends('admin.layouts.layout')

@section('page_title', $page_title)

@section('content')
    <div class="block {{ __('block.styles') }}">
        <div class="block-content">
            <form id="save-form">
                <div class="row push mb-4">
                    <div class="col-sm-3">
                        <x-forms.input-new-line id="name" :value="$d->name" :label="__('car_batteries.name')" :optionals="['required' => true]" />
                    </div>
                    <div class="col-sm-3">
                        <x-forms.input-new-line id="version" :value="$d->version" :label="__('car_batteries.version')" :optionals="['required' => true]" />
                    </div>
                    <div class="col-sm-6">
                        <x-forms.input-new-line id="detail" :value="$d->detail" :label="__('car_batteries.detail')" :optionals="['required' => true]" />
                    </div>
                </div>

                <div class="row push mb-4">  
                    <div class="col-sm-3">
                        <x-forms.input-new-line id="price" :value="$d->price" :label="__('car_batteries.price')" :optionals="['required' => true , 'oninput'=> true ]" />
                    </div>
                </div>

                <x-forms.hidden id="id" :value="$d->id" />
                <x-forms.submit-group :optionals="['url' => 'admin.car-batteries.index','view' => empty($view) ? null : $view]" />
            </form>
        </div>
    </div>
@endsection

@include('admin.components.select2-default')
@include('admin.components.sweetalert')
@include('admin.components.form-save', [
    'store_uri' => route('admin.car-batteries.store'),
])

@push('scripts')
<script>
    $status = '{{ isset($view) }}';
    if($status){
    $('#version').prop('disabled', true);
    $('#name').prop('disabled', true);
    $('#detail').prop('disabled', true);
    $('#price').prop('disabled', true);
    }

</script>
@endpush