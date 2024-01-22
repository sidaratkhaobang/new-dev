@extends('admin.layouts.layout')

@section('page_title', $page_title)

@section('content')
    <div class="block {{ __('block.styles') }}">
        <div class="block-content">
            <form id="save-form">
                <div class="row push mb-4">           
                    <div class="col-sm-4">
                        <x-forms.input-new-line id="name" :value="$d->name" :label="__('general_ledger_accounts.name')" :optionals="['required' => true]" />
                    </div>
                    <div class="col-sm-4">
                        <x-forms.input-new-line id="account" :value="$d->account" :label="__('general_ledger_accounts.account')" :optionals="['required' => true]" />
                    </div>
                    {{-- <div class="col-sm-4">
                        <x-forms.select-option id="branch_id" :value="$d->branch_id" :list="$branch_list" :label="__('general_ledger_accounts.branch')" />
                    </div> --}}
                    <div class="col-sm-4">
                        <x-forms.select-option id="customer_group[]" :value="$customer_group" :list="$customer_group_list" :label="__('general_ledger_accounts.customer_group')"
                            :optionals="['multiple' => true]" />
                    </div>
                </div>
                <div class="row push mb-4">
                   
                    <div class="col-sm-4">
                        <x-forms.text-area-new-line id="description" :value="$d->description" :label="__('general_ledger_accounts.description')" />
                    </div>
                </div>
                <x-forms.hidden id="id" :value="$d->id" />
                <x-forms.submit-group :optionals="[
                    'url' => 'admin.general-ledger-accounts.index', 
                    'view' => empty($view) ? null : $view,
                    'manage_permission' => Actions::Manage . '_' . Resources::GlAccouunt
                    ]" />
            </form>
        </div>
    </div>
@endsection

@include('admin.components.select2-default')
@include('admin.components.sweetalert')
@include('admin.components.form-save', [
    'store_uri' => route('admin.general-ledger-accounts.store'),
])

@push('scripts')
    <script>
        $status = '{{ isset($view) }}';
        if ($status) {
            $('#name').prop('disabled', true);
            $('#type').prop('disabled', true);
            $('#branch_id').prop('disabled', true);
            $('#account').prop('disabled', true);
            $('#description').prop('disabled', true);
            $('[name="customer_group[]"]').prop('disabled', true);
        }
    </script>
@endpush
