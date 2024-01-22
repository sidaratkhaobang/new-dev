<div id="index-table" class="block {{ __('block.styles') }}">
    @include('admin.components.block-header', [
           'text' => __('lang.total_list'),
       ])
    <div class="block-content">
        <div class="justify-content-between mb-4">
            <div class="block-content">
                <div class="justify-content-between mb-4">
                    <div class="table-wrap db-scroll">
                        <table class="table table-striped table-vcenter">
                            <thead class="bg-body-dark">
                            <th>
                                @sortablelink('worksheet_no', __('insurance_deduct.accident_worksheet_no'))
                            </th>
                            <th>
                                {{__('insurance_deduct.insurance_company')}}
                            </th>
                            <th>
                                @sortablelink('car.license_plate', __('insurance_deduct.license_plate'))
                            </th>
                            <th>
                                @sortablelink('car.chassis_no', __('insurance_deduct.chassis_no'))
                            </th>
                            <th>
                                {{__('insurance_deduct.policy_number_no')}}
                            </th>
                            <th>
                                @sortablelink('claim_no', __('insurance_deduct.claim_no'))
                            </th>
                            <th>
                                {{__('insurance_deduct.customer_group')}}
                            </th>
                            <th>
                                {{__('insurance_deduct.customer')}}
                            </th>
                            <th>
                                @sortablelink('accident_date', __('insurance_deduct.accident_datetime'))
                            </th>
                            <th>
                            </th>
                            </thead>
                            <tbody>
                            @if(!empty($listAccident->count()))
                                @foreach($listAccident as $keyAccident => $valueAccident)
                                    <tr>
                                        <td class="text-center">
                                            {{ $valueAccident?->worksheet_no ?? "-" }}
                                        </td>
                                        <td class="text-center">
                                            {{ $valueAccident?->car?->vmi[0]?->insurer?->insurance_name_th ?? "-" }}
                                        </td>
                                        <td class="text-center">
                                            {{ $valueAccident?->license_plate ?? "-" }}
                                        </td>
                                        <td class="text-center">
                                            {{ $valueAccident?->chassis_no ?? "-" }}
                                        </td>
                                        <td class="text-center">
                                            {{ $valueAccident?->car?->vmi[0]?->policy_reference_vmi ?? "-" }}
                                        </td>
                                        <td class="text-center">
                                            {{ $valueAccident?->claim_no ?? "-" }}
                                        </td>
                                        <td class="text-center">
                                            {{ $valueAccident?->customer_group ?? "-" }}
                                        </td>
                                        <td class="text-center">
                                            {{ $valueAccident?->customer_name ?? "-" }}
                                        </td>
                                        <td class="text-center">
                                            {{ $valueAccident?->accident_date ?? "-" }}
                                        </td>
                                        <td class="text-center">
                                            @include('admin.components.dropdown-action',
                                                [
                                                    'view_route' => route('admin.insurance-deducts.show', ['insurance_deduct' => $valueAccident]),
                                                    'edit_route' => route('admin.insurance-deducts.edit', ['insurance_deduct' => $valueAccident]),
                                                    'view_permission' => Actions::View . '_' . Resources::InsuranceDeduct,
                                                    'manage_permission' => Actions::Manage . '_' . Resources::InsuranceDeduct,
                                                ]
                                             )
                                        </td>
                                    </tr>
                                @endforeach
                            @else
                                <tr class="table-empty">
                                    <td class="text-center" colspan="12">
                                        " {{__('lang.no_list')}} "
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
