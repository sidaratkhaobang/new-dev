@extends('admin.layouts.layout')

@section('page_title', $page_title)

@push('styles')
    <style>
        .tr-last-item .td-table {
            border-bottom-width: 1.5px !important;
            border-color: #a8aec2;
        }
    </style>
@endpush
@section('content')
    <div class="block {{ __('block.styles') }}">
        <div class="block-content" id="accessory-new" v-cloak data-detail-uri="" data-title="">
            <form id="save-form">
                <x-forms.hidden id="id" :value="$car_list->id" />
                <x-forms.hidden id="lt_rental_id" :value="$lt_rental_id" />
                <h4 class="fw-light text-gray-darker">{{ __('long_term_rentals.car_table') }}</h4>
                <hr>
                <div class="row push mb-3">
                    <div class="col-sm-4">
                        <x-forms.input-new-line id="car_class_field" :value="$car_list->car_class_text" :label="__('long_term_rentals.car_class')"
                            :optionals="['required' => true]" />
                    </div>
                    <div class="col-sm-2">
                        <x-forms.input-new-line id="car_color_field" :value="$car_list->car_color_text" :label="__('long_term_rentals.car_color')"
                            :optionals="['required' => true]" />
                    </div>
                    <div class="col-sm-2">
                        <x-forms.input-new-line id="amount_car_field" :value="$car_list->amount" :label="__('long_term_rentals.car_amount')"
                            :optionals="['required' => true]" />
                    </div>
                    <div class="col-sm-4">
                        <x-forms.radio-inline id="have_accessory_field" :value="$car_list->have_accessories" :list="[
                            ['name' => __('lang.have'), 'value' => 1],
                            ['name' => __('lang.no_have'), 'value' => 0],
                        ]"
                            :label="__('long_term_rentals.have_accessory')" />
                    </div>
                </div>
                <div class="row push mb-3">
                    <div class="col-sm-4">
                        <x-forms.input-new-line id="remark" :value="$car_list->remark" :label="__('long_term_rentals.remark')" />
                    </div>
                </div>

                <div class="row push mb-3">
                    <div class="col-auto">
                        <h4 class="fw-light text-gray-darker mt-1">{{ __('long_term_rentals.accessories_table') }}</h4>
                    </div>
                    @if (!isset($view_only))
                        <div class="col-sm-3">
                            <button type="button" class="btn btn-primary"
                                onclick="modalAccessory()">{{ __('long_term_rentals.bom') }}</button>
                            @include('admin.long-term-rental-spec-tors.modals.bom-modal')
                        </div>
                    @endif
                </div>
                <hr>
                @if (!isset($view_only))
                    <div class="row push">
                        <div class="col-sm-8">
                            <x-forms.select-option id="accessory_field" :value="null" :list="null"
                                :label="__('long_term_rentals.accessories')" :optionals="['select_class' => 'js-select2-custom', 'ajax' => true]" />
                        </div>
                        <div class="col-sm-4">
                            <x-forms.input-new-line id="amount_accessory_field" :value="null" :label="__('long_term_rentals.car_amount')"
                                :optionals="['oninput' => true, 'type' => 'number']" />
                        </div>
                    </div>
                    <div class="row push">
                        <div class="col-sm-8">
                            <x-forms.input-new-line id="tor_section_field" :value="null" :label="__('long_term_rentals.tor_section')" />
                        </div>
                        <div class="col-sm-4">
                            <x-forms.input-new-line id="remark_bom_field" :value="null" :label="__('long_term_rentals.remark')" />
                        </div>
                    </div>
                    <div class="row mb-4">
                        <div class="col-md-12 text-end">
                            <button type="button" class="btn btn-primary"
                                onclick="addAccessory()">{{ __('lang.add') }}</button>
                        </div>
                    </div>
                @endif
                <div id="accessory-new" v-cloak data-detail-uri="" data-title="">
                    <div class="table-wrap">
                        <table class="table table-striped">
                            <thead class="bg-body-dark">
                                <th>#</th>
                                <th>{{ __('long_term_rentals.accessories') }}</th>
                                <th>{{ __('long_term_rentals.amount_accessory') . ' / ' . __('long_term_rentals.car_unit') }}
                                </th>
                                <th>{{ __('long_term_rentals.tor_section') }}</th>
                                <th>{{ __('long_term_rentals.remark') }}</th>
                                @if (!isset($view_only))
                                    <th class="sticky-col text-center">{{ __('lang.tools') }}</th>
                                @endif
                            </thead>
                            <tbody v-if="car_accessories.length > 0">
                                <tr v-for="(item, index) in car_accessories">
                                    <td>@{{ index + 1 }}</td>
                                    <td>@{{ item.accessory_text }}</td>
                                    <td>@{{ item.amount_accessory }}</td>
                                    <td>@{{ item.tor_section }}</td>
                                    <td>@{{ item.remark }}</td>
                                    @if (!isset($view_only))
                                        <td class="sticky-col text-center">
                                            <div class="btn-group">
                                                <div class="col-sm-12">
                                                    <a class="dropdown-item btn-delete-row"
                                                        v-on:click="removeAccessory(index)"><i
                                                            class="fa fa-trash-alt me-1"></i></a>
                                                </div>
                                            </div>
                                        </td>
                                    @endif
                                </tr>
                            </tbody>
                            <tbody v-else>
                                <tr class="table-empty">
                                    <td class="text-center" colspan="6">"
                                        {{ __('lang.no_list') . __('long_term_rentals.accessories_table') }} "</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div v-for="(item, index) in car_accessories">
                        <input type="hidden" v-bind:name="'accessories[' + index + '][accessory_id]'"
                            v-bind:value="item.accessory_id">
                        <input type="hidden" v-bind:name="'accessories[' + index + '][accessory_amount]'"
                            v-bind:value="item.amount_accessory">
                        <input type="hidden" v-bind:name="'accessories[' + index + '][tor_section]'"
                            v-bind:value="item.tor_section">
                        <input type="hidden" v-bind:name="'accessories[' + index + '][remark]'"
                            v-bind:value="item.remark">
                        <input type="hidden" v-bind:name="'accessories[' + index + '][type_accessories]'"
                            v-bind:value="item.type_accessories">
                    </div>
                </div>
                <div class="row push mt-3">
                    <div class="col-sm-12 text-end">
                        @if (!isset($view_only))
                            @if (isset($accessory_controller))
                                <a class="btn btn-secondary"
                                    href="{{ route('admin.long-term-rental.specs.accessories.edit', $lt_rental_id) }}">{{ __('lang.back') }}</a>
                            @else
                                <a class="btn btn-secondary" href="{{ $redirect_route }}">{{ __('lang.back') }}</a>
                            @endif
                        @else
                            <a class="btn btn-secondary"
                                href="{{ route('admin.long-term-rental.specs.show', ['rental' => $lt_rental_id, 'accessory_controller' => true]) }}">{{ __('lang.back') }}</a>
                        @endif
                        @if (!isset($view_only))
                            @if (isset($accessory_controller))
                                <button type="button"
                                    class="btn btn-primary btn-save-form-accessory">{{ __('lang.save') }}</button>
                            @else
                                <button type="button" class="btn btn-primary btn-save-form">{{ __('lang.save') }}</button>
                            @endif
                        @endif
                    </div>
                </div>

            </form>
        </div>
    </div>
