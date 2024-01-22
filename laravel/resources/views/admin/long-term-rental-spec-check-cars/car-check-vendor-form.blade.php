@extends('admin.layouts.layout')

@section('page_title', $page_title)

@push('styles')
@endpush
@section('content')
    <div class="block {{ __('block.styles') }}">
        <div class="block-header">
        </div>
        <div class="block-content">
            <form id="save-form">
                <div class="row push mb-5">
                    <div class="col-sm-6">

                        <label for="Dealer">Dealer</label><br>
                        <span>{{ $dealer->name }}</span>
                    </div>
                    <div class="col-sm-3">
                        <label for="date">วันที่บันทึก</label><br>
                        <span>{{ Carbon::now()->format('d/m/Y') }}</span>
                    </div>
                </div>
                <div class="row mb-3 ">
                    <div class="col-sm-4 mb-3">
                    </div>
                </div>
                <div class="mb-3" id="app2" v-cloak>
                    <div class="row push mb-5">

                        @include('admin.long-term-rental-specs.sections.dealer-form')
                    </div>
                </div>
                <div class="row push">
                    <div class="col-sm-12 text-end">
                        <button type="button" class="btn btn-primary btn-save">{{ __('lang.save') }}</button>
                    </div>
                </div>
                <x-forms.hidden id="rental_id" :value="$rental_id" />
                <x-forms.hidden id="dealer_id" :value="$dealer->id" />
            </form>
        </div>
    </div>
@endsection
@include('admin.components.select2-default')
@include('admin.components.sweetalert')
@include('admin.components.date-input-script')
@include('admin.components.form-save', [
    'store_uri' => route('long-term-rental-spec-dealers.store'),
])

@include('admin.long-term-rental-specs.scripts.dealer-form-script')


@include('admin.components.select2-ajax', [
    'id' => 'car_class_field',
    'modal' => '#modal-car-accessory',
    'url' => route('admin.util.select2.car-class'),
])

@include('admin.components.select2-ajax', [
    'id' => 'car_color_field',
    'modal' => '#modal-car-accessory',
    'url' => route('admin.util.select2.car-colors'),
])

@include('admin.components.select2-ajax', [
    'id' => 'accessory_field',
    'modal' => '#modal-car-accessory',
    'url' => route('admin.util.select2.accessories-type-accessory'),
])

@include('admin.components.select2-ajax', [
    'id' => 'bom_id',
    'modal' => '#modal-bom-car',
    'url' => route('admin.util.select2-rental.lt-rental-by-bom'),
])

@push('scripts')
    <script>
        $('#worksheet_no').prop('disabled', true);
        $('#job_type').prop('disabled', true);
        $('#rental_duration').prop('disabled', true);
        $('#remark').prop('disabled', true);
        $('#customer_type').prop('disabled', true);
        $('#customer_id').prop('disabled', true);
        $('#customer_name').prop('disabled', true);
        $('#offer_date').prop('disabled', true);

        var view_only = '{{ isset($view_only) ? true : false }}';
        if (view_only) {
            $('input[name="tor_line_check_input[]"]').prop('disabled', true);
        }

        $('.toggle-table').parent().next('tr').toggle();
        $('.toggle-table').click(function() {
            $(this).parent().next('tr').toggle();
            $(this).children().toggleClass('fa fa-angle-down text-muted').toggleClass(
                'fa fa-angle-right text-muted');
        });

        $(".btn-save-review").on("click", function() {
            let storeUri = "{{ route('admin.long-term-rental.specs.store') }}";
            var formData = new FormData(document.querySelector('#save-form'));
            var spec_status = $(this).attr('data-status');
            formData.append('spec_status', spec_status);
            saveForm(storeUri, formData);
        });

        function addBom() {
            bomCarVue.removeAll();
            $("#bom_id").val('').change();
            $("#modal-bom-car").modal("show");
        }

        $(".btn-save").on("click", function() {
            let storeUriDealer = "{{ route('long-term-rental-spec-dealers.store') }}";
            var formData = new FormData(document.querySelector('#save-form'));

            saveForm(storeUriDealer, formData);

        });

        $("input[name^='no_car_dealer']").change(function() {
            var id = $(this).attr('data-value');
            if (this.checked) {
                carVue2.clearDealerCheckCars(id);
            } else {
                carVue2.setDealerCheckCars(id);
            }
        });
    </script>
@endpush
