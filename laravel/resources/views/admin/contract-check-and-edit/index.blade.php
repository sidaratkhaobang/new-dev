@extends('admin.layouts.layout')
@section('page_title', __('menu.contract.sub.contract_check_and_edit'))
@push('custom_styles')
    <meta name="csrf-token" content="{{ csrf_token() }}">
@endpush
@section('content')
    <div class="block {{ __('block.styles') }}">
        @include('admin.components.block-header', [
            'text' => __('lang.search'),
            'block_icon_class' => 'icon-search',
            'is_toggle' => true,
        ])
        <div class="block-content">
            {{--            <div class="block-header"> --}}
            {{--                <h4><i class="fa fa-file-lines"></i> {{ __('รายการทั้งหมด') }}</h4> --}}
            {{--            </div> --}}
            @include('admin.contract-check-and-edit.sections.search-form')
        </div>
    </div>

    <div class="block {{ __('block.styles') }}">
        @include('admin.components.block-header', [
            'text' => __('lang.total_list'),
            'block_icon_class' => 'icon-document',
        ])
        <div class="block-content">
            <div class="table-wrap db-scroll">
                <table class="table table-striped table-vcenter">
                    <thead class="bg-body-dark">
                        <tr>
                            <th style="width: 30px"></th>
                            <th style="width: 1px;">#</th>
                            <th style="width: 13%;">@sortablelink('worksheet_no', __('เลขที่สัญญา'))</th>
                            <th style="width: 13%;">@sortablelink('contract_type', __('ประเภทสัญญา'))</th>
                            <th style="width: 18%;">@sortablelink('car_count', __('จำนวนรถในสัญญา'))</th>
                            <th style="width: 13%;">@sortablelink('service_type_name', __('ลูกค้า'))</th>
                            <th style="width: 13%;"></th>
                            <th class="text-center" style="width: 10%;">@sortablelink('status', __('สถานะ'))</th>
                            <th class="sticky-col text-center"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @if (!empty($lists->count()))
                            @foreach ($lists as $index => $d)
                                @if (isset($d->contractline) && sizeof($d->contractline) > 0)
                                    <tr>
                                        <td class="text-center toggle-table">
                                            <i class="fa fa-angle-right text-muted"></i>
                                        <td>{{ $lists->firstItem() + $index }}</td>
                                        <td>{{ $d->worksheet_no }}</td>
                                        <td>{{ $d->job ? $d->job->getJobTypeName() : null }}</td>
                                        <td>{{ $d->contractline_count }}</td>
                                        <td>{{ $d->customer?->name }}</td>
                                        <td>{{ $d->customer?->branch?->name }}</td>
                                        <td class="text-center">{!! badge_render(__('contract.status_class_' . $d->status), __('contract.status_text_' . $d->status)) !!}</td>
                                        <td class="sticky-col text-center">
                                            <div class="btn-group">
                                                <div class="col-sm-12">
                                                    <div class="dropdown dropleft">
                                                        <button type="button"
                                                            class="btn btn-sm btn-alt-secondary dropdown-toggle"
                                                            id="dropdown-dropleft-dark" data-bs-toggle="dropdown"
                                                            aria-haspopup="true" aria-expanded="false">
                                                            <i class="fa fa-ellipsis-vertical"></i>
                                                        </button>
                                                        <div class="dropdown-menu" aria-labelledby="dropdown-dropleft-dark">
                                                            <a class="dropdown-item"
                                                                href="{{ route('admin.contracts.show', ['contract' => $d]) }}"><i
                                                                    class="fa fa-eye me-1"></i> {{ __('ดูข้อมูล') }}</a>
                                                            @if (
                                                                $d->status == \App\Enums\ContractEnum::ACTIVE_BETWEEN_CONTRACT ||
                                                                    $d->status == \App\Enums\ContractEnum::REJECT_REQUEST)
                                                                <button class="dropdown-item"
                                                                    onclick="showModalEdit('{{ $d->id }}')"><i
                                                                        class="fa fa-edit me-1"></i>
                                                                    {{ __('ข้อเปลี่ยนแปลงข้อมูล') }}
                                                                </button>
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
                                                    <th style="width: 1px;">#</th>
                                                    <th style="width: 13%;">หมายเลขตัวถัง</th>
                                                    <th style="width: 14%;">ทะเบียนรถ</th>
                                                    <th style="width: 19%;">ผู้ใช้รถ</th>
                                                    <th style="width: 15.5%;">วันที่เริ่มสัญญา</th>
                                                    <th style="width: 14%;">วันที่สิ้นสุดสัญญา</th>
                                                    <th class="text-center">สถานะ</th>
                                                </thead>
                                                <tbody>
                                                    @if (sizeof($d->contractline) > 0)
                                                        @foreach ($d->contractline as $_index => $item)
                                                            <tr>
                                                                <td>{{ $_index + 1 }}</td>
                                                                <td>{{ $item->car?->engine_no }}</td>
                                                                <td>{{ $item->car?->license_plate }}</td>
                                                                <td></td>
                                                                <td>{{ isset($item->pick_up_date) ? get_date_time_by_format($item->pick_up_date) : '' }}
                                                                </td>
                                                                <td>{{ isset($item->return_date) ? get_date_time_by_format($item->return_date) : '' }}
                                                                </td>
                                                                <td class="text-center">{!! badge_render(__('contract.status_class_' . $item->status), __('contract.status_text_' . $item->status)) !!}</td>
                                                                {{-- <td class="sticky-col text-center"></td> --}}
                                                            </tr>
                                                        @endforeach
                                                    @else
                                                        <tr>
                                                            <td class="text-center" colspan="10">"
                                                                {{ __('lang.no_list') }} "
                                                            </td>
                                                        </tr>
                                                    @endif
                                                </tbody>
                                            </table>
                                        </td>
                                    </tr>
                                @endif
                            @endforeach
                        @else
                            <tr>
                                <td class="text-center" colspan="11">" {{ __('lang.no_list') }} "</td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
            {!! $lists->appends(\Request::except('page'))->render() !!}
        </div>
    </div>
    @include('admin.contract-check-and-edit.modals.form-modal')
    @include('admin.contract-check-and-edit.modals.upload-file-modal')
