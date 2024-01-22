<div class="block {{ __('block.styles') }} {{ ($show ? '' : 'block-mode-hidden') }}">
    <x-blocks.block-header-step :title="__('short_term_rentals.step_title.summary')" :step="7" :success="$success" :optionals="['block_icon_class' => __('short_term_rentals.step_icon.summary'), 'is_toggle' => $istoggle, 'showstep' => $showstep]" />
    @if($success)
    <div class="block-content pt-0">
        <div class="table-wrap db-scroll">
            <table class="table table-striped table-vcenter">
                <thead class="bg-body-dark">
                    <tr>
                        <th style="width: 1px;">#</th>
                        <th>{{ __('short_term_rentals.bill_list') }}</th>
                        <th class="text-end" style="width: 20%;">{{ __('short_term_rentals.total') }}</th>
                        <th class="text-center">{{ __('short_term_rentals.quotation') }}</th>
                        <th class="text-center">{{ __('short_term_rentals.2c2p_link') }}</th>
                        <th class="text-center" style="width: 10px;">{{ __('lang.status') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($rental_bills as $index => $rental_bill)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ __('short_term_rentals.bill_' . $rental_bill->bill_type) }}</td>
                        <td class="text-end">{{ number_format($rental_bill->total, 2) }}</td>
                        <td class="text-center">
                            @if (!in_array($rental_bill->status, [RentalStatusEnum::DRAFT]))
                            <a class="btn btn-primary" href="{{ route('admin.quotations.pdf', ['id' => $rental_bill->quotation_id]) }}" target="_blank">
                                {{ $rental_bill->qt_no }}
                            </a>
                            @else
                            -
                            @endif
                        </td>
                        <td class="text-center">
                            @if ($rental_bill->payment_url && !in_array($rental_bill->status, [RentalStatusEnum::PAID]))
                            <a href="{{ $rental_bill->payment_url }}" target="_blank">
                                <i class="fa fa-arrow-up-right-from-square"></i> Link
                            </a>
                            @else
                            -
                            @endif
                        </td>
                        <td class="text-center">
                            {!! badge_render(__('short_term_rentals.class_' . $rental_bill->status), __('short_term_rentals.status_' . $rental_bill->status)) !!}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif
</div>
