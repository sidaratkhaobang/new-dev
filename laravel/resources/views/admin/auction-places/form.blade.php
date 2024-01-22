@extends('admin.layouts.layout')

@section('page_title', $page_title)

@section('content')
    <div class="block {{ __('block.styles') }}">
        @include('admin.components.block-header', [
            'text' => __('auction_places.detail'),
            'block_icon_class' => 'icon-document',
        ])
        <div class="block-content">
            <form id="save-form">
                <div class="row push mb-4">
                    <div class="col-sm-3">
                        <x-forms.input-new-line id="name" :value="$d->name" :label="__('auction_places.name')" :optionals="['required' => true]" />
                    </div>
                    <div class="col-sm-9">
                        <x-forms.input-new-line id="address" :value="$d->address" :label="__('auction_places.address')" :optionals="['maxlength' => 1000]" />
                    </div>
                </div>
                <div class="row push mb-4">
                    <div class="col-sm-3">
                        <x-forms.input-new-line id="contact_name" :value="$d->contact_name" :label="__('auction_places.contact_name')" />
                    </div>
                    <div class="col-sm-3">
                        <x-forms.input-new-line id="contact_tel" :value="$d->contact_tel" :label="__('auction_places.contact_tel')" />
                    </div>
                    <div class="col-sm-3">
                        <x-forms.input-new-line id="contact_email" :value="$d->contact_email" :label="__('auction_places.contact_email')" />
                    </div>
                    <div class="col-sm-3">
                        <x-forms.input-new-line id="remark" :value="$d->remark" :label="__('auction_places.remark')" />
                    </div>
                </div>
                <div class="row push mb-4">
                    <div class="col-sm-6">
                        <x-forms.radio-inline id="status" :value="$d->status" :list="$status_list" :label="__('lang.status')" />
                    </div>
                </div>

                <x-forms.hidden id="id" :value="$d->id" />
                <x-forms.submit-group :optionals="[
                    'url' => 'admin.auction-places.index',
                    'view' => empty($view) ? null : $view,
                    'manage_permission' => Actions::Manage . '_' . Resources::AuctionPlace,
                ]" />
            </form>
        </div>
    </div>
@endsection

@include('admin.components.sweetalert')
@include('admin.components.form-save', [
    'store_uri' => route('admin.auction-places.store'),
])

@push('scripts')
    <script>
        $view = '{{ isset($view) }}';
        if ($view) {
            $('.form-control').prop('disabled', true);
            $("input[type='radio']").attr('disabled', true);
        }
    </script>
@endpush
