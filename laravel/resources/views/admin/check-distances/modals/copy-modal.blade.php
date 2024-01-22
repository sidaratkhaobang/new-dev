<div class="modal fade" id="modal-copy" tabindex="-1" aria-labelledby="modal-copy" style="display: none;" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header"></div>
            <div class="modal-body">
                <div class="row push">
                    <div class="col-sm-12">
                        <label class="text-start col-form-label">{{ __('check_distances.car_class_model') }}</label>
                        <input type="text" id="car_class" name="car_class" class="form-control" disabled />
                    </div>
                </div>
                <div class="row push mb-4">
                    <div class="col-sm-6">
                        <x-forms.select-option :value="null" id="car_brand_id_field" :list="null"
                            :label="__('car_classes.car_brand')" :optionals="[
                                'ajax' => true,
                            ]" />
                    </div>
                    <div class="col-sm-6">
                        <x-forms.select-option :value="null" id="car_class_id_field" :list="null"
                            :label="__('car_classes.class')" :optionals="[
                                'ajax' => true,
                            ]" />
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-sm-12 text-end">
                        <button type="button" class="btn btn-secondary btn-block btn-hide-copy"
                            data-dismiss="modal">{{ __('lang.cancel') }}</button>
                        <button type="button"
                            class="btn btn-primary btn-block btn-copy-submit">{{ __('lang.ok') }}</button>
                        <input type="hidden" name="car_class_copy" id="car_class_copy" value="">
                    </div>
                </div>
            </div>
            <div class="modal-footer"></div>
        </div>
    </div>
</div>

@push('scripts')
    <script>
        $(".btn-hide-copy").on("click", function() {
            $('#modal-copy').modal('hide');
        });

        $(".btn-copy-submit").on("click", function() {
            var data = {
                car_class_copy: document.getElementById("car_class_copy").value,
                car_class_id: document.getElementById("car_class_id_field").value,
            };
            var updateUri = "{{ route('admin.check-distances.copy-check-distance') }}";
            axios.post(updateUri, data).then(response => {
                if (response.data.success) {
                    mySwal.fire({
                        title: "{{ __('lang.store_success_title') }}",
                        text: "คัดลอกเรียบร้อย",
                        icon: 'success',
                        confirmButtonText: "{{ __('lang.ok') }}"
                    }).then(value => {
                        if (response.data.redirect) {
                            window.location.href = response.data.redirect;
                        } else {
                            window.location.reload();
                        }
                    });
                    $('#modal-copy').modal('hide');
                } else {
                    mySwal.fire({
                        title: "{{ __('lang.store_error_title') }}",
                        text: "ไม่พบข้อมูล",
                        icon: 'error',
                        confirmButtonText: "{{ __('lang.ok') }}",
                    }).then(value => {
                        if (value) {
                            //
                        }
                    });
                }
            }).catch(error => {
                mySwal.fire({
                    title: "{{ __('lang.store_error_title') }}",
                    text: "ไม่พบข้อมูล",
                    icon: 'error',
                    confirmButtonText: "{{ __('lang.ok') }}",
                }).then(value => {
                    if (value) {
                        //
                    }
                });
            });
        });
    </script>
@endpush
