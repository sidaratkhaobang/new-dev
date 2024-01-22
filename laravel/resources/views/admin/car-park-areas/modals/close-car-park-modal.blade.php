<div class="modal fade" id="modal-close-car-park" tabindex="-1" aria-labelledby="modal-close-car-park" style="display: none;"
    aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-start" id="cancel-modal-label">ยืนยันการปิดช่องจอด</h5>
                <button type="button" class="btn-close btn-hide-close-car-park" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body pb-1">
                <p id="cancel-modal-body">กรุณาระบุวันที่ต้องการปิดช่องจอด</p>
                <div class="row push mb-1">
                    <div class="col-sm-12">
                        <label class="text-start col-form-label">
                            {{ __('parking_lots.disabled_date') }}
                            <span class="text-danger">*</span>
                        </label>
                        <div class="form-group">
                            <div class="input-daterange input-group" data-date-format="mm/dd/yyyy" data-week-start="1"
                                data-autoclose="true" data-today-highlight="true">
                                <input type="text" class="js-flatpickr form-control flatpickr-input" id="start_disabled_date"
                                    name="start_disabled_date" 
                                    placeholder="{{ __('parking_lots.start_date') }}" data-week-start="1"
                                    data-autoclose="true" data-today-highlight="true">
                                <div class="input-group-prepend input-group-append">
                                    <span class="input-group-text font-w600">
                                        <i class="fa fa-fw fa-arrow-right"></i>
                                    </span>
                                </div>
                                <input type="text" class="js-flatpickr form-control flatpickr-input" id="end_disabled_date"
                                    name="end_disabled_date"
                                    placeholder="{{ __('parking_lots.end_date') }}" data-week-start="1"
                                    data-autoclose="true" data-today-highlight="true">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row push mb-4">
                    <div class="col-sm-12">
                        <x-forms.checkbox-inline id="is_permanent_disabled" :list="[
                            [
                                'id' => 1,
                                'name' => __('parking_lots.is_permanent_disabled'),
                                'value' => 1,
                            ]
                        ]" :label="null" :value="null" />
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <div class="form-group">
                    <div class="col-sm-12 text-end mb-2">
                        <button type="button" class="btn btn-secondary btn-block btn-hide-close-car-park"
                            data-dismiss="modal">{{ __('lang.cancel') }}</button>
                        <button type="button"
                            class="btn btn-primary btn-block btn-submit-close-car-park">{{ __('lang.confirm') }}</button>
                        <input type="hidden" name="car_park_id" id="car_park_id">
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
    <script>
        $('.btn-submit-close-car-park').on("click", function() {
            console.log($("#is_permanent_disabled").val());
            var id = $('#car_park_id').val();
            var data = {
                car_park_id: id,
                start_disabled_date: $("#start_disabled_date").val(),
                end_disabled_date: $("#end_disabled_date").val(),
                is_permanent_disabled: $('input[name="is_permanent_disabled[]"]:checked').val(),
            };
            $('#modal-close-car-park').modal('hide');
            clearInputVal();
            updateDefaultStatus(data);
        });

        $(".btn-hide-close-car-park").on("click", function() {
            $('#modal-close-car-park').modal('hide');
            clearInputVal();
        });

        function clearInputVal()
        {
            $("#start_disabled_date").val('');
            $("#end_disabled_date").val('');
            $('input[name="is_permanent_disabled[]"]').prop('checked', false);
        }

        $('input[name="is_permanent_disabled[]').change(
            function(){
                if ($(this).is(':checked')) {
                    $("#end_disabled_date").val('');
                }
            }
        );
        $('#end_disabled_date').change(
            function(){
                if ($(this).val().length > 0) {
                    $('input[name="is_permanent_disabled[]"][value="1"]').prop('checked', false);
                }
            }
        );
    </script>
@endpush
