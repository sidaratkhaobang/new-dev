<div id="vue-item" class="block {{ __('block.styles') }}">
    @section('block_options_list')
        <div class="block-options-item">
            <button @click="getDataRenewAll('{{InsuranceCarEnum::VMI}}','ALL')"
                    class="btn btn-primary btn-renew-all btn-renew-all-vmi" data-bs-toggle="modal"
                    data-bs-target="#modal-cmi-renew"><i
                    class="icon-menu-document-add"></i>{{ __('insurance_car.renew_all_insurance') }}</button>
            <button @click="getDataRenewAll('{{InsuranceCarEnum::CMI}}','ALL')"
                    class="btn btn-primary btn-renew-all btn-renew-all-cmi" data-bs-toggle="modal"
                    data-bs-target="#modal-cmi-renew"><i
                    class="icon-menu-document-add"></i>{{ __('insurance_car.renew_all_cmi') }}</button>
        </div>
    @endsection

    @include('admin.components.block-header', [
            'text' => __('lang.total_list'),
            'block_option_id' => '_list'
        ])
    @if(!empty($car_list))
        @foreach($car_list as $key_car => $value_car)
            <div class="block-content">
                <div class="justify-content-between mb-4">
                    <div class="block {{ __('block.styles') }}">
                        <div class="justify-content-between mb-4">
                            <div class="form-group row push mb-4">
                                <div class="col-sm-3">
                                    <div class="text-center">
                                        @include('admin.cmi-components.car-section',['car' => $value_car])
                                    </div>
                                </div>
                                <div class="col-sm-9 block-main-car">
                                    @section('block_options_list_car_'.$key_car)
                                        <div class="block-options-item">
                                            <input id="car_id_{{$key_car}}" value="{{$value_car?->id}}" hidden>
                                            <button
                                                @click="getDataCmiRenew('{{InsuranceCarEnum::VMI}}','car_id_{{$key_car}}','{{$value_car?->id}}')"
                                                class="btn btn-primary btn-renew-all me-1" data-bs-toggle="modal"
                                                @if ($value_car->disabled_vmi) disabled @endif
                                                class="btn btn-primary btn-renew-all btn-renew-vmi" data-bs-toggle="modal"
                                                data-bs-target="#modal-cmi-renew"><i
                                                    class="icon-menu-document-add"></i>{{ __('insurance_car.renew_insurance') }}
                                            </button>
                                            <button
                                                @click="getDataCmiRenew('{{InsuranceCarEnum::CMI}}','car_id_{{$key_car}}','{{$value_car?->id}}')"
                                                class="btn btn-primary btn-renew-all" data-bs-toggle="modal"
                                                @if ($value_car->disabled_cmi) disabled @endif
                                                data-bs-target="#modal-cmi-renew"><i

                                                data-bs-target="#modal-cmi-renew btn-renew-cmi"><i

                                                    class="icon-menu-document-add"></i>{{ __('insurance_car.renew_cmi') }}
                                            </button>
                                        </div>
                                    @endsection
                                    @include('admin.components.block-header', [
                                                'text' => __('insurance_car.insurance_list'),
                                                'block_option_id' => '_list_car_'.$key_car
                                            ])
                                    <div class="block-content">
                                        <div class="justify-content-between mb-4">
                                            <div class="table-wrap db-scroll">
                                                <table class="table table-striped table-vcenter">
                                                    <thead class="bg-body-dark">
                                                    <th class="text-center" style="width: 70px;">
                                                        {{--                                                        <div class="form-check d-inline-block">--}}
                                                        {{--                                                            <input class="form-check-input check-all-block"--}}
                                                        {{--                                                                   type="checkbox" value=""--}}
                                                        {{--                                                                   id=""--}}
                                                        {{--                                                                   name="selectAll">--}}
                                                        {{--                                                            <label class="form-check-label" for="selectAll"></label>--}}
                                                        {{--                                                        </div>--}}
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
                                                    @if(!empty(count($value_car['cmi_data'])) || !empty(count($value_car['vmi_data'])))
                                                        @if($value_car['cmi_data'])
                                                            @foreach($value_car['cmi_data'] as $key_cmi => $value_cmi)
                                                                <tr>
                                                                    <td class="text-center">
                                                                        <div class="form-check d-inline-block">
                                                                            @if(!empty($value_cmi?->renew_status))
                                                                                <input
                                                                                    class="form-check-input form-check-input-each car_cmi"
                                                                                    type="checkbox"
                                                                                    value=""
                                                                                    data-id="{{$value_cmi?->id}}"
                                                                                    data-type="{{InsuranceCarEnum::CMI}}"
                                                                                    data-cmi_status="{{$value_cmi?->status_cmi}}"
                                                                                    data-car_id="{{$value_car?->id}}"
                                                                                    data-license_plate="{{$value_car?->license_plate}}"
                                                                                    data-engine_no="{{$value_car?->engine_no}}"
                                                                                    data-chassis_no="{{$value_car?->chassis_no}}"
                                                                                >
                                                                            @endif

                                                                        </div>
                                                                    </td>
                                                                    <td>
                                                                        {{$value_cmi?->worksheet_no ?? "-"}}
                                                                    </td>
                                                                    <td>
                                                                        พรบ.
                                                                    </td>
                                                                    <td>
                                                                        {{$value_cmi?->policy_reference_cmi ?? "-"}}
                                                                    </td>
                                                                    <td>
                                                                        {{$value_cmi?->insurer?->insurance_name_th ?? "-"}}
                                                                    </td>
                                                                    <td>
                                                                        {{$value_cmi?->term_start_date ? get_date_time_by_format($value_cmi?->term_start_date, 'd/m/Y H:i') : '-'}}
                                                                    </td>
                                                                    <td>
                                                                        {{$value_cmi?->term_end_date ? get_date_time_by_format($value_cmi?->term_end_date, 'd/m/Y H:i') : '-'}}
                                                                    </td>
                                                                    <td>
                                                                        {!! badge_render(
                                           __('insurance_car.status_' . $value_cmi?->status_cmi),
                                           __('insurance_car.class_' . $value_cmi?->status_cmi),
                                       ) !!}

                                                                    </td>
                                                                    <td class="sticky-col text-center">
                                                                        <div class="btn-group">
                                                                            <div class="col-sm-12">
                                                                                <div class="dropdown dropleft">
                                                                                    <button type="button"
                                                                                            class="btn btn-sm btn-alt-secondary dropdown-toggle"
                                                                                            id="dropdown-dropleft-dark"
                                                                                            data-bs-toggle="dropdown"
                                                                                            aria-haspopup="true"
                                                                                            aria-expanded="false">
                                                                                        <i class="fa fa-ellipsis-vertical"></i>
                                                                                    </button>
                                                                                    <div class="dropdown-menu"
                                                                                         aria-labelledby="dropdown-dropleft-dark">
                                                                                        @can(Actions::View . '_' . Resources::InsuranceCar)
                                                                                            <a class="dropdown-item"
                                                                                               href="{{ route('admin.insurance-car-cmi.show', ['insurance_car_cmi' => $value_cmi]) }}"><i
                                                                                                    class="fa fa-eye me-1"></i>
                                                                                                ดูข้อมูล</a>
                                                                                        @endcan
                                                                                        @can(Actions::Manage . '_' . Resources::InsuranceCar)
                                                                                            <a class="dropdown-item"
                                                                                               href="{{ route('admin.insurance-car-cmi.edit', ['insurance_car_cmi' => $value_cmi]) }}">
                                                                                                <i class="far fa-edit me-1"></i>
                                                                                                แก้ไข
                                                                                            </a>
                                                                                        @endcan
                                                                                        @if($value_cmi->status_cmi == InsuranceCarStatusEnum::UNDER_POLICY)
                                                                                            @can(Actions::Manage . '_' . Resources::InsuranceCar)
                                                                                                <a class="dropdown-item btn-cancel-status"
                                                                                                   data-url="{{route('admin.insurance-car-cmi.request-cancel-insurance', ['insurance_car_cmi' => $value_cmi])}}">
                                                                                                    <i class="far fa-circle-xmark me-1"></i>
                                                                                                    ยกเลิก
                                                                                                </a>
                                                                                            @endcan
                                                                                        @endif
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </td>
                                                                </tr>
                                                            @endforeach
                                                        @endif
                                                        @if(!empty($value_car['vmi_data']))
                                                            @foreach($value_car['vmi_data'] as $key_vmi => $value_vmi)
                                                                {{--                                                                @dd($value_vmi?->insurance_type);--}}
                                                                <tr>
                                                                    <td class="text-center">
                                                                        <div class="form-check d-inline-block">
                                                                            @if(!empty($value_vmi?->renew_status))
                                                                                <input
                                                                                    class="form-check-input form-check-input-each car_vmi"
                                                                                    type="checkbox"
                                                                                    value=""
                                                                                    id="" name="row_checkboxes"
                                                                                    data-id="{{$value_vmi?->id}}"
                                                                                    data-type="{{InsuranceCarEnum::VMI}}"
                                                                                    data-cmi_status="{{$value_vmi?->status_cmi}}"
                                                                                    data-car_id="{{$value_car?->id}}"
                                                                                    data-license_plate="{{$value_car?->license_plate}}"
                                                                                    data-engine_no="{{$value_car?->engine_no}}"
                                                                                    data-chassis_no="{{$value_car?->chassis_no}}"
                                                                                >
                                                                            @endif
                                                                        </div>
                                                                    </td>
                                                                    <td>
                                                                        {{$value_vmi?->worksheet_no ?? "-"}}
                                                                    </td>
                                                                    <td>
                                                                        {{$value_vmi?->insurance_type ?? "-"}}
                                                                    </td>
                                                                    <td>
                                                                        {{$value_vmi?->policy_reference_vmi ?? "-"}}
                                                                    </td>
                                                                    <td>
                                                                        {{$value_vmi?->insurer?->insurance_name_th ?? "-"}}
                                                                    </td>
                                                                    <td>
                                                                        {{$value_vmi?->term_start_date ? get_date_time_by_format($value_vmi?->term_start_date, 'd/m/Y H:i') : '-'}}
                                                                    </td>
                                                                    <td>
                                                                        {{$value_vmi?->term_end_date ? get_date_time_by_format($value_vmi?->term_end_date, 'd/m/Y H:i') : '-'}}
                                                                    </td>
                                                                    <td>
                                                                        {!! badge_render(
                                           __('insurance_car.status_' . $value_vmi?->status_vmi),
                                           __('insurance_car.class_' . $value_vmi?->status_vmi),
                                       ) !!}

                                                                    </td>
                                                                    <td class="sticky-col text-center">
                                                                        <div class="btn-group">
                                                                            <div class="col-sm-12">
                                                                                <div class="dropdown dropleft">
                                                                                    <button type="button"
                                                                                            class="btn btn-sm btn-alt-secondary dropdown-toggle"
                                                                                            id="dropdown-dropleft-dark"
                                                                                            data-bs-toggle="dropdown"
                                                                                            aria-haspopup="true"
                                                                                            aria-expanded="false">
                                                                                        <i class="fa fa-ellipsis-vertical"></i>
                                                                                    </button>
                                                                                    <div class="dropdown-menu"
                                                                                         aria-labelledby="dropdown-dropleft-dark">
                                                                                        @can(Actions::View . '_' . Resources::InsuranceCar)
                                                                                            <a class="dropdown-item"
                                                                                               href="{{ route('admin.insurance-car-vmi.show', ['insurance_car_vmi' => $value_vmi]) }}"><i
                                                                                                    class="fa fa-eye me-1"></i>
                                                                                                ดูข้อมูล</a>
                                                                                        @endcan
                                                                                        @can(Actions::Manage . '_' . Resources::InsuranceCar)
                                                                                            <a class="dropdown-item"
                                                                                               href="{{ route('admin.insurance-car-vmi.edit', ['insurance_car_vmi' => $value_vmi]) }}">
                                                                                                <i class="far fa-edit me-1"></i>
                                                                                                แก้ไข
                                                                                            </a>
                                                                                        @endcan
                                                                                        @if($value_vmi->status_vmi == InsuranceCarStatusEnum::UNDER_POLICY)
                                                                                            @can(Actions::Manage . '_' . Resources::InsuranceCar)
                                                                                                <a class="dropdown-item btn-cancel-status"
                                                                                                   data-url="{{route('admin.insurance-car-vmi.request-cancel-insurance', ['insurance_car_vmi' => $value_vmi])}}">
                                                                                                    <i class="far fa-circle-xmark me-1"></i>
                                                                                                    ยกเลิก
                                                                                                </a>
                                                                                            @endcan
                                                                                        @endif

                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </td>
                                                                </tr>
                                                            @endforeach
                                                        @endif
                                                    @else
                                                        <tr>
                                                            <td class="text-center" colspan="12">"
                                                                ไม่มีรายการประกันภัย "
                                                            </td>
                                                        </tr>
                                                    @endif
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
        @endforeach
    @else

    @endif
    {!! $car_list->appends(\Request::except('page'))->render() !!}
</div>


