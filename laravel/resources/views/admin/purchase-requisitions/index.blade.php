@extends('admin.layouts.layout')
@section('page_title', __('purchase_requisitions.page_title'))

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
        @include('admin.components.block-header',[
     'text' =>   __('lang.search')    ,
    'block_icon_class' => 'icon-search',
       'is_toggle' => true
])
{{--        <div class="block-header">--}}
{{--            <h3 class="block-title">{{ __('purchase_requisitions.total_items') }}</h3>--}}
{{--            <div class="block-options">--}}
{{--                <div class="block-options-item">--}}
{{--                    @can(Actions::Manage . '_' . Resources::PurchaseRequisition)--}}
{{--                        <x-btns.add-new btn-text="{{ __('purchase_requisitions.add_new') }}"--}}
{{--                                        route-create="{{ route('admin.purchase-requisitions.create') }}"/>--}}
{{--                    @endcan--}}
{{--                </div>--}}
{{--            </div>--}}
{{--        </div>--}}
        <div class="block-content">
            <div class="justify-content-between">
                <form action="" method="GET" id="form-search">
                    <div class="form-group row push mb-4">
                        <div class="col-sm-3">
                            <label for="s" class="text-start col-form-label">{{ __('lang.search_label') }}</label>
                            <input type="text" id="s" name="s" class="form-control"
                                   placeholder="{{ __('lang.search_placeholder') }}" value="{{ $s }}">
                        </div>
                        <div class="col-sm-3">
                            <x-forms.select-option id="pr_no" :value="$pr_no" :list="$pr_list"
                                                   :label="__('purchase_requisitions.pr_no')"/>
                        </div>
                        <div class="col-sm-3">
                            <x-forms.select-option id="rental_type" :value="$rental_type" :list="$rental_type_list"
                                                   :label="__('purchase_requisitions.rental_type')"/>
                        </div>
                        <div class="col-sm-3">
                            <x-forms.select-option id="status" :value="$status" :list="$status_list"
                                                   :label="__('purchase_requisitions.status')"/>
                        </div>
                    </div>
                    <div class="form-group row push mb-4">
                        <div class="col-sm-6">
                            <label class="text-start col-form-label"
                                   for="from_request_date">{{ __('purchase_requisitions.request_date') }}</label>
                            <div class="form-group">
                                <div class="input-daterange input-group" data-date-format="mm/dd/yyyy"
                                     data-week-start="1"
                                     data-autoclose="true" data-today-highlight="true">
                                    <input type="text" class="js-flatpickr form-control flatpickr-input"
                                           id="from_request_date" name="from_request_date"
                                           value="{{ $from_request_date }}"
                                           placeholder="{{ __('lang.select_date') }}" data-week-start="1"
                                           data-autoclose="true"
                                           data-today-highlight="true">
                                    <div class="input-group-prepend input-group-append">
                                        <span class="input-group-text font-w600">
                                            <i class="fa fa-fw fa-arrow-right"></i>
                                        </span>
                                    </div>
                                    <input type="text" class="js-flatpickr form-control flatpickr-input"
                                           id="to_request_date" name="to_request_date" value="{{ $to_request_date }}"
                                           placeholder="{{ __('lang.select_date') }}" data-week-start="1"
                                           data-autoclose="true" data-today-highlight="true">
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <label class="text-start col-form-label"
                                   for="from_require_date">{{ __('purchase_requisitions.require_date') }}</label>
                            <div class="form-group">
                                <div class="input-daterange input-group" data-date-format="mm/dd/yyyy"
                                     data-week-start="1"
                                     data-autoclose="true" data-today-highlight="true">
                                    <input type="text" class="js-flatpickr form-control flatpickr-input"
                                           id="from_require_date" name="from_require_date"
                                           value="{{ $from_require_date }}"
                                           placeholder="{{ __('lang.select_date') }}" data-week-start="1"
                                           data-autoclose="true" data-today-highlight="true">
                                    <div class="input-group-prepend input-group-append">
                                        <span class="input-group-text font-w600">
                                            <i class="fa fa-fw fa-arrow-right"></i>
                                        </span>
                                    </div>
                                    <input type="text" class="js-flatpickr form-control flatpickr-input"
                                           id="to_require_date" name="to_require_date" value="{{ $to_require_date }}"
                                           placeholder="{{ __('lang.select_date') }}" data-week-start="1"
                                           data-autoclose="true" data-today-highlight="true">
                                </div>
                            </div>
                        </div>
                    </div>
                    @include('admin.components.btns.search')
                </form>
            </div>
        </div>
    </div>
    @section('block_options')
        @can(Actions::Manage . '_' . Resources::PurchaseRequisition)
        <x-btns.add-new btn-text="{{ __('purchase_requisitions.add_new') }}"
                        route-create="{{ route('admin.purchase-requisitions.create') }}"/>
        @endcan
    @endsection
    <div class="block {{ __('block.styles') }}">
        @include('admin.components.block-header',[
   'text' =>   __('purchase_requisitions.total_items')   ,
  'block_icon_class' => 'icon-document',
])
        <div class="block-content">
            <div class="table-wrap db-scroll">
                <table class="table table-striped table-vcenter">
                    <thead class="bg-body-dark">
                    <tr>
                        <th style="width: 1px;">#</th>
                        <th>@sortablelink('pr_no', __('purchase_requisitions.pr_no_car'))</th>
                        <th>@sortablelink('parent_id', __('purchase_requisitions.pr_parent'))</th>
                        <th>@sortablelink('rental_type', __('purchase_requisitions.rental_type'))</th>
                        <th>@sortablelink('request_date', __('purchase_requisitions.request_date'))</th>
                        <th>@sortablelink('require_date', __('purchase_requisitions.require_date'))</th>
                        <th class="text-center">@sortablelink('status', __('purchase_requisitions.status'))</th>
                        <th style="width: 100px;" class="sticky-col text-center"></th>
                    </tr>
                    </thead>
                    <tbody>
                        @if (sizeof($list) > 0)
                            @foreach ($list as $index => $d)
                                <tr>
                                    <td>{{ $list->firstItem() + $index }}</td>
                                    <td>{{ $d->pr_no }}</td>
                                    <td>{{ ($d->parent_pr) ? $d->parent_pr->pr_no : null }}</td>
                                    <td>{{ __('purchase_requisitions.rental_type_' . $d->rental_type) }}</td>
                                    <td>{{ get_thai_date_format($d->request_date, 'd/m/Y') }}</td>
                                    <td>{{ get_thai_date_format($d->require_date, 'd/m/Y') }}</td>
                                    <td class="text-center">
                                        {!! badge_render(
                                            __('purchase_requisitions.status_' . $d->status . '_class'),
                                            __('purchase_requisitions.status_' . $d->status . '_text'),
                                            null,
                                        ) !!}
                                    </td>
                                    <td class="sticky-col text-center">
                                        @if (in_array($d->status, [PRStatusEnum::CONFIRM, PRStatusEnum::CANCEL, PRStatusEnum::COMPLETE]))
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
                                                            @can(Actions::View . '_' . Resources::PurchaseRequisition)
                                                                <a class="dropdown-item"
                                                                href="{{ route('admin.purchase-requisitions.show', ['purchase_requisition' => $d]) }}"><i
                                                                        class="fa fa-eye me-1"></i> ดูข้อมูล</a>
                                                            @endcan
                                                            @can(Actions::Manage . '_' . Resources::PurchaseRequisition)
                                                                @if ($d->rental_type !== RentalTypeEnum::LONG)
                                                                    <a class="dropdown-item duplicate_pr"
                                                                    href="javascript:void(0)"
                                                                    data-id="{{ $d->id }}"><i
                                                                            class="far fa-clone me-1"></i> คัดลอกข้อมูล
                                                                    </a>
                                                                @endif
                                                            @endcan
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @elseif (in_array($d->status, [PRStatusEnum::PENDING_REVIEW, PRStatusEnum::REJECT]))
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
                                                            @can(Actions::View . '_' . Resources::PurchaseRequisition)
                                                                <a class="dropdown-item"
                                                                href="{{ route('admin.purchase-requisitions.show', ['purchase_requisition' => $d]) }}"><i
                                                                        class="fa fa-eye me-1"></i> ดูข้อมูล</a>
                                                            @endcan
                                                            @can(Actions::Manage . '_' . Resources::PurchaseRequisition)
                                                                <a class="dropdown-item"
                                                                href="{{ route('admin.purchase-requisitions.edit', ['purchase_requisition' => $d]) }}">
                                                                    <i class="far fa-edit me-1"></i> แก้ไข
                                                                </a>
                                                                @if (in_array($d->status, [PRStatusEnum::PENDING_REVIEW]) && $d->rental_type !== RentalTypeEnum::LONG)
                                                                    <a class="dropdown-item duplicate_pr"
                                                                    href="javascript:void(0)"
                                                                    data-id="{{ $d->id }}"><i
                                                                            class="far fa-clone me-1"></i> คัดลอกข้อมูล
                                                                    </a>
                                                                @endif
                                                                <a class="dropdown-item btn-cancel-status"
                                                                data-status="{{ \App\Enums\PRStatusEnum::CANCEL }}"
                                                                data-id="{{ $d->id }}">
                                                                    <i class="far fa-circle-xmark me-1"></i> ยกเลิก
                                                                </a>
                                                            @endcan
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @elseif(in_array($d->status, [PRStatusEnum::DRAFT]))
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
                                                            @can(Actions::View . '_' . Resources::PurchaseRequisition)
                                                                <a class="dropdown-item"
                                                                href="{{ route('admin.purchase-requisitions.show', ['purchase_requisition' => $d]) }}"><i
                                                                        class="fa fa-eye me-1"></i> ดูข้อมูล</a>
                                                            @endcan
                                                            @can(Actions::Manage . '_' . Resources::PurchaseRequisition)
                                                                <a class="dropdown-item"
                                                                href="{{ route('admin.purchase-requisitions.edit', ['purchase_requisition' => $d]) }}">
                                                                    <i class="far fa-edit me-1"></i> แก้ไข
                                                                </a>
                                                                @if ($d->rental_type !== RentalTypeEnum::LONG)
                                                                    <a class="dropdown-item duplicate_pr"
                                                                    href="javascript:void(0)"
                                                                    data-id="{{ $d->id }}"><i
                                                                            class="far fa-clone me-1"></i> คัดลอกข้อมูล
                                                                    </a>
                                                                @endif
                                                                <a class="dropdown-item btn-delete-row"
                                                                href="javascript:void(0)"
                                                                data-route-delete="{{ route('admin.purchase-requisitions.destroy', [
                                                                        'purchase_requisition' => $d,
                                                                    ]) }}"><i
                                                                        class="fa fa-trash-alt me-1"></i> ลบ</a>
                                                            @endcan
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        @else
                        <tr class="table-empty">
                            <td class="text-center" colspan="8">" {{ __('lang.no_list') }} "</td>
                        </tr>
                        @endif
                    </tbody>
                </table>
            </div>
            {!! $list->appends(\Request::except('page'))->render() !!}
        </div>
    </div>
    @include('admin.purchase-requisition-approve.modals.cancel-modal')
@endsection


@include('admin.components.select2-default')
@include('admin.components.sweetalert')
@include('admin.components.list-delete')
@include('admin.purchase-requisition-approve.scripts.update-status')
@include('admin.components.date-input-script')

@push('scripts')
    <script>
        function cancelmodal(id) {
            document.getElementById("cancel_status").value = {{ PRStatusEnum::CANCEL }}
            document.getElementById("cancel_id").value = id;
            document.getElementById("redirect").value = "{{ route('admin.purchase-requisitions.index') }}";
            $('#modal-cancel').modal('show');
        }


        $('.duplicate_pr').click(function (e) {

            var purchase_requisition_id = $(this).attr("data-id");
            $.ajax({
                type: 'GET',
                url: "{{ route('admin.purchase-requisition.duplicate') }}",
                data: {
                    purchase_requisition_id: purchase_requisition_id,
                },
                success: function (data) {
                    mySwal.fire({
                        title: "{{ __('lang.store_success_title') }}",
                        text: 'คัดลอกเรียบร้อย',
                        icon: 'success',
                        confirmButtonText: "{{ __('lang.ok') }}"
                    }).then(value => {
                        window.location.reload();
                    });
                }
            });
        });
    </script>
@endpush
