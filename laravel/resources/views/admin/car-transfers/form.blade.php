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

                {{-- user open --}}
                <h4>{{ __('car_transfers.user_table') }}</h4>
                <hr>
                <div class="row push mb-4">
                    <div class="col-sm-3">
                        <x-forms.input-new-line id="user_open" :value="get_user_name()" :label="__('car_transfers.user_open')" />
                    </div>
                    <div class="col-sm-3">
                        <x-forms.input-new-line id="department" :value="get_department_name()" :label="__('car_transfers.department')" />
                    </div>
                </div>
                {{-- end user open --}}

                {{-- license --}}
                <h4>{{ __('car_transfers.license_table') }}</h4>
                <hr>
                <div class="row push mb-4">
                    <div class="col-sm-3">
                        <x-forms.select-option :value="null" id="license_category" :list="$license_category"
                            :label="__('car_transfers.license_category')" />
                    </div>
                    <div class="col-sm-6">
                        <x-forms.input-new-line :value="null" id="remark" :label="__('car_transfers.remark')" />
                    </div>
                    <div class="col-sm-3">
                        <x-forms.input-new-line id="open_date" name="open_date" :value="$d->open_date" :label="__('car_transfers.open_date')" />
                    </div>
                </div>
                {{-- end license --}}

                {{-- car --}}
                <h4>{{ __('car_transfers.car_table') }}</h4>
                <hr>
                <div class="row push mb-4">
                    <div class="col-sm-3">
                        <x-forms.select-option id="car_license" :value="null" :list="null" :label="__('car_transfers.car_license')" />
                    </div>
                    <div class="col-sm-3">
                        <x-forms.select-option id="rental_type" :value="null" :list="$rental_type_list" :label="__('car_transfers.rental_type')" />
                    </div>
                    <div class="col-sm-3">
                        <x-forms.input-new-line id="engine_no" :value="null" :label="__('car_transfers.engine_no')" />
                        {{-- <x-forms.select-option id="engine_no" :value="null" :list="null" :label="__('car_transfers.engine_no')" /> --}}
                    </div>
                    <div class="col-sm-3">
                        <x-forms.input-new-line id="chassis_no" :value="null" :label="__('car_transfers.chassis_no')" />
                        {{-- <x-forms.select-option id="chassis_no" :value="null" :list="null" :label="__('car_transfers.chassis_no')" /> --}}
                    </div>
                </div>
                <div class="row push mb-4">
                    <div class="col-sm-3">
                        {{-- <x-forms.select-option id="car_category_id" :value="null" :list="$car_category"
                            :label="__('car_transfers.car_category')" /> --}}
                        <x-forms.input-new-line id="car_category" :value="null" :label="__('car_transfers.car_category')" />
                    </div>
                    <div class="col-sm-6">
                        {{-- <x-forms.select-option id="class_id" :value="null" :list="null" :label="__('car_transfers.class')"
                            :optionals="['select_class' => 'js-select2-custom', 'ajax' => true]" /> --}}
                        <x-forms.input-new-line id="class" :value="null" :label="__('car_transfers.class')" />
                    </div>
                    <div class="col-sm-3">
                        {{-- <x-forms.select-option id="color_id" :value="null" :list="$car_category" :label="__('car_transfers.color')" /> --}}
                        <x-forms.input-new-line id="color" :value="null" :label="__('car_transfers.color')" />
                    </div>
                </div>
                <div class="row push mb-4">
                    <div class="col-sm-3">
                        <x-forms.input-new-line id="key_storage" :value="null" :label="__('car_transfers.key_storage')" />
                    </div>
                    <div class="col-sm-3">
                        <x-forms.input-new-line id="zone_old" :value="null" :label="__('car_transfers.zone')" />
                    </div>
                    <div class="col-sm-3">
                        <x-forms.input-new-line id="parking_slot_old" :value="null" :label="__('car_transfers.parking_slot')" />
                    </div>
                </div>
                {{-- end car --}}

                {{-- parking --}}
                {{-- parking_1 --}}
                <div class="row" id="parking_1" style="display: none">
                    <div class="col-12">
                        <h4>{{ __('car_transfers.transfer_table') }}</h4>
                        <hr>
                        <div class="row mb-4">
                            <div class="col-sm-3">
                                <x-forms.select-option id="zone_new" :value="null" :list="null"
                                    :label="__('car_transfers.zone')" />
                            </div>
                            <div class="col-sm-3">
                                <x-forms.select-option id="parking_slot_new" :value="null" :list="null"
                                    :label="__('car_transfers.parking_slot')" />
                            </div>
                            <div class="col-sm-3">
                                <label class="text-start col-form-label">{{ __('car_transfers.download_qr') }}
                                </label>
                                {{-- <p>{{ __('car_transfers.no_data') }}</p> --}}
                            </div>
                        </div>
                        <p>หมายเหตุ : หากไม่ระบุช่องจอด ระบบจะทำการจองช่องจอดให้
                            เมื่อนำรถผ่านเข้าคลัง</p>
                    </div>
                </div>
                {{-- parking_2 --}}
                <div class="row" id="parking_2" style="display: none">
                    <div class="col-12">
                        <h4>{{ __('car_transfers.transfer_table') }}</h4>
                        <hr>
                        <div class="row mb-4" id="move_2">
                            <div class="col-sm-3">
                                <x-forms.input-new-line id="location" :value="null" :label="__('car_transfers.location')" />
                            </div>
                            <div class="col-sm-6">
                                <x-forms.input-new-line id="detail" :value="null" :label="__('car_transfers.detail')" />
                            </div>
                            <div class="col-sm-3">
                                <label class="text-start col-form-label">{{ __('car_transfers.download_qr') }}
                                </label>
                                {{-- <p>{{ __('car_transfers.no_data') }}</p> --}}
                            </div>
                        </div>
                        <p>หมายเหตุ : หากไม่ระบุช่องจอด ระบบจะทำการจองช่องจอดให้
                            เมื่อนำรถผ่านเข้าคลัง</p>
                    </div>
                </div>
                {{-- end parking --}}


                <x-forms.hidden id="id" :value="$d->id" />
                <div class="row push">
                    <div class="col-sm-12 text-end">
                        <a class="btn btn-secondary"
                            href="{{ route('admin.car-transfers.index') }}">{{ __('lang.back') }}</a>
                        <button type="button" class="btn btn-primary btn-save-form">{{ __('lang.save_draft') }}</button>
                        <button type="button" class="btn btn-info btn-show-confirm-modal"
                            data-status="">{{ __('lang.save') }}</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    @include('admin.car-transfers.modals.confirm-modal')
