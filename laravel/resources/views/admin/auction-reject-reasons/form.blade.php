@extends('admin.layouts.layout')

@section('page_title', $page_title)

@section('content')
    <div class="block {{ __('block.styles') }}">
        <div class="block-content">
            <form id="save-form">
                <div class="row push mb-4">
                    <div class="col-sm-6">
                        <x-forms.input-new-line id="name" :value="$d->name" :label="__('auction_reject_reasons.name')" :optionals="['required' => true], 'maxlength' => 100" />
                    </div>
                </div>
                <x-forms.hidden id="id" :value="$d->id" />
                <x-forms.submit-group :optionals="[
                    'url' => 'admin.auction-reject-reasons.index', 
                    'view' => empty($view) ? null : $view,
                    'manage_permission' => Actions::Manage . '_' . Resources::AuctionRejectReason
                    ]" />
            </form>
        </div>
    </div>
@endsection

@include('admin.components.sweetalert')
@include('admin.components.form-save', [
    'store_uri' => route('admin.auction-reject-reasons.store'),
])

@push('scripts')
    <script>
        $status = '{{ isset($view) }}';
        if ($status) {
            $('#name').prop('disabled', true);
        }
    </script>
@endpush
