<div id="modal-cmi-renew" class="modal fade" tabindex="-1" aria-labelledby="modal-cmi-renew" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">

                <form id="save-form">
                    <x-forms.hidden id="insurance_cmi_id" :value="null" />
                    <x-forms.hidden id="insurance_vmi_id" :value="null" />
                    <x-forms.hidden id="car_id" :value="null" />
                    <x-forms.hidden id="type" :value="null" />
                    <x-forms.hidden id="modal_cmi_id" :value="null"/>
                    <div class="block-content">
                        <div class="row push mb-4">
                            @include('admin.components.block-header',[
            'text' =>   'ต่ออายุ พรบ.'   ,
           'block_icon_class' => 'icon-document',
           ])
                            <div class="col-sm-3">
                                <x-forms.input-new-line id="lot" :value="null" :label="'เลขการต่ออายุ'"/>

                            </div>
                            <div class="col-sm-3">
                                <x-forms.select-option :value="'RenewCmi'" id="modal-renew-cmi-jobtype"
                                                       :list="$insurance_job_type_list"
                                                       :label="__('insurance_car.job_type')"
                                                       :optionals="['required' => true]"/>
                            </div>
                            <div class="col-sm-3">
                                <x-forms.input-new-line id="modal_renew_cmi_year" :value="'1ปี'" :label="__('insurance_car.cmi_year')" :optionals="['required' => false]" />
{{--                                <x-forms.select-option :value="1" id="modal_renew_cmi_year" :list="$year_list"--}}
{{--                                                       :label="__('insurance_car.cmi_year')"--}}
{{--                                                       :optionals="['required' => true]"/>--}}
                            </div>

                        </div>
                        <div class="row push mb-4">
                            <div class="col-sm-3">
                                <x-forms.date-input id="modal_renew_cmi_startdate" :value="null"
                                                    :label="__('insurance_car.insurance_start_date')"/>
                            </div>
                            <div class="col-sm-3">
                                <x-forms.date-input id="modal_renew_cmi_enddate" :value="null"
                                                    :label="__('insurance_car.insurance_end_date')"/>
                            </div>
                        </div>
                        <div class="row push mb-1">

                            <div class="col-sm-3">
                                <x-forms.checkbox-inline id="modal-renew-cmi-insurance-status"
                                                         :label="'บริษัทที่ต้องการการต่ออายุ'" :list="[
                                    [
                                        'id' => 1,
                                        'name' => 'ต้องการต่อกับบริษัทเดิม',
                                        'value' => 1,
                                    ],
                                ]" :label="null"
                                                         :value="null"/>
                            </div>
                            <div class="col-sm-3">
                                <x-forms.select-option :value="null" id="modal_renew_insurance_company"
                                                       :list="$insurer_list"
                                                       :label="__('insurance_car.company')"
                                                       :optionals="['required' => true]"/>
                            </div>
                        </div>
                        <div class="row push mb-4">
                            @include('admin.components.block-header',[
           'text' =>   'รายการทั้งหมด'   ,
          'block_icon_class' => 'icon-document',
          ])
                            <table id="modal-renew" class="table table-striped">
                                <thead class="bg-body-dark">
                                <th>ทะเบียนรถ</th>
                                <th>หมายเลขตัวถัง</th>
                                <th>หมายเลขเครื่องยนต์</th>
                                </thead>
                                <tbody v-if="renew_cmi_list.length > 0" id="table-accessory-data">

                                <tr v-for="(item, index) in renew_cmi_list">

                                    <td>
                                        @{{ item.chassis_no || '-' }}

                                    </td>
                                    <td>
                                        @{{ item.license_plate || '-'}}
                                    </td>
                                    <td>
                                        @{{ item.engine_no || '-' }}
                                    </td>
                                </tr>

                                </tbody>
                                <tbody v-else>
                                <tr>
                                    <td class="text-center" colspan="12">" ไม่มีรายการรถ "</td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="block {{ __('block.styles') }}" style="border: none; box-shadow: none;">
                            <div class="block-content group-submit">
                                <x-forms.submit-group
                                    :optionals="['url' => 'admin.insurance-car.index', 'view' => empty($view) ? null : $view,'manage_permission' => Actions::Manage . '_' . Resources::InsuranceCar]"/>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
    <script>
        $('#lot').prop('disabled', true);
        $('#modal_renew_cmi_year').prop('disabled', true);
        $(document).on('change', "#modal-renew-cmi-insurance-status_0", function () {
            let insurance_status = $(this).is(':checked');
            if (insurance_status == true) {
                $('#modal_renew_insurance_company').parent().hide()
            } else {
                $('#modal_renew_insurance_company').parent().show()
            }
        })
    </script>
@endpush