@endsection

@include('admin.components.select2-default')
@include('admin.components.sweetalert')
@include('admin.components.date-input-script')
@include('admin.components.form-save', [
    'store_uri' => route('admin.car-transfers.store'),
])

{{-- @include('admin.components.select2-ajax', [
    'id' => 'class_id',
    'url' => route('admin.util.select2.car-class'),
]) --}}

{{-- @include('admin.components.select2-ajax', [
    'id' => 'color_id',
    'url' => route('admin.util.select2.car-class-colors'),
    'parent_id' => 'class_id',
]) --}}

@push('scripts')
    <script>
        $('#user_open').prop('readonly', true);
        $('#department').prop('readonly', true);
        $('#open_date').prop('readonly', true);
        $('#car_category').prop('readonly', true);
        $('#class').prop('readonly', true);
        $('#color').prop('readonly', true);
        $('#engine_no').prop('readonly', true);
        $('#chassis_no').prop('readonly', true);
        $('#key_storage').prop('readonly', true);
        $('#zone_old').prop('readonly', true);
        $('#parking_slot_old').prop('readonly', true);

        $(".btn-show-confirm-modal").on("click", function() {
            $('#modal-confirm').modal('show');
        });

        var parking_1 = document.getElementById("parking_1");
        var parking_2 = document.getElementById("parking_2");

        $('#license_category').on('select2:select', function() {
            var x = document.getElementById("license_category").value;
            if (x === '1') {
                parking_1.style.display = "block"
                parking_2.style.display = "none"
            } else if (x === '2') {
                parking_1.style.display = "none"
                parking_2.style.display = "block"
            }
        });
    </script>
@endpush
