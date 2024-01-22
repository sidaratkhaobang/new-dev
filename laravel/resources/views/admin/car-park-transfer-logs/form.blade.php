@extends('admin.layouts.layout')

@section('page_title', $page_title)

@push('custom_styles')
    <style>
        .input-group-text {
            padding: 0.7rem .75rem;
            background-color: transparent;
            border-radius: 0;
            color: #6c757d;
        }
    </style>
@endpush

@section('content')
    <div class="block {{ __('block.styles') }}">
        <div class="block-content">
            <form id="save-form">

                <h4>{{ __('car_inout_licenses.user_open_table') }}</h4>
                <hr>
                <div class="row push mb-4">
                    <div class="col-sm-3">
                        <x-forms.input-new-line id="user_open" :value="null" :label="__('car_transfers.user_open')" />
                    </div>
                    <div class="col-sm-3">
                        <x-forms.input-new-line id="department" :value="null" :label="__('car_transfers.department')" />
                    </div>
                </div>

                <h4>{{ __('car_inout_licenses.license_table') }}</h4>
                <hr>
                <div class="row push mb-4">
                    <div class="col-sm-3">
                        <x-forms.select-option :value="null" id="license_category" :list="$license_category"
                            :label="__('car_inout_licenses.license_category')" />
                    </div>
                    <div class="col-sm-3">
                        <x-forms.input-new-line :value="null" id="remark" :label="__('car_inout_licenses.remark')" />
                    </div>
                    <div class="col-sm-3">
                        <x-forms.date-input id="expected_date" name="expected_date" :value="null" :label="__('car_inout_licenses.expected_date')"
                            :optionals="['placeholder' => __('lang.select_date')]" />
                    </div>
                    <div class="col-sm-3">
                        <label class="text-start col-form-label"
                            for="from_period_date">{{ __('car_inout_licenses.period_date') }}</label>
                        <div class="form-group">
                            <div class="input-daterange input-group" data-date-format="mm/dd/yyyy" data-week-start="1"
                                data-autoclose="true" data-today-highlight="true">
                                <input type="text" class="js-flatpickr form-control flatpickr-input"
                                    id="from_period_date" name="from_period_date" value=""
                                    placeholder="{{ __('lang.select_date') }}" data-week-start="1" data-autoclose="true"
                                    data-today-highlight="true">
                                <div class="input-group-prepend input-group-append">
                                    <span class="input-group-text font-w600">
                                        <i class="fa fa-fw fa-arrow-right"></i>
                                    </span>
                                </div>
                                <input type="text" class="js-flatpickr form-control flatpickr-input" id="to_period_date"
                                    name="to_period_date" value="" placeholder="{{ __('lang.select_date') }}"
                                    data-week-start="1" data-autoclose="true" data-today-highlight="true">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row push mb-4">
                    <div class="col-sm-3">
                        <x-forms.input-new-line id="user_car" :value="null" :label="__('car_inout_licenses.user_car')" />
                    </div>
                </div>

                <h4>{{ __('car_inout_licenses.car_table') }}</h4>
                <hr>
                <div class="row push mb-4">
                    <div class="col-sm-3">
                        <x-forms.select-option id="car_type_id" :value="null" :list="$car_type" :label="__('car_inout_licenses.car_type')" />
                    </div>
                    <div class="col-sm-3">
                        <x-forms.select-option id="car_license" :value="null" :list="null" :label="__('car_inout_licenses.car_license')" />
                    </div>
                    <div class="col-sm-3">
                        <x-forms.input-new-line id="engine_no" :value="null" :label="__('car_inout_licenses.engine_no')" />
                        {{-- <x-forms.select-option id="engine_no" :value="null" :list="null" :label="__('car_inout_licenses.engine_no')" /> --}}
                    </div>
                    <div class="col-sm-3">
                        <x-forms.input-new-line id="chassis_no" :value="null" :label="__('car_inout_licenses.chassis_no')" />
                        {{-- <x-forms.select-option id="chassis_no" :value="null" :list="null" :label="__('car_inout_licenses.chassis_no')" /> --}}
                    </div>
                </div>
                <div class="row push mb-4">
                    <div class="col-sm-3">
                        {{-- <x-forms.select-option id="car_category_id" :value="null" :list="$car_category"
                            :label="__('car_inout_licenses.car_category')" /> --}}
                        <x-forms.input-new-line id="car_category" :value="null" :label="__('car_inout_licenses.car_category')" />
                    </div>
                    <div class="col-sm-6">
                        {{-- <x-forms.select-option id="class_id" :value="null" :list="null" :label="__('car_inout_licenses.class')"
                            :optionals="['select_class' => 'js-select2-custom', 'ajax' => true]" /> --}}
                        <x-forms.input-new-line id="class" :value="null" :label="__('car_inout_licenses.class')" />
                    </div>
                    <div class="col-sm-3">
                        {{-- <x-forms.select-option id="color_id" :value="null" :list="$car_category" :label="__('car_inout_licenses.color')" /> --}}
                        <x-forms.input-new-line id="color" :value="null" :label="__('car_inout_licenses.color')" />
                    </div>
                </div>

                <h4>{{ __('car_inout_licenses.parking_table') }}</h4>
                <hr>
                <div class="row push mb-4">
                    <div class="col-sm-3">
                        <x-forms.select-option id="zone" :value="null" :list="null" :label="__('car_inout_licenses.zone')" />
                    </div>
                    <div class="col-sm-3">
                        <x-forms.select-option id="parking_slot" :value="null" :list="null" :label="__('car_inout_licenses.parking_slot')" />
                    </div>
                    <div class="col-sm-4">
                        <label class="text-start col-form-label">{{__('car_inout_licenses.download_qr')}}</label><br>
                        <a href="#">ทดสอบ.pdf</a>
                        {{-- <input id="parking_slot" :value="null" :list="null" :label="__('car_inout_licenses.download_qr')" /> --}}
                    </div>
                </div>

                {{-- @if (isset($d->audits)) --}}
                <br>
                <h4>{{ __('car_park_transfer_logs.transaction_sheet') }}</h4>
                <hr>
                <div class="mb-5">
                    <div class="table-wrap">
                        <table class="table table-striped">
                            <thead class="bg-body-dark">
                                <th>#</th>
                                <th>{{ __('car_park_transfer_logs.type') }}</th>
                                <th>{{ __('car_park_transfer_logs.datetime_log') }}</th>
                                <th>{{ __('car_park_transfer_logs.taker_car') }}</th>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>1</td>
                                    <td>นำรถเข้า</td>
                                    <td><i class="fa-solid fa-right-to-bracket" style="color:#157CF2"></i></i> 26/07/2565 12:12</td>
                                    <td>020938928 นายทดสอบ นามสมมุติ</td>
                                </tr>
                                <tr>
                                    <td>1</td>
                                    <td>นำรถออก</td>
                                    <td><i class="fa-solid fa-right-from-bracket"  style="color: #E04F1A "></i> 26/07/2565 12:12</td>
                                    <td>020938928 นายทดสอบ นามสมมุติ</td>
                                </tr>
                                {{-- @php $i = 1; @endphp
                                @foreach ($d->audits->reverse() as $index => $audit)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $audit->user ? $audit->user->name : '' }}</td>
                                        <td>{{ get_thai_date_format($audit->created_at, 'd/m/Y H:i') }}</td>
                                        <td>
                                            @foreach ($audit->getModified() as $attribute => $modified)
                                                @if (strcmp($attribute, 'status') === 0 && $modified['new'] == \App\Enums\PRStatusEnum::CONFIRM)
                                                    {{ __('purchase_requisitions.status_' . \App\Enums\PRStatusEnum::CONFIRM . '_text') }}
                                                @endif
                                                @if (strcmp($attribute, 'status') === 0 && $modified['new'] == \App\Enums\PRStatusEnum::REJECT)
                                                    {{ __('purchase_requisitions.status_' . \App\Enums\PRStatusEnum::REJECT . '_text') }}
                                                @endif
                                                @if (strcmp($attribute, 'status') === 0 && $modified['new'] == \App\Enums\PRStatusEnum::CANCEL)
                                                    {{ __('purchase_requisitions.status_' . \App\Enums\PRStatusEnum::CANCEL . '_text') }}
                                                @endif
                                            @endforeach
                                        </td>
                                    </tr>
                                    @php $i += 1; @endphp
                                @endforeach --}}
                            </tbody>
                        </table>
                    </div>
                </div>
                {{-- @endif --}}


                {{-- <x-forms.hidden id="id" :value="$d->id" /> --}}
                <div class="row push">
                    <div class="col-sm-12 text-end">
                        <a class="btn btn-secondary"
                            href="{{ route('admin.car-in-out-logs.index') }}">{{ __('lang.back') }}</a>
                    
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@include('admin.components.select2-default')
@include('admin.components.sweetalert')
@include('admin.components.date-input-script')

@include('admin.components.select2-ajax', [
    'id' => 'class_id',
    'url' => route('admin.util.select2.car-class'),
])

@include('admin.components.select2-ajax', [
    'id' => 'color_id',
    'url' => route('admin.util.select2.car-class-colors'),
    'parent_id' => 'class_id',
])

@push('scripts')
    <script>
        $('#user_open').prop('readonly', true);
        $('#department').prop('readonly', true);
        $('#car_category').prop('readonly', true);
        $('#class').prop('readonly', true);
        $('#color').prop('readonly', true);
        $('#user_car').prop('readonly', true);
        $('#engine_no').prop('readonly', true);
        $('#chassis_no').prop('readonly', true);
        $('#car_type_id').prop('disabled', true);
        $('#license_category').prop('disabled', true);
        $('#car_license').prop('disabled', true);
        $('#zone').prop('disabled', true);
        $('#parking_slot').prop('disabled', true);
        $('#remark').prop('readonly', true);
        $('#expected_date').prop('disabled', true);
        $('#from_period_date').prop('disabled', true);
        $('#to_period_date').prop('disabled', true);
        // $('#expected_date').css("background-color", "#e9ecef");

    </script>
@endpush
