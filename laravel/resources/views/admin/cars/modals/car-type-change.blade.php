<div class="modal fade" id="modal-car-type-change" tabindex="-1" aria-labelledby="modal-car-type-change"
     style="display: none;"
     aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-body pb-1 mt-4">
                <h5 class="text-start" style="font-size: 24px;">
                    <i class="icon-menu-car"></i> {{__('cars.change_car_type')}}
                </h5>
                <div class="row push mb-4">
                    <div class="col-sm-4">
                        <x-forms.input-new-line id="car-type" :value="null" :label="__('cars.old_car_type')"/>
                    </div>
                    <div class="col-sm-4">
                        <x-forms.select-option id="car-type-change" :value="null" :list="$rental_type_list"
                                               :label="__('cars.new_car_type')"/>
                    </div>
                </div>
                <div class="d-flex justify-content-end push mb-4">
                    <button type="button"
                            class="button-submit-modal ms-1 me-1 btn btn-outline-secondary btn-hide-car-type-change"
                            data-bs-dismiss="modal" style="">{{ __('lang.cancel') }}</button>

                    <button onclick="saveCarChangeType()" type="button"
                            class="button-submit-modal ms-1 me-1 btn btn-primary btn-save-car-type-change"
                            style=""><i
                            class="fa fa-save"></i> {{ __('lang.save') }}</button>
                </div>
                <input type="hidden" id="car-type-id">
            </div>
        </div>
    </div>
</div>
@push('scripts')
    <script>
        $('#car-type').prop('disabled', true)

        function openModalCarChangeType(rentalType, carId) {
            if (rentalType) {
                $('#car-type').val(rentalType)
            } else {
                $('#car-type').val('')
            }
            if (carId) {
                $('#car-type-id').val(carId)
            } else {
                $('#car-type-id').val(null)
            }
            $('#modal-car-type-change').modal('toggle')
        }

        function saveCarChangeType() {
            let carId = $('#car-type-id').val()
            let carType = $('#car-type-change').val()
            let storeUri = "{{ route('admin.cars.update-type') }}"
            axios.post(storeUri, {
                car_id:carId,
                car_type:carType
            }).then(response => {
                if (response.data.success) {
                    hideLoading();
                    @if (isset($save_callback))
                    if (typeof(modalCallback) == "function") {
                        modalCallback(response.data);
                    } else {
                        saveCallback(response.data);
                    }
                    @else
                    mySwal.fire({
                        title: "{{ __('lang.store_success_title') }}",
                        text: "{{ __('lang.store_success_message') }}",
                        icon: 'success',
                        confirmButtonText: "{{ __('lang.ok') }}"
                    }).then(value => {
                        if (response.data.redirect) {
                            if (response.data.redirect === 'false'){
                                if (typeof(modalCallback) == "function") {
                                    modalCallback(response.data);
                                }
                            } else {
                                window.location.href = response.data.redirect;
                            }
                        } else {
                            window.location.reload();
                        }
                    });
                    @endif
                } else {
                    hideLoading();
                    mySwal.fire({
                        title: "{{ __('lang.store_error_title') }}",
                        text: response.data.message,
                        icon: 'error',
                        confirmButtonText: "{{ __('lang.ok') }}",
                    }).then(value => {
                        if (value) {
                            //
                        }
                    });
                }
            }).catch(error => {
                hideLoading();
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
            {{--axios.post("{{ route('admin.cars.update-type') }}", {--}}
            {{--    car_id: carId,--}}
            {{--    car_type: carType,--}}
            {{--}).then(response => {--}}
            {{--    console.log(response)--}}
                {{--if (response.data.success) {--}}
                {{--    $('#modal-send-sale-car').modal('toggle');--}}
                {{--} else {--}}
                {{--    mySwal.fire({--}}
                {{--        title: "{{ __('lang.store_error_title') }}",--}}
                {{--        text: response.data.message,--}}
                {{--        icon: 'error',--}}
                {{--        confirmButtonText: "{{ __('lang.ok') }}",--}}
                {{--    }).then(value => {--}}
                {{--        if (value) {--}}
                {{--            //--}}
                {{--        }--}}
                {{--    });--}}
                {{--}--}}
            // });
        }
    </script>
@endpush
