@extends('admin.layouts.layout')

@section('page_title', $page_title)

@section('content')
    <form id="save-form">
        <x-blocks.block :title="__('promotions.section_title_main')" :optionals="['is_toggle' => false]">
            @if (in_array($promotion->promotion_type, [PromotionTypeEnum::COUPON, PromotionTypeEnum::PARTNER]))
                @include('admin.promotion-codes.sections.coupon')
            @endif
            @if (in_array($promotion->promotion_type, [PromotionTypeEnum::VOUCHER]))
                @include('admin.promotion-codes.sections.voucher')
            @endif
        </x-blocks.block>
        <x-blocks.block>
            <x-forms.hidden id="id" :value="$d->id" />
            <x-forms.hidden id="promotion_id" :value="$promotion_id" />
            <x-forms.hidden id="promotion_type" :value="$promotion->promotion_type" />
            <x-forms.submit-group :optionals="[
                'fullurl' => route('admin.promotion-codes.index', ['promotion_id' => $promotion_id]),
                'view' => empty($view) ? null : $view,
            ]">
            </x-forms.submit-group>
        </x-blocks.block>
    </form>
@endsection

@include('admin.components.select2-default')
@include('admin.components.sweetalert')
@include('admin.components.form-save', [
    'store_uri' => route('admin.promotion-codes.store'),
])
@include('admin.components.date-input-script')

@include('admin.components.upload-image-scripts')
@include('admin.components.upload-image', [
    'id' => 'code_file',
    'max_files' => 1,
    'accepted_files' => '.xls,.xlsx,.csv',
    // 'mock_files' => $rental_images_files,
])

@push('scripts')
    <script>
        // $view = '{{ isset($view) }}';

        // if ($view) {
        //     $('#min_total').prop('disabled', true);
        //     $('[name="incompatible[]"]').prop('disabled', true);
        // }

        var patternCodeRadio = $('input:radio[name="pattern_code"]');
        var canReuseRadio = $('input:radio[name="can_reuse"]');
        $('input[name="can_reuse"]').on("click", function() {
            var can_reuse = $('input[name="can_reuse"]:checked').val();
            if (can_reuse === '{{ BOOL_TRUE }}') {
                document.getElementById("can_reuse_yes").style.display = "block"
                document.getElementById("can_reuse_no").style.display = "none"
            } else {
                document.getElementById("can_reuse_yes").style.display = "none"
                document.getElementById("can_reuse_no").style.display = "block"
            }
            patternCodeRadio.prop('checked', false);
        });

        $('input[name="build_at"]').on("click", function() {
            var build_at = $('input[name="build_at"]:checked').val();

            if (build_at === '{{ BOOL_TRUE }}') {
                document.getElementById("build_yes").style.display = "block"
                document.getElementById("code_img").style.display = "none"
            } else {
                document.getElementById("build_yes").style.display = "none"
                document.getElementById("code_img").style.display = "block"
                document.getElementById("can_reuse_yes").style.display = "none"
                document.getElementById("can_reuse_no").style.display = "none"
                canReuseRadio.prop('checked', false);
                patternCodeRadio.prop('checked', false);
            }
        });
    </script>
@endpush
