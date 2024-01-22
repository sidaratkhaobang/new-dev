<div class="table-wrap db-scroll">
    <table class="table table-striped table-vcenter">
        <thead class="bg-body-dark">
            <tr>
                <th style="width: 5px;">#</th>
                <th>@sortablelink('seq', __('inspection_cars.inspection_seq'))</th>
                <th>@sortablelink('seq', __('inspection_cars.worksheet_type'))</th>
                <th>@sortablelink('seq', __('inspection_cars.department'))</th>
                <th>@sortablelink('seq', __('inspection_cars.inspector'))</th>
                <th>@sortablelink('seq', __('inspection_cars.inspection_place'))</th>
                <th>@sortablelink('seq', __('inspection_cars.inspection_date'))</th>
                <th>@sortablelink('seq', __('inspection_cars.status'))</th>
                <th style="width: 5px;" ></th>
            </tr>
        </thead>
        @if (count($list) > 0)
            <tbody id="step_table">
                @foreach ($list as $index => $d)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ __('car_inspection_types.status_condition_name_' . $d->transfer_reason) }}
                        </td>
                        <td>{{ $d->InspectionForm ? $d->InspectionForm->name : '' }}</td>
                        <td>{{ $d->UserDepartment ? $d->UserDepartment->name : '' }}</td>
                        <td>
                            @if ($d->UserInspector || $d->UserInspectorDriver)
                                {{ $d->UserInspector ? $d->UserInspector->name : $d->UserInspectorDriver->name }}
                            @else
                                {{ $d->inspector_fullname }}
                            @endif
                        </td>
                        <td>{{ $d->inspection_location }}</td>
                        <td>{{ $d->inspection_date ? get_thai_date_format($d->inspection_date, 'd/m/Y') : '' }}
                        </td>
                        <td>
                            @if ($d->inspection_status == InspectionStatusEnum::NOT_PASS)
                                {!! badge_render(
                                    __('inspection_cars.class_' . $d->inspection_status),
                                    __('inspection_cars.status_' . $d->inspection_status) .
                                        ' (' .
                                        __('inspection_cars.remark_reason_' . $d->remark_reason) .
                                        ')',
                                ) !!}
                            @else
                                {!! badge_render(
                                    __('inspection_cars.class_' . $d->inspection_status),
                                    __('inspection_cars.status_' . $d->inspection_status),
                                ) !!}
                            @endif
                        </td>
                        <td class="sticky-col text-center">
                            <div class="dropdown dropleft">
                                <button type="button" class="btn btn-sm btn-alt-secondary dropdown-toggle"
                                    id="dropdown-dropleft-dark" data-bs-toggle="dropdown"
                                    aria-haspopup="true" aria-expanded="false">
                                    <i class="fa fa-ellipsis-vertical"></i>
                                </button>
                                <div class="dropdown-menu" aria-labelledby="dropdown-dropleft-dark">
                                    @can(Actions::View . '_' . Resources::CarInspection)
                                        <a class="dropdown-item"
                                            href="{{ route('admin.inspection-job-step-forms.show', ['inspection_job_step_form' => $d->InspectionForm->id, 'job_id' => $d->inspection_job_id, 'job_step_id' => $d->id]) }}"><i
                                                class="fa fa-eye me-1"></i>
                                            {{ __('car_inspections.view') }}
                                        </a>
                                    @endcan
                                    @if (!isset($show) && (($d->inspection_status != InspectionStatusEnum::PASS) && (($d->inspection_status != InspectionStatusEnum::NOT_PASS) || ($d->remark_reason != InspectionRemarkEnum::CHANGE_CAR))))
                                        @if ($d->inspection_status != InspectionStatusEnum::PASS)
                                            @can(Actions::Manage . '_' . Resources::CarInspection)
                                                <a class="dropdown-item"
                                                    href="{{ route('admin.inspection-job-step-forms.edit', ['inspection_job_step_form' => $d->InspectionForm->id, 'job_id' => $d->inspection_job_id, 'job_step_id' => $d->id]) }}"><i
                                                        class="far fa-edit me-1"></i>
                                                    {{ __('car_inspections.edit') }}
                                                </a>
                                            @endcan
                                        @endif
                                    @endif
                                </div>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        @else
            <tbody>
                <tr class="table-empty">
                    <td class="text-center" colspan="8">{{ __('lang.no_list') }}</td>
                </tr>
            </tbody>
        @endif
    </table>
</div>