@endsection

@include('admin.components.select2-default')
@include('admin.components.date-input-script')
@include('admin.components.sweetalert')
@include('admin.components.list-delete')

@include('admin.components.select2-ajax', [
    'id' => 'change_user_license_plate',
    'url' => route('admin.util.select2.car-license-plate-by-contract'),
    'parent_id' => 'contract_id',
])

@include('admin.components.select2-ajax', [
    'id' => 'transfer_customer_code',
    'url' => route('admin.util.select2-customer.customer-codes'),
    'modal' => '#modal-edit-contract',
])

@include('admin.components.select2-ajax', [
    'id' => 'transfer_customer',
    'url' => route('admin.util.select2-customer.customer-codes'),
    'modal' => '#modal-edit-contract',
])

@include('admin.components.upload-image-scripts')
@include('admin.components.upload-image', [
    'id' => 'zone-upload-file',
    'max_files' => 10,
    'accepted_files' => '.jpeg,.jpg,.png,.svg,.pdf,.xls,.xlsx,.doc,.csv',
    'preview_files' => true,
])

@include('admin.components.form-save', [
    'store_uri' => route('admin.contract-check-and-edit.store'),
])

@push('scripts')
    <script>
        $('.toggle-table').click(function() {
            $(this).parent().next('tr').toggle();
            $(this).children().toggleClass('fa fa-angle-down text-muted').toggleClass(
                'fa fa-angle-right text-muted');
        });

        $('#modal-edit-contract').on('hidden.bs.modal', function(e) {
            window.tableFileUpload.clearDataList();
            window.tableChangeCarUser.clearDataList();
            $('#change_user_car_description').val('')
            $('#change_address_description').val('')
            $('#change_address_new_address').val('')
            $('#transfer_description').val('')
            $('#change_user_name').val('')
            $('#change_user_phone').val('')
            $('#transfer_customer_phone').val('')
            $('#transfer_customer_address').val('')

            $('#status_request').val(null).trigger('change');
            $('#transfer_customer_code').val(null).trigger('change');
            $('#change_user_license_plate').val(null).trigger('change');
            $('#transfer_customer').val(null).trigger('change');
        });

        function showModalEdit(id) {
            $('#contract_id').val(id);
            mySwal.showLoading();
            $.ajax({
                type: 'POST',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: "{{ route('admin.contract.media_file') }}",
                data: {
                    contract_id: id,
                },
                success: function(data) {
                    swal.close();
                    console.log(data.success)
                    if (data.success) {
                        window.tableFileUpload.addDataList(data.data)
                        $('#status_request').val(null).trigger('change');
                        $('#modal-edit-contract').modal('show')
                    }
                },
                error: function(data) {
                    swal.close();
                    mySwal.fire({
                        title: "{{ __('lang.store_error_title') }}",
                        html: 'ไม่สามารถโหลดข้อมูลเอกสารที่เกี่ยวข้องได้<br>กรุณาลองใหม่อีกครั้งภายหลัง',
                        icon: 'warning',
                        confirmButtonText: "{{ __('lang.ok') }}",
                    });
                }
            });
        }
    </script>
@endpush
