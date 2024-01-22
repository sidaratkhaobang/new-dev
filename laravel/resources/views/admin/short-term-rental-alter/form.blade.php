@extends('admin.layouts.layout')

@section('page_title', $page_title . ' ' . $d->worksheet_no)
@section('page_title_sub')
    @if (isset($d->status))
        {!! badge_render(
            __('short_term_rentals.class_' . $d->status),
            __('short_term_rentals.status_' . $d->status),
            null,
        ) !!}
    @endif
@endsection

@section('content')

    @include('admin.components.creator')
    <x-short-term-rental.step-service :rentalid="$rental_id" :success="true" :step="1" :show="true"
                                      :istoggle="true" :showstep="false"/>
    <x-short-term-rental.step-channel :rentalid="$rental_id" :success="true" :step="2" :show="true"
                                      :istoggle="true" :showstep="false"/>

    <div class="block {{ __('block.styles') }}">
        <x-blocks.block-header-step :title="__('short_term_rentals.step_title.info')" :step="3"
                                    :optionals="['block_icon_class' => __('short_term_rentals.step_icon.info'), 'showstep' => false]"/>
        <div class="block-content pt-0">
            <form id="save-form">
                @include('admin.short-term-rental-alter.sections.branch')
                @include('admin.short-term-rental-info.sections.rental-detail', ['edit_rental' => true])
                @include('admin.short-term-rental-info.sections.customer', ['edit_rental' => true])
                @include('admin.short-term-rental-info.sections.tax-invoice', ['edit_rental' => true])
                <x-forms.hidden id="rental_id" :value="$rental_id"/>
                <x-forms.hidden id="service_type_id" :value="$service_type_id"/>
                <x-forms.hidden id="is_customer_address" :value="$use_customer_billing_address"/>
                <x-forms.hidden id="customer_billing_address_id_selected" :value="$d->customer_billing_address_id"/>

                <div class="row">
                    <div class="col-sm-12 text-end">
                        {{-- <a class="btn btn-danger btn-cancel-status">{{ __('lang.cancel') }}</a> --}}
                        <a class="btn btn-secondary"
                           href="{{ route('admin.short-term-rentals.index') }}">{{ __('lang.back') }}</a>
                        @if (!isset($view))
                            @can(Actions::Manage . '_' . Resources::ShortTermRental)
                                <button type="button"
                                        class="btn btn-primary btn-save-form">{{ __('lang.next') }}</button>
                            @endcan
                        @endif
                    </div>
                </div>
            </form>
        </div>
    </div>

    <x-short-term-rental.step-driver :rentalid="$rental_id" :success="true" :step="5" :show="true"
                                     :istoggle="true" :showstep="false"/>
    <x-short-term-rental.step-promotion :rentalid="$rental_id" :success="true" :step="6" :show="true"
                                        :istoggle="true" :showstep="false"/>
    <x-short-term-rental.step-summary :rentalid="$rental_id" :success="true" :step="7" :show="true"
                                      :istoggle="true" :showstep="false"/>
@endsection

@include('admin.components.select2-default')
@include('admin.components.sweetalert')
@include('admin.components.form-save', [
    'store_uri' => route('admin.short-term-rental.alter.store-info'),
])
@include('admin.short-term-rentals.scripts.update-cancel-status')
@include('admin.components.date-input-script')
@include('admin.short-term-rental-info.scripts.customer-script')
@include('admin.short-term-rental-info.scripts.tax-invoice-script')
@include('admin.short-term-rental-info.scripts.origin-script')
@include('admin.short-term-rental-info.scripts.destination-script')
@include('admin.short-term-rental-info.scripts.origin-google-map')
@include('admin.short-term-rental-info.scripts.destination-google-map')

@include('admin.components.select2-ajax', [
    'id' => 'product_id',
    'parent_id' => 'branch_id',
    'parent_id_2' => 'service_type_id',
    'url' => route('admin.util.select2.products-by-branch'),
])

@include('admin.components.select2-ajax', [
    'id' => 'origin_id',
    'parent_id' => 'branch_id',
    'url' => route('admin.util.select2.origins-by-branch'),
])

@include('admin.components.select2-ajax', [
    'id' => 'destination_id',
    'parent_id' => 'branch_id',
    'url' => route('admin.util.select2.destinations-by-branch'),
])

@include('admin.components.select2-ajax', [
    'id' => 'customer_id',
    'parent_id' => 'customer_type',
    'url' => route('admin.util.select2-customer.customer-codes'),
])
@include('admin.short-term-rental-info.scripts.update-datetime-script')
@push('scripts')
    <script>
        $("input[type=radio][name=order_channel]").attr('disabled', true);
        $("select").attr('disabled', true);
        $("#pickup_date").attr('disabled', true);
        $("#avg_distance").attr('disabled', true);
        $("#customer_name").attr('disabled', true);
        $("#customer_email").attr('disabled', true);
        $("#customer_tel").attr('disabled', true);
        $("#customer_zipcode").attr('disabled', true);
        $("#customer_address").attr('disabled', true);
        $('[name="check_customer_address[]"]').prop('disabled', true);
        $("#destination_id").attr('disabled', false);
        $("#origin_remark").attr('disabled', true);
        $("#destination_remark").attr('disabled', true);
        $('#return_date').attr('disabled', true);
        $('#destination_id').attr('disabled', true);
        $(document).ready(() => {
            var product_id_selected = $('#product_id_selected').val();
            if (product_id_selected != "") {
                $("input[name=product_id][value='" + product_id_selected + "']").prop("checked", true);
            }
            $("input[type=radio][name=product_id]").attr('disabled', true);
            $("input[type=checkbox]").attr('disabled', true);
        });
    </script>
@endpush
