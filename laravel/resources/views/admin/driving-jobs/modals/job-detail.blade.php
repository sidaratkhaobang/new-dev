<div class="modal fade" id="modal-rental" tabindex="-1" aria-labelledby="modal-driver" aria-hidden="false">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-popout" style="width:1250px;">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="driver-modal-label">{{ __('driving_jobs.driver_detail') }}
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="form-group row push mb-4">
                    <div class="col-sm-3">
                        {{ __('driving_jobs.worksheet_no') }} 
                    </div>
                    <div class="col-sm-1">
                        :
                    </div>
                    <div class="col-sm-4">
                        <div id="worksheet_no"></div>
                    </div>
                </div>
                <div class="form-group row push mb-4">
                    <div class="col-sm-3">
                        {{ __('driving_jobs.driver_name') }} 
                    </div>
                    <div class="col-sm-1">
                        :
                    </div>
                    <div class="col-sm-4">
                        <div id="driver_name"></div>
                    </div>
                </div>
                <div class="form-group row push mb-4">
                    <div class="col-sm-3">
                        {{ __('driving_jobs.worksheet_type') }} 
                    </div>
                    <div class="col-sm-1">
                        :
                    </div>
                    <div class="col-sm-4">
                        <div id="job_type"></div>
                    </div>
                </div>
                <div class="form-group row push mb-4">
                    <div class="col-sm-3">
                        {{ __('driving_jobs.ref_no') }} 
                    </div>
                    <div class="col-sm-1">
                        :
                    </div>
                    <div class="col-sm-4">
                        <div id="ref_no"></div>
                    </div>
                </div>
                <div class="form-group row push mb-4">
                    <div class="col-sm-3">
                        {{ __('driving_jobs.job_type') }} 
                    </div>
                    <div class="col-sm-1">
                        :
                    </div>
                    <div class="col-sm-4">
                        <div id="self_drive_type"></div>
                    </div>
                </div>
                <div class="form-group row push mb-4">
                    <div class="col-sm-3" id="license_plate_chassis">
                        {{-- {{ __('driving_jobs.license_plate') }}  --}}
                    </div>
                    <div class="col-sm-1">
                        :
                    </div>
                    <div class="col-sm-4">
                        <div id="license_plate"></div>
                    </div>
                </div>
                <div class="form-group row push mb-4">
                    <div class="col-sm-3">
                        {{ __('driving_jobs.start_date') }} 
                    </div>
                    <div class="col-sm-1">
                        :
                    </div>
                    <div class="col-sm-4">
                        <div id="start_date"></div>
                    </div>
                </div>
                <div class="form-group row push mb-4">
                    <div class="col-sm-3">
                        {{ __('driving_jobs.end_date') }} 
                    </div>
                    <div class="col-sm-1">
                        :
                    </div>
                    <div class="col-sm-6">
                        <div id="end_date"></div>
                    </div>
                </div>
                <div class="form-group row push mb-4">
                    <div class="col-sm-3">
                        {{ __('driving_jobs.rental_origin') }} 
                    </div>
                    <div class="col-sm-1">
                        :
                    </div>
                    <div class="col-sm-6">
                        <div id="origin_name"></div>
                    </div>
                </div>
                <div class="form-group row push mb-4">
                    <div class="col-sm-3">
                        {{ __('driving_jobs.rental_destination') }} 
                    </div>
                    <div class="col-sm-1">
                        :
                    </div>
                    <div class="col-sm-6">
                        <div id="destination_name"></div>
                    </div>
                </div>
                <div class="form-group row push mb-4">
                    <div class="col-sm-3" id="cus_del">
                        {{-- {{ __('driving_jobs.customer') }}  --}}
                    </div>
                    <div class="col-sm-1">
                        :
                    </div>
                    <div class="col-sm-6">
                        <div id="customer_name"></div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
            </div>
        </div>
    </div>
</div>
