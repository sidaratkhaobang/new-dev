<div class="block {{ __('block.styles') }}">
    @include('admin.components.block-header', [
        'text' => __('long_term_rentals.approval_info'),
        'block_icon_class' => 'icon-document',
        'block_option_id' => '_btn',
    ])
    <div class="block-content">
        <div class="block {{ __('block.styles') }}">
            <div class="block-content">
                <ul class="flex-container space-between">
                    <li class="flex-item">
                        <p>{{ __('long_term_rentals.job_type') }}</p>
                        <p style="margin-bottom: 0px;" class="text-p">
                            {{ __('long_term_rentals.job_type_' . $d->job_type) }}</p>
                    </li>
                    <div class="block-options-item seperator">|</div>
                    <li class="flex-item">
                        <p>{{ __('long_term_rentals.customer_type') }}</p>
                        <p style="margin-bottom: 0px;" class="text-p">
                            {{ __('customers.type_' . $d->customer_type) }}</p>
                    </li>
                    <div class="block-options-item seperator">|</div>
                    <li class="flex-item">
                        <p>{{ __('long_term_rentals.customer') }}</p>
                        <p style="margin-bottom: 0px;" class="text-p">{{ $d->customer_name }}</p>
                    </li>
                    <div class="block-options-item seperator">|</div>
                    <li class="flex-item">
                        <p>{{ __('long_term_rentals.rental_time') }}</p>
                        @foreach ($month_list as $item_time)
                            <span class="badge badge-customs badge-bg-primary-custom">
                                {{ $item_time->name }}
                            </span>
                        @endforeach
                    </li>
                    <div class="block-options-item seperator">|</div>
                    <li class="flex-item">
                        <p>{{ __('long_term_rentals.offer_date') }}</p>
                        <p style="margin-bottom: 0px;" class="text-p">{{ $d->offer_date }}</p>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>