@endsection

@include('admin.components.select2-default')
@include('admin.components.sweetalert')
@include('admin.components.form-save', [
    'store_uri' => route('admin.long-term-rental.specs.tor.update-accessory'),
])
@include('admin.long-term-rental-spec-tors.scripts.accessory-new-script')

@include('admin.components.select2-ajax', [
    'id' => 'accessory_field',
    'url' => route('admin.util.select2.accessories-type-accessory'),
])

@include('admin.components.select2-ajax', [
    'id' => 'bom_field',
    'modal' => '#modal-bom',
    'url' => route('admin.util.select2.accessories-bom'),
])

@push('scripts')
    <script>
        $('#car_class_field').prop('disabled', true);
        $('#car_color_field').prop('disabled', true);
        $('#amount_car_field').prop('disabled', true);
        $("input[type='radio']").attr('disabled', true);
        $('#remark').prop('disabled', true);

        $('.toggle-table').click(function() {
            $(this).parent().next('tr').toggle();
            $(this).children().toggleClass('fa fa-angle-down text-muted').toggleClass(
                'fa fa-angle-right text-muted');
        });

        $('#bom_field').change(function() {
            var id = $('#bom_field :selected').val();
            $.ajax({
                type: 'POST',
                url: "{{ route('admin.long-term-rental.specs.tors.get-data-accessory-type') }}",
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                data: {
                    id: id
                },
                success: function(data) {
                    $('#list_table').empty();
                    console.log(data.lists.length);
                    if (data.lists.length > 0) {
                        $('#empty-list').hide();
                        data.lists.forEach((element, index) => {
                            console.log(element.name);
                            $('#list_table').append(`<tr><td>${index+1}</td>
                            <td>${element.name}</td><td>${element.amount}</td>
                            <td><input type="text" class="form-control" id="tor_section"></td>
                            <td><input type="text" class="form-control" id="remark_bom"></td>`)
                        });
                    } else if (data.lists.length == 0) {
                        $('#empty-list').show();
                    }
                }
            });

        });

        $(".btn-save-form-accessory").on("click", function() {
            let storeUri = "{{ route('admin.long-term-rental.specs.tor.update-accessory') }}";
            var formData = new FormData(document.querySelector('#save-form'));
            formData.append('accessory_controller', true);
            saveForm(storeUri, formData);
        });

        $(".btn-save-form").on("click", function() {
            let storeUri = "{{ route('admin.long-term-rental.specs.tor.update-accessory') }}";
            var formData = new FormData(document.querySelector('#save-form'));
            // formData.append('accessory_controller', true);
            saveForm(storeUri, formData);
        });
    </script>
@endpush
