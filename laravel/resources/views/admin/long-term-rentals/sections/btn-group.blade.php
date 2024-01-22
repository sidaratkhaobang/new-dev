<div class="row items-push mb-4">
    <div class="col-sm-6">
        <div class="btn-group" role="group">
            <a type="button" href="{{ $route_group['route_lt_rental'] ?? '' }}"
                class="btn btn-outline-primary
                {{ in_array(Route::currentRouteName(), ['admin.long-term-rentals.edit', 'admin.long-term-rentals.show', 'admin.long-term-rentals.create']) ? 'active' : '' }}">
                {{ __('long_term_rentals.lt_detail') }}
            </a>

            {{--            @if ($allow_confirm) --}}
            {{--                @if ($d->rentalType && $d->rentalType->job_type && strcmp($d->rentalType->job_type, LongTermRentalJobType::BUDGET) !== 0) --}}
            <a type="button" id="btn-pr-line" href="{{ $route_group['route_pr_line'] ?? '' }}"
                class="btn btn-outline-primary pe-none
                {{ in_array(Route::currentRouteName(), ['admin.long-term-rentals.pr-lines.edit', 'admin.long-term-rentals.pr-lines.show']) ? 'active' : '' }}">
                {{ __('long_term_rentals.approval_info') }}
            </a>
            {{--                @endif --}}
            {{--            @endif --}}
            {{--            @if ($d->status == \App\Enums\LongTermRentalStatusEnum::COMPLETE) --}}
            <a type="button" id="btn-car-info" href="{{ $route_group['route_car_contract'] ?? '' }}"
                class="btn btn-outline-primary pe-none
                {{ in_array(Route::currentRouteName(), ['admin.long-term-rentals.car-info-and-deliver.edit', 'admin.long-term-rentals.car-info-and-deliver.show', 'admin.long-term-rentals.car-info-and-deliver.create']) ? 'active' : '' }}">
                {{ __('ข้อมูลรถและการส่งมอบ') }}
            </a>
            {{--            @endif --}}
        </div>
    </div>
</div>

@push('scripts')
    <script>
        $allow_confirm = @json($allow_confirm);
        var rentalType = "{{ $d->rentalType }}";
        var longrentalJobType =
            "{{ !empty($d->rentalType->job_type) ? strcmp($d->rentalType->job_type, LongTermRentalJobType::BUDGET) : null }}";
        var rentalJobType = "{{ !empty($d->rentalType->job_type) ? $d->rentalType->job_type : null }}";
        var statusCarInfo = "{{ $d->status == \App\Enums\LongTermRentalStatusEnum::COMPLETE }}";
        if ($allow_confirm == true) {

            if (rentalType && rentalJobType && longrentalJobType != 0) {
                $('#btn-pr-line').removeClass('pe-none');
            }
        }
        if (statusCarInfo) {
            $('#btn-car-info').removeClass('pe-none');
        }
    </script>
@endpush
