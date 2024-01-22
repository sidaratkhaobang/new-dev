@extends('admin.layouts.layout')
@section('page_title',__('request_premium.page_title'))
@section('content')
            @include('admin.request-premium.sections.index-search')
            @include('admin.request-premium.sections.index-table')
@endsection
@include('admin.components.select2-default')
@include('admin.components.sweetalert')
@include('admin.components.list-delete')
@include('admin.components.date-input-script')
@include('admin.components.select2-ajax', [
    'id' => 'customer_id',
    'parent_id' => 'customer_type',
    'url' => route('admin.util.select2-customer.customer-codes'),
])
@push('scripts')
    <script>
        eventSelect = $('#customer_id');
        eventSelect.on('change', function (e) {
            clearCustomerDetail();
        });

        eventSelect.on('select2:select', function (e) {
            var data = e.params.data;
            axios.get("{{ route('admin.util.select2-customer.customer-detail') }}", {
                params: {
                    customer_id: data.id
                }
            }).then(response => {
                if (response.data.success) {
                    addCustomerDetail(response.data.data);
                }
            });
        });

    </script>
@endpush
