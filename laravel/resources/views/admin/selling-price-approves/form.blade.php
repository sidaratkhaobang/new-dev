@extends('admin.layouts.layout')
@section('page_title', $page_title . ' ' . $d->worksheet_no)
@section('page_title_sub')
    @if (isset($d->status))
        {!! badge_render(__('selling_prices.class_' . $d->status), __('selling_prices.status_' . $d->status)) !!}
    @endif
@endsection
@push('styles')
    <style>
        .profile-image {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            overflow: hidden;
        }

        .profile-image img {
            width: 10%;
            height: 10%;
            object-fit: cover;
        }

        .form-progress-bar .form-progress-bar-header {
            text-align: left;

        }

        .form-progress-bar .form-progress-bar-steps {
            margin: 30px 0 10px 0;
        }

        div.check-status {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100%;
            flex-direction: column;
        }

        .form-progress-bar .form-progress-bar-steps li,
        .form-progress-bar .form-progress-bar-labels li {
            width: 16.6%;
            float: left;
            position: relative;
        }

        .form-progress-bar-line {
            background-color: #f3f3f3;
            content: "";
            height: 2px;
            left: 0;
            right: 0;
            top: 50%;
            transform: translateY(-50%);
            border-bottom: 1px solid #dddddd;
            border-top: 1px solid #dddddd;
            margin-left: 20px;
            margin-right: 30px;
        }

        .form-progress-bar .form-progress-bar-steps span.check {
            background-color: #dddddd;
            border-radius: 50%;
            display: inline-block;
            height: 40px;
            line-height: 40px;
            position: relative;
            text-align: center;
            width: 40px;
        }

        .form-progress-bar .form-progress-bar-steps span.pending {
            background-color: #dddddd;
            border-radius: 50%;
            display: inline-block;
            height: 40px;
            line-height: 40px;
            position: relative;
            text-align: center;
            width: 40px;
        }

        .form-progress-bar .form-progress-bar-steps span.pending-secondary {
            background-color: #dddddd;
            border-radius: 50%;
            display: inline-block;
            height: 40px;
            line-height: 40px;
            position: relative;
            text-align: center;
            width: 40px;
        }

        .form-progress-bar .form-progress-bar-steps span.reject {
            background-color: #dddddd;
            border-radius: 50%;
            display: inline-block;
            height: 40px;
            line-height: 40px;
            position: relative;
            text-align: center;
            width: 40px;
        }

        .form-progress-bar .form-progress-bar-steps span.check,
        .form-progress-bar .form-progress-bar-steps span.check {
            background-color: #6f9c40;
            color: #ffffff;
        }

        .form-progress-bar .form-progress-bar-steps span.pending,
        .form-progress-bar .form-progress-bar-steps span.pending {
            background-color: #e69f17;
            color: #ffffff;
        }

        .form-progress-bar .form-progress-bar-steps span.pending-secondary,
        .form-progress-bar .form-progress-bar-steps span.pending-secondary {
            background-color: #909395;
            color: #ffffff;
        }

        .form-progress-bar .form-progress-bar-steps span.reject,
        .form-progress-bar .form-progress-bar-steps span.reject {
            background-color: red;
            color: #ffffff;
        }

        .bg-pending-previous {
            background-color: #909395;
        }

        .bg-check {
            background-color: #6f9c40;
        }

        .bg-pending {
            background-color: #e69f17;
        }

        .table-bordered-custom thead,
        .table-bordered-custom tbody,
        .table-bordered-custom tfoot,
        .table-bordered-custom tr,
        .table-bordered-custom td,
        .table-bordered-custom th {
            border: 1px solid #cbd4e1 !important;
        }
    </style>
