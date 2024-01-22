<div class="block {{ __('block.styles') }}">
    @section('block_options_list')
        <div class="block-options-item">
            <button class="btn btn-primary btn-renew-all"><i
                    class="icon-menu-document-add"></i>{{ __('insurance_car.renew_all_insurance') }}</button>
            <button class="btn btn-primary btn-renew-all"><i
                    class="icon-menu-document-add"></i>{{ __('insurance_car.renew_all_cmi') }}</button>
        </div>
    @endsection
    @section('block_options_list_car')
        <div class="block-options-item">
            <button class="btn btn-primary btn-renew-all"><i
                    class="icon-menu-document-add"></i>{{ __('insurance_car.renew_insurance') }}</button>
            <button class="btn btn-primary btn-renew-all"><i
                    class="icon-menu-document-add"></i>{{ __('insurance_car.renew_cmi') }}</button>
        </div>
    @endsection
    @include('admin.components.block-header', [
            'text' => __('lang.total_list'),
            'block_option_id' => '_list'
        ])
    <div class="block-content">
        <div class="justify-content-between mb-4">
            <div class="block {{ __('block.styles') }}">
                <div class="justify-content-between mb-4">
                    <div class="form-group row push mb-4">
                        <div class="col-sm-3">
                            <div class="text-center">
                                <img class="rounded"
                                     src="https://imgd.aeplcdn.com/370x208/n/cw/ec/130591/fronx-exterior-right-front-three-quarter-4.jpeg?isig=0&q=75"
                                     width="100%" height="100%">
                            </div>
                        </div>
                        <div class="col-sm-9">
                            @include('admin.components.block-header', [
                                        'text' => __('insurance_car.insurance_list'),
                                        'block_option_id' => '_list_car'
                                    ])
                            <div class="block-content">
                                <div class="justify-content-between mb-4">
                                    <div class="table-wrap db-scroll">
                                        <table class="table table-striped table-vcenter">
                                            <thead class="bg-body-dark">
                                            <th class="text-center" style="width: 70px;">
                                                <div class="form-check d-inline-block">
                                                    <input class="form-check-input" type="checkbox" value=""
                                                           id="selectAll"
                                                           name="selectAll">
                                                    <label class="form-check-label" for="selectAll"></label>
                                                </div>
                                            </th>
                                            <th>
                                                {!! __('insurance_car.worksheet_no') !!}
                                            </th>
                                            <th>
                                                {{__('insurance_car.type')}}
                                            </th>
                                            <th>
                                                {{__('insurance_car.policy_number_no')}}
                                            </th>
                                            <th>
                                                {{__('insurance_car.insurance_company')}}
                                            </th>
                                            <th>
                                                {{__('insurance_car.coverage_start_date')}}
                                            </th>
                                            <th>
                                                {{__('insurance_car.coverage_end_date')}}
                                            </th>
                                            <th>
                                                {{__('insurance_car.status')}}
                                            </th>
                                            <th>
                                            </th>
                                            </thead>
                                            <tbody>
                                            <tr>
                                                <td class="text-center">
                                                    <div class="form-check d-inline-block">
                                                        <input class="form-check-input form-check-input-each"
                                                               type="checkbox"
                                                               value=""
                                                               id="" name="row_checkboxes">
                                                        <label class="form-check-label" for=""></label>
                                                    </div>
                                                </td>
                                                <td>
                                                    INS2023020002
                                                </td>
                                                <td>
                                                    พรบ.
                                                </td>
                                                <td>
                                                    20987644652
                                                </td>
                                                <td>
                                                    บริษัท ทิพยประกันภัย จำกัด
                                                </td>
                                                <td>
                                                    20/03/2023
                                                </td>
                                                <td>
                                                    20/03/2023
                                                </td>
                                                <td>
                                                    สถานะ
                                                </td>
                                                <td>
                                                    เครื่องมือ
                                                </td>
                                            </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
