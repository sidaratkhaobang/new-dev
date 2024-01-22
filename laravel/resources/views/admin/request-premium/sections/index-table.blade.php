<div class="block {{ __('block.styles') }}">
    @include('admin.components.block-header',[
    'text' =>   __('transfer_cars.total_items')    ,
   'block_icon_class' => 'icon-document',
])
    <div class="block-content">
        <div class="table-wrap db-scroll">
            <table class="table table-striped table-vcenter">
                <thead class="bg-body-dark">
                <tr>
                    <th>@sortablelink('getLongTermRental.worksheet_no', __('request_premium.longterm_rental_number'))</th>
                    <th>@sortablelink('getLongTermRental.job_type', __('request_premium.job_type'))</th>
                    <th>{{__('request_premium.rental_duration')}}</th>
                    <th>@sortablelink('getLongTermRental.customer_email', __('insurances.insurance_email'))</th>
                    <th>@sortablelink('getLongTermRental.customer_name', __('request_premium.customer'))</th>
                    <th>@sortablelink('status', __('request_premium.status'))</th>
                    <th></th>
                </tr>
                </thead>
                <tbody>

                @if(!$premium_list->isEmpty())
                    @foreach($premium_list as $key_premium_list => $value_premium_list)
                        <tr>

                        </tr>
                        <td>
                            {{$value_premium_list?->getLongTermRental?->worksheet_no ?: '-'}}
                        </td>
                        <td>
                            {{$value_premium_list?->getLongTermRental?->rentalType?->name ?: '-'}}
                        </td>
                        <td>
                            {{$value_premium_list->month}}
                        </td>
                        <td>
                            {{$value_premium_list?->getLongTermRental?->customer_email ?: '-'}}
                        </td>
                        <td>
                            {{$value_premium_list?->getLongTermRental?->customer_name ?: '-'}}
                        </td>
                        <td>

                            {!! badge_render(
                                              __('request_premium.request_premium_status_class_' . $value_premium_list->status),
                                              __('request_premium.request_premium_status_' . $value_premium_list->status),
                                          ) !!}
                        </td>
                        <td class="sticky-col text-center">
                            @include('admin.components.dropdown-action', $value_premium_list?->dropdown )
                        </td>
                    @endforeach
                @else
                    <tr>
                        <td class="text-center" colspan="12">" {{__('lang.no_list')}} "</td>
                    </tr>
                @endif
                </tbody>
            </table>
        </div>
        {!! $premium_list->appends(\Request::except('page'))->render() !!}
    </div>
</div>
