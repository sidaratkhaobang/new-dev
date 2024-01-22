<div class="modal fade" id="modal-rental" tabindex="-1" aria-labelledby="modal-driver" aria-hidden="false">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-popout" style="width:1250px;">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="driver-modal-label">{{ __('short_term_rentals.short_term_rental_detail') }}
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="form-group row push mb-4">
                    <div class="col-sm-3">
                        {{ __('short_term_rentals.rental_no') }} 
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
                        {{ __('short_term_rentals.package') }} 
                    </div>
                    <div class="col-sm-1">
                        :
                    </div>
                    <div class="col-sm-4">
                        <div id="product_name"></div>
                    </div>
                </div>
                <div class="form-group row push mb-4">
                    <div class="col-sm-3">
                        {{ __('short_term_rentals.license_plate') }} 
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
                        {{ __('short_term_rentals.pickup_date') }} 
                    </div>
                    <div class="col-sm-1">
                        :
                    </div>
                    <div class="col-sm-4">
                        <div id="pickup_date"></div>
                    </div>
                </div>
                <div class="form-group row push mb-4">
                    <div class="col-sm-3">
                        {{ __('short_term_rentals.return_date') }} 
                    </div>
                    <div class="col-sm-1">
                        :
                    </div>
                    <div class="col-sm-4">
                        <div id="return_date"></div>
                    </div>
                </div>
                <div class="form-group row push mb-4">
                    <div class="col-sm-3">
                        {{ __('short_term_rentals.origin_name') }} 
                    </div>
                    <div class="col-sm-1">
                        :
                    </div>
                    <div class="col-sm-4">
                        <div id="origin_name"></div>
                    </div>
                </div>
                <div class="form-group row push mb-4">
                    <div class="col-sm-3">
                        {{ __('short_term_rentals.destination_name') }} 
                    </div>
                    <div class="col-sm-1">
                        :
                    </div>
                    <div class="col-sm-4">
                        <div id="destination_name"></div>
                    </div>
                </div>
                <div class="form-group row push mb-4">
                    <div class="col-sm-3">
                        {{ __('short_term_rentals.customer') }} 
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
