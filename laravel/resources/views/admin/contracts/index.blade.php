@extends('admin.layouts.layout')
@section('page_title', __('menu.contract.sub.contract_list'))
@push('custom_styles')
    <meta name="csrf-token" content="{{ csrf_token() }}">
@endpush
@section('content')
    <div class="block {{ __('block.styles') }}">
        @include('admin.components.block-header',[
            'text' =>   __('lang.search')    ,
            'block_icon_class' => 'icon-search',
            'is_toggle' => true
        ])
        <div class="block-content">
            @include('admin.contracts.sections.search-form')
        </div>
    </div>

    <div class="block {{ __('block.styles') }}">
        @include('admin.components.block-header',[
            'text' =>   __('lang.total_items'),
            'block_icon_class' => 'icon-document',
        ])
        <div class="block-content">
            <div class="table-wrap db-scroll">
                <table class="table table-striped table-vcenter">
                    <thead class="bg-body-dark">
                    <tr>
                        <th style="width: 30px"></th>
                        <th style="width: 13%;">@sortablelink('worksheet_no', __('เลขที่สัญญา'))</th>
                        <th style="width: 13%;">@sortablelink('contract_type', __('ประเภทสัญญา'))</th>
                        <th style="width: 18%;">@sortablelink('car_count', __('จำนวนรถในสัญญา'))</th>
                        <th style="width: 13%;">@sortablelink('service_type_name', __('ลูกค้า'))</th>
                        <th style="width: 13%;">@sortablelink('created_at', __('สาขาลูกค้า'))</th>
                        <th style="width: 10%;">@sortablelink('status', __('สถานะ'))</th>
                        <th class="sticky-col text-center"></th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach ($lists as $d)
                        <tr>
                            <td class="text-center toggle-table">
                                <i class="fa fa-angle-right text-muted"></i>
                            </td>
                            <td>{{ $d->worksheet_no }}</td>
                            <td>{{ $d->job ? $d->job->getJobTypeName() : null }}</td>
                            <td>{{ $d->contractline_count }}</td>
                            <td>{{ $d->customer->name }}</td>
                            <td>{{ $d->customer->branch?->name }}</td>
                            <td>{!! badge_render(__('contract.status_class_' . $d->status), __('contract.status_text_' . $d->status)) !!}</td>
                            <td class="sticky-col text-center">
                                <div class="btn-group">
                                    <div class="col-sm-12">
                                        <div class="dropdown dropleft">
                                            <button type="button" class="btn btn-sm btn-alt-secondary dropdown-toggle" id="dropdown-dropleft-dark"
                                                    data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                <i class="fa fa-ellipsis-vertical"></i>
                                            </button>
                                            <div class="dropdown-menu" aria-labelledby="dropdown-dropleft-dark">
                                                <a class="dropdown-item" href="{{ route('admin.contracts.show', ['contract' => $d]) }}"><i class="fa fa-eye me-1"></i> ดูข้อมูล</a>

                                                @if($d->status == \App\Enums\ContractEnum::REQUEST_TRANSFER_CONTRACT || $d->status == \App\Enums\ContractEnum::REQUEST_CHANGE_ADDRESS || $d->status == \App\Enums\ContractEnum::REQUEST_CHANGE_USER_CAR)
                                                    <button class="dropdown-item" onclick="showModalEdit('{{$d->id}}','{{$d->status}}')"><i class="far fa-edit me-1"></i> {{__('ตรวจสอบขอเปลี่ยนแปลงข้อมูล')}}</button>
                                                @else
                                                    @if($d->status != \App\Enums\ContractEnum::CANCEL_CONTRACT && $d->status != \App\Enums\ContractEnum::CLOSE_CONTRACT)
                                                        <a class="dropdown-item" href="{{ route('admin.contracts.edit', ['contract' => $d]) }}"><i class="far fa-edit me-1"></i> แก้ไข</a>
                                                    @endif
                                                @endif
                                                @if((strcmp($d->job_type, \App\Models\LongTermRental::class) === 0 && !empty($d->contract_type)) || strcmp($d->job_type, \App\Models\Rental::class) === 0)
                                                    <a class="dropdown-item" href="{{ route('admin.contracts.print-pdf', ['contract' => $d]) }}" target="_blank" ><i class="fa fa-upload me-1"></i> พิมพ์สัญญา</a>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <tr style="display: none;">
                            <td></td>
                            <td class="td-table" colspan="7">
                                <table class="table table-striped">
                                    <thead class="bg-body-dark">
                                    <th style="width: 13%;">หมายเลขตัวถัง</th>
                                    <th style="width: 14%;">ทะเบียนรถ</th>
                                    <th style="width: 19%;">ผู้ใช้รถ</th>
                                    <th style="width: 15.5%;">วันที่เริ่มสัญญา</th>
                                    <th style="width: 14%;">วันที่สิ้นสุดสัญญา</th>
                                    <th>สถานะ</th>
                                    <th></th>
                                    </thead>
                                    <tbody>
                                    @if (sizeof($d->contractline) > 0)
                                        @foreach ($d->contractline as $index => $item)
                                            <tr>
                                                <td>{{$item->car->engine_no}}</td>
                                                <td>{{$item->car->license_plate}}</td>
                                                <td></td>
                                                <td>{{isset($item->pick_up_date) ? get_date_time_by_format($item->pick_up_date) : ''}}</td>
                                                <td>{{isset($item->return_date) ? get_date_time_by_format($item->return_date) : ''}}</td>
                                                <td>{!! badge_render(__('contract.status_class_' . $item->status), __('contract.status_text_' . $item->status)) !!}</td>
                                                <td class="sticky-col text-center"></td>
                                            </tr>
                                        @endforeach
                                    @else
                                        <tr>
                                            <td class="text-center" colspan="10">" {{ __('lang.no_list') }} "
                                            </td>
                                        </tr>
                                    @endif
                                    </tbody>
                                </table>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
            {!! $lists->appends(\Request::except('page'))->render() !!}
        </div>
    </div>
    @include('admin.contracts.modals.form-verify-modal')
    @include('admin.contracts.modals.form-non-approve-modal')
