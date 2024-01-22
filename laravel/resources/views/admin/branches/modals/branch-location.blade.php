<div class="modal fade" id="modal-branch-location" role="dialog" style="overflow:hidden;"
    aria-labelledby="modal-car-class-color">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-popout">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="branch-location-modal-label">{{ __('lang.add_data') }}</h5>
            </div>
            <div class="modal-body">
                <div class="row push">
                    <div class="col-sm-4">
                        <x-forms.select-option id="location_group_field" :value="null" :list="null"
                            :label="__('branches.location_group')" :optionals="['select_class' => 'js-select2-custom', 'ajax' => true]" />
                    </div>
                    <div class="col-sm-8">
                        <x-forms.select-option id="location_field" :value="null" :list="null" :label="__('branches.location')"
                            :optionals="['select_class' => 'js-select2-custom', 'ajax' => true, 'required' => true]" />
                    </div>
                </div>
                <div class="row push mb-4">
                    <div class="col-sm-4">
                        <x-forms.radio-inline id="origin_field" :value="null" :list="$yes_no_list"
                            :label="__('branches.can_origin')" />
                    </div>
                    <div class="col-sm-4">
                        <x-forms.radio-inline id="stopover_field" :value="null" :list="$yes_no_list"
                            :label="__('branches.can_stopover')" />
                    </div>
                    <div class="col-sm-4">
                        <x-forms.radio-inline id="destination_field" :value="null" :list="$yes_no_list"
                            :label="__('branches.can_destination')" />
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-clear-search"
                    data-bs-dismiss="modal">{{ __('lang.cancel') }}</button>
                <button type="button" class="btn btn-primary"
                    onclick="addBranchLocation()">{{ __('lang.save') }}</button>
            </div>
        </div>
    </div>
</div>
