@extends('admin.layouts.layout')
@section('page_title', $page_title . ' ' . $d->license_plate)
@section('page_title_sub')
    @if (isset($d->status))
        {!! badge_render(
            __('borrow_car_lists.status_' . $borrow_type . '_class'),
            __('borrow_car_lists.status_' . $borrow_type . '_text'),
            null,
        ) !!}
    @endif
@endsection


@push('styles')
    <style>
        .profile-image {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            overflow: hidden;
        }

        .profile-image img {
            width: 10%;
            height: 10%;
            object-fit: cover;
        }
    </style>
@endpush

@section('content')
    
    @include('admin.components.creator')
    <form id="save-form">

        @include('admin.borrow-car-lists.sections.borrow-detail')
        @include('admin.borrow-car-lists.sections.borrow-history')
        {{-- @include('admin.borrow-cars.sections.borrower-detail')
        @include('admin.borrow-cars.sections.borrow-car-detail') --}}
        <x-forms.hidden id="id" :value="$d->id" />
        <x-forms.hidden id="status" :value="$d->status" />
        @if (isset($status_confirm))
            <x-forms.hidden id="status_confirm" :value="$status_confirm" />
        @endif
    </form>

    <div class="block {{ __('block.styles') }}">
        <div class="block-content">
            <div class="row push">
                <div class="col-sm-12 text-end">
                {{-- @include('admin.borrow-cars.submit') --}}
                <a class="btn btn-outline-secondary btn-custom-size" href="{{ route($url) }}" >{{ __('lang.back') }}</a>
        </div>
    </div>
</div>
</div>
@endsection

@include('admin.components.select2-default')
@include('admin.components.sweetalert')
@include('admin.components.form-save', [
    'store_uri' => route('admin.borrow-cars.store'),
])

@include('admin.transfer-cars.scripts.update-status')
@include('admin.components.date-input-script')



@push('scripts')
    <script>
        $(".form-control").attr('disabled', true);
    </script>
@endpush