@endsection

@include('admin.components.select2-default')
@include('admin.components.date-input-script')
@include('admin.components.sweetalert')
@include('admin.components.list-delete')

@push('scripts')
    <script>
        $('.toggle-table').click(function () {
            $(this).parent().next('tr').toggle();
            $(this).children().toggleClass('fa fa-angle-down text-muted').toggleClass(
                'fa fa-angle-right text-muted');
        });

        function hideAllForm() {
            $('.form-change-user-car').hide();
            $('.form-change-address').hide();
            $('.form-transfer').hide();
        }

        $('#modal-edit-contract').on('hidden.bs.modal' , function (e) {
            hideAllForm();
            window.tableShowFileUpload.clearDataList();
            window.tableChangeCarUser.clearDataList();
        });

        let contractLogPayload = null

        function showModalEdit(id , status_request) {
            $('#contract_id').val(id);
            mySwal.showLoading();
            $.ajax({
                type: 'POST' ,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                } ,
                url: "{{ route('admin.contract.log_and_media_file') }}" ,
                data: {
                    contract_id: id ,
                    status: status_request ,
                } ,
                success: function (data) {
                    swal.close();
                    console.log(data)
                    if (data.success) {
                        window.tableShowFileUpload.addDataList(data.data.media_file)

                        if (status_request === '{{\App\Enums\ContractEnum::REQUEST_CHANGE_USER_CAR}}') {
                            if (data.data.contract_log.length > 0) {
                                $('#remark').val(data.data.contract_log[0].remark);
                                data.data.contract_log.forEach(log => {
                                    const car_data = JSON.parse(log.new_value)
                                    window.tableChangeCarUser.addData(car_data)
                                });
                            }
                            $('.form-change-user-car').show();
                        }
                        else if (status_request === '{{\App\Enums\ContractEnum::REQUEST_CHANGE_ADDRESS}}') {
                            $('#remark').val(data.data.contract_log.remark);
                            $('#change_address_new_address').val(data.data.contract_log.new_value);
                            $('#change_address_new_address').prop('disabled' , true);

                            $('.form-change-address').show();
                        }
                        else if (status_request === '{{\App\Enums\ContractEnum::REQUEST_TRANSFER_CONTRACT}}') {
                            $('#remark').val(data.data.contract_log.remark);
                            console.log(data.data.customer)
                            const customer = data.data.customer

                            $('#transfer_customer').val(customer.customer_code + ' - ' + customer.name);
                            $('#transfer_customer_phone').val(customer.tel);
                            $('#transfer_customer_address').val(customer.address);

                            $('#transfer_customer_phone').prop('disabled' , true);
                            $('#transfer_customer_address').prop('disabled' , true);

                            $('.form-transfer').show();
                            $('.form-transfer input').prop('disabled' , true);
                        }

                        $('#remark').prop('disabled' , true);
                        $('#status_request').prop('disabled' , true);
                        $('#status_request').val(status_request).trigger('change');
                        $('#modal-edit-contract').modal('show')
                    }
                } ,
                error: function (data) {
                    console.log(data)
                    swal.close();
                    mySwal.fire({
                        title: "{{ __('lang.store_error_title') }}" ,
                        html: 'ไม่สามารถโหลดข้อมูลที่เกี่ยวข้องได้<br>กรุณาลองใหม่อีกครั้งภายหลัง' ,
                        icon: 'warning' ,
                        confirmButtonText: "{{ __('lang.ok') }}" ,
                    });
                }
            });
        }

        $('.btn-save-form-modal-approve').click(function () {
            mySwal.showLoading();
            $.ajax({
                type: 'POST' ,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                } ,
                url: "{{ route('admin.contract.update-approve-request') }}" ,
                data: {
                    approveStatus : 1 ,
                    contract_id : $('#contract_id').val() ,
                } ,
                success: function (response) {
                    swal.close();
                    console.log(response)
                    if (response.success) {
                        mySwal.fire({
                            title: "{{ __('lang.store_success_title') }}",
                            text: "{{ __('lang.store_success_message') }}",
                            icon: 'success',
                            confirmButtonText: "{{ __('lang.ok') }}"
                        }).then(value => {
                            if (response.redirect) {
                                window.location.href = response.redirect;
                            } else {
                                window.location.reload();
                            }
                        });
                    }
                } ,
                error: function (data) {
                    console.log(data)
                    swal.close();
                    mySwal.fire({
                        title: "{{ __('lang.store_error_title') }}" ,
                        html: 'กรุณาลองใหม่อีกครั้งภายหลัง' ,
                        icon: 'warning' ,
                        confirmButtonText: "{{ __('lang.ok') }}" ,
                    });
                }
            });
        });

        $('.btn-show-form-modal-non-approve').click(function () {
            $('#non_approve_contract_id').val($('#contract_id').val())
            $('#modal-form-non-approve').modal('show')
        });

        $('.btn-save-form-modal-non-approve').click(function () {
            $('#modal-edit-contract').modal('hide')
            mySwal.showLoading();
            $.ajax({
                type: 'POST' ,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                } ,
                url: "{{ route('admin.contract.update-approve-request') }}" ,
                data: {
                    approveStatus : 0 ,
                    contract_id : $('#non_approve_contract_id').val() ,
                    reason : $('#reason').val() ,
                } ,
                success: function (response) {
                    $('#modal-form-non-approve').modal('hide')
                    swal.close();
                    console.log(response)
                    if (response.success) {
                        mySwal.fire({
                            title: "{{ __('lang.store_success_title') }}",
                            text: "{{ __('lang.store_success_message') }}",
                            icon: 'success',
                            confirmButtonText: "{{ __('lang.ok') }}"
                        }).then(value => {
                            if (response.redirect) {
                                window.location.href = response.redirect;
                            } else {
                                window.location.reload();
                            }
                        });
                    }
                } ,
                error: function (data) {
                    $('#modal-form-non-approve').modal('hide')
                    console.log(data)
                    swal.close();
                    mySwal.fire({
                        title: "{{ __('lang.store_error_title') }}" ,
                        html: 'กรุณาลองใหม่อีกครั้งภายหลัง' ,
                        icon: 'warning' ,
                        confirmButtonText: "{{ __('lang.ok') }}" ,
                    });
                }
            });
        });
    </script>
@endpush
