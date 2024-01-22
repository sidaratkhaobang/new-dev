@extends('admin.layouts.layout')

@section('page_title', $page_title)

@section('content')
    <div class="block {{ __('block.styles') }}">
        <div class="block-content">
            <form id="save-form">
                @include('admin.purchase-orders.sections.views.dealers')
                @include('admin.long-term-rental-compare-price.sections.selected-dealer')
                <x-forms.hidden id="id" name="id" :value="$d->id" />

                <div class="row push">
                    <div class="text-end">     
                        <a class="btn btn-secondary" href="{{ route('admin.long-term-rental.compare-price-approve.index') }}" >{{ __('lang.back') }}</a>
                        @if ($d->comparison_price_status == \App\Enums\ComparisonPriceStatusEnum::PENDING_REVIEW)
                            <a class="btn btn-danger btn-update-compare-price-status" 
                                data-status='{{ \App\Enums\ComparisonPriceStatusEnum::REJECT }}'>
                                {{ __('lang.disapprove') }}
                            </a>
                            <a class="btn btn-primary btn-update-compare-price-status" 
                                data-status='{{ \App\Enums\ComparisonPriceStatusEnum::CONFIRM }}'>
                                {{ __('lang.approve') }}
                            </a>
                        @endif           
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection
@include('admin.components.select2-default')
@include('admin.components.sweetalert')
@include('admin.components.list-delete')
@include('admin.components.update-status', [
    'route' => route('admin.long-term-rental.compare-price-approve.update-compare-rental-status'),
])

@push('scripts')
    <script>
        var selected_creditor = @json($selected_dealer);
        if (selected_creditor) {
            var defaultDealerOption = {
                id: selected_creditor.id,
                text: selected_creditor.name,
            };
            var tempDealerOption = new Option(defaultDealerOption.text, defaultDealerOption.id, true, true);
            $("#ordered_creditor_id").append(tempDealerOption).trigger('change');
        }
        $("#ordered_creditor_id").prop('disabled', true);

        $(".btn-update-compare-price-status").on("click", function () {
            var id = $('#id').val();
            var status = $(this).attr('data-status');
            var redirect_route = '{{ route("admin.long-term-rental.compare-price-approve.index") }}';
            var data = {
                compare_price_status: status,
                rental_id: id,
                redirect_route: redirect_route
            };
            mySwal.fire({
                title:  "{{ __('lang.update_confirm') }}",
                text: "{{ __('lang.update_confirm_question') }}",
                icon: 'warning',
                showCancelButton: true,
                customClass: {
                    confirmButton: 'btn btn-danger m-1',
                    cancelButton: 'btn btn-secondary m-1'
                },
                confirmButtonText: "{{ __('lang.ok') }}",
                cancelButtonText: "{{ __('lang.cancel') }}",
                html: false,
                preConfirm: e => {
                    return new Promise(resolve => {
                        setTimeout(() => {
                            resolve();
                        }, 50);
                    });
                }
            }).then(result => {
                if (result.value) {
                    updateDefaultStatus(data);
                }
            });
        });
    </script>
@endpush
