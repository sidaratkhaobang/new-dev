@extends('admin.layouts.layout')

@section('page_title', $page_title)

@push('custom_styles')
    <style>
        .btn-img {
            background: #FFFFFF;
            border: solid #FFFFFF;
            box-shadow: 0px 4px 10px rgba(163, 163, 163, 0.25);
            border-radius: 0.5em;
        }

        p.container {
            text-align: center;
            padding-top: 15px;
        }

        .btn-img:hover {
            border: solid #a4c1e2;
            box-shadow: 0px 4px 10px rgba(163, 163, 163, 0.25);
            border-radius: 0.5em;
        }

        .btn-img-active {
            border: solid #157CF2;
            box-shadow: 0px 4px 10px rgba(163, 163, 163, 0.25);
            border-radius: 0.5em;
        }
    </style>
@endpush

@section('content')
<x-blocks.block>
    <form id="save-form">
        <h4>{{ __('promotions.promotion_type') }}</h4>
        <hr>
        <br>
        <div class="row justify-content-center">
            @foreach ($promotion_types as $promotion_type)
                <div class="col-4 mb-4">
                    <div class="btn-img" onclick="getPromotionType('{{ $promotion_type['id'] }}')">
                        <a id="promotion_type">
                            <p class="container">{{ $promotion_type['name'] }}</p>
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
        <x-forms.hidden id="promotion_type_id" :value="null" />
        <div class="row">
            <div class="col-sm-12 text-end">
                <a class="btn btn-danger" href="{{ route('admin.promotions.index') }}">{{ __('lang.cancel') }}</a>
                <button type="button" class="btn btn-primary"
                    onclick="getPromotion()">{{ __('lang.next') }}</button>
            </div>
        </div>
    </form>
</x-blocks.block>
@endsection

@include('admin.components.sweetalert')
{{-- @include('admin.components.form-save', [
    'store_uri' => route('admin.short-term-rental-service-types.store'),
]) --}}

@push('scripts')
    <script>
        $(document).ready(function() {
            $('.btn-img').click(function() {
                if ($('.btn-img-active').length) {
                    $('.btn-img-active').not($(this)).removeClass('btn-img-active').addClass('btn-img');
                }
                $(this).removeClass('btn-img').addClass('btn-img-active');
            });
        });

        function getPromotionType(promotion_type) {
            document.getElementById("promotion_type_id").value = promotion_type;
        }

        function getPromotion() {
            let storeUri = "{{ route('admin.promotions.create-promotion') }}";
            var formData = new FormData(document.querySelector('#save-form'));
            axios.post(storeUri, formData).then(response => {
                if (response.data.success) {
                    window.location.href = response.data.redirect;
                } else {
                    //
                }
            }).catch(error => {
                mySwal.fire({
                    title: "{{ __('lang.store_error_title') }}",
                    text: error.response.data.message,
                    icon: 'error',
                    confirmButtonText: "{{ __('lang.ok') }}",
                }).then(value => {
                    if (value) {
                        //
                    }
                });
            });
        }
    </script>
@endpush