@endpush
@section('content')
    <form id="save-form">
        @include('admin.components.creator')
        {{-- @if (isset($approve_line_list) && $approve_line_list)
            @include('admin.components.step-progress')
        @endif --}}
        <x-approve.step-approve :configenum="ConfigApproveTypeEnum::SELLING_PRICE" :id="$d->id" :model="get_class($d)" />
        <div class="block {{ __('block.styles') }}">
            <div class="block-content">
                <div class="table-wrap db-scroll">
                    <table class="table table-striped table-vcenter">
                        <thead class="bg-body-dark">
                            <tr>
                                <th style="width: 10%;">{{ __('cars.license_plate') }}</th>
                                <th style="width: 10%;">{{ __('gps.car_class') }}</th>
                                <th style="width: 10%;">{{ __('cars.chassis_no') }}</th>
                                <th style="width: 10%;">{{ __('cars.engine_no') }}</th>
                                <th style="width: 10%;">{{ __('car_classes.manufacturing_year') }}</th>
                                <th style="width: 10%;">{{ __('selling_prices.car_color') }}</th>
                                <th style="width: 10%;">{{ __('cars.registration_date') }}</th>
                                <th style="width: 10%;">{{ __('selling_prices.ownership') }}</th>
                                <th style="width: 10%;">{{ __('selling_prices.mileage') }}</th>
                                <th class="text-end">{{ __('selling_prices.price') }}</th>
                                <th class="text-end">{{ __('selling_prices.vat') }}</th>
                                <th class="text-end">{{ __('selling_prices.total') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($selling_price_line as $item)
                                <tr>
                                    <td>{{ $item->license_plate }}</td>
                                    <td>{{ $item->car_class_name }}</td>
                                    <td>{{ $item->chassis_no }}</td>
                                    <td>{{ $item->engine_no }}</td>
                                    <td>{{ $item->manufacturing_year }}</td>
                                    <td>{{ $item->car_color }}</td>
                                    <td>{{ $item->registration_date }}</td>
                                    <td>{{ $item->ownership }}</td>
                                    <td>{{ $item->mileage }}</td>
                                    <td class="text-end">{{ number_format($item->selling_price_line_price, 2, '.', ',') }}
                                    <td class="text-end">{{ number_format($item->selling_price_line_vat, 2, '.', ',') }}
                                    <td class="text-end">{{ number_format($item->selling_price_line_total, 2, '.', ',') }}
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                {!! $selling_price_line->appends(\Request::except('page'))->render() !!}
            </div>
        </div>
        <div class="block {{ __('block.styles') }}">
            <div class="block-content">
                <x-forms.hidden id="id" :value="$d->id" />
                @if ($approve_line_owner)
                    <x-forms.hidden id="approve_line_id" :value="$approve_line_owner->id" />
                @endif
                <div class="row push">
                    <div class="col-sm-12 text-end">
                        <a class="btn btn-secondary"
                            href="{{ route('admin.selling-price-approves.index') }}">{{ __('lang.back') }}</a>
                        @if ($approve_line_owner)
                            <button type="button" class="btn btn-danger btn-reject-status"
                                data-status="{{ SellingPriceStatusEnum::REJECT }}">{{ __('lang.disapprove') }}
                            </button>
                            <button type="button" class="btn btn-primary btn-approve-status"
                                data-status="{{ SellingPriceStatusEnum::CONFIRM }}">{{ __('purchase_requisitions.approved') }}</button>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </form>
@endsection

@include('admin.components.sweetalert')
@include('admin.components.form-save', [
    'store_uri' => route('admin.selling-price-approves.store'),
])
@push('scripts')
    <script>
        $(".btn-approve-status").on("click", function() {
            let storeUri = "{{ route('admin.selling-price-approves.store') }}";
            var formData = new FormData(document.querySelector('#save-form'));
            var status = $(this).attr('data-status');
            var approve_line_id = document.getElementById("approve_line_id").value;
            formData.append('approve_line_id', approve_line_id);
            formData.append('status_update', status);

            mySwal.fire({
                title: "อนุมัติทำราคาขายรถล่วงหน้า",
                html: 'เมื่อยืนยันอนุมัติทำราคาขายรถล่วงหน้าแล้ว ระบบจะส่งข้อมูลคำขอนี้เพื่อดำเนินการในส่วนต่อไป',
                icon: 'warning',
                customClass: {
                    confirmButton: 'btn btn-primary m-1',
                    cancelButton: 'btn btn-secondary m-1'
                },
                showCancelButton: true,
                confirmButtonText: "{{ __('lang.confirm') }}",
                cancelButtonText: "{{ __('lang.cancel') }}",
                preConfirm: e => {
                    return new Promise(resolve => {
                        setTimeout(() => {
                            resolve();
                        }, 50);
                    });
                }
            }).then((result) => {
                if (result.value) {
                    saveForm(storeUri, formData);
                }
            })
        });

        $(".btn-reject-status").on("click", function() {
            let storeUri = "{{ route('admin.selling-price-approves.store') }}";
            var formData = new FormData(document.querySelector('#save-form'));
            var status = $(this).attr('data-status');
            var approve_line_id = document.getElementById("approve_line_id").value;
            formData.append('approve_line_id', approve_line_id);
            formData.append('status_update', status);

            mySwal.fire({
                title: "ไม่อนุมัติทำราคาขายรถล่วงหน้า",
                html: 'เหตุผลการไม่อนุมัติ ',
                input: 'text',
                icon: 'warning',
                customClass: {
                    confirmButton: 'btn btn-danger m-1',
                    cancelButton: 'btn btn-secondary m-1'
                },
                showCancelButton: true,
                confirmButtonText: "{{ __('lang.confirm') }}",
                cancelButtonText: "{{ __('lang.cancel') }}",
                preConfirm: e => {
                    return new Promise(resolve => {
                        setTimeout(() => {
                            resolve();
                        }, 50);
                    });
                }
            }).then((result) => {
                var reject_reason = '';
                if (result.value) {
                    reject_reason = result.value;
                }
                formData.append('reject_reason', reject_reason);
                saveForm(storeUri, formData);
            })
        });
    </script>
@endpush
