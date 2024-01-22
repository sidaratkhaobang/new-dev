@extends('admin.layouts.layout')

@section('page_title', $page_title)

@section('content')
<x-blocks.block>
    <form id="save-form" >
        <div class="row push">
            <div class="col-sm-6">
                <x-forms.input-new-line id="name" :value="$d->name" :label="__('customer_groups.name')" :optionals="['required' => true]" />
            </div>
        </div>
        <x-forms.hidden id="id" :value="$d->id" />
        <x-forms.submit-group :optionals="['url' => 'admin.customer-groups.index', 'view' => empty($view) ? null : $view]"/>
    </form>
</x-blocks.block>
@endsection

@include('admin.components.sweetalert')
@include('admin.components.form-save', [
    'store_uri' => route('admin.customer-groups.store')
])

@push('scripts')
    <script>
        $status = '{{ isset($view) }}';
        if ($status) {
            $('#name').prop('disabled', true);
        }
    </script>
@endpush

