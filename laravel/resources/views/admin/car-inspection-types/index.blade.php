@extends('admin.layouts.layout')
@section('page_title', __('car_inspection_types.page_title'))

@section('content')
<div class="block {{ __('block.styles') }}">
    @include('admin.components.block-header', [
        'text' => __('lang.search'),
        'block_icon_class' => 'icon-search',
        'is_toggle' => true
    ])
    <div class="block-content pt-0">
        <form action="" method="GET" id="form-search">
            <div class="form-group row push">
                <div class="col-sm-3">
                    <label for="s" class="text-start col-form-label">{{ __('lang.search_label') }}</label>
                    <input type="text" id="s" name="s" class="form-control"
                            placeholder="{{ __('lang.search_placeholder') }}" value="{{ $s }}">
                </div>

                <div class="col-sm-3">
                    <x-forms.select-option id="inspection_form" :value="$inspection_form"
                            :list="$inspection_form_list"
                            :label="__('car_inspection_types.inspect_type')"/>
                </div>
            </div>
            @include('admin.components.btns.search')
        </form>
    </div>
</div>
<div class="block {{ __('block.styles') }}">
    @include('admin.components.block-header', [
        'text' => __('lang.total_list'),
    ])
    <div class="block-content">
        <div class="table-wrap db-scroll">
            <table class="table table-striped table-vcenter">
                <thead class="bg-body-dark">
                <tr>
                    <th style="width: 1px;"></th>
                    <th style="width: 1px;">#</th>
                    <th style="width: 30%;">@sortablelink('name', __('car_inspection_types.inspect_type'))</th>
                    <th style="width: 30%;">@sortablelink('name', __('car_inspection_types.car_type'))</th>
                    <th style="width: 20%;">{{ __('car_inspection_types.rental_type')}}</th>
                    <th style="width: 10%;">{{ __('car_inspection_types.customer_signature_out')}}</th>
                    <th style="width: 10%;">{{ __('car_inspection_types.customer_signature_in')}}</th>
                    <th style="width: 10px;" class="sticky-col"></th>
                </tr>
                </thead>
                <tbody>
                @if(!empty($list->count()))
                    @foreach ($list as $index => $d)
                        <tr>
                            <td><i class="fas fa-angle-right" aria-hidden="true" onclick="hide({{$index}})"
                                    id="bt{{$index}}"></i></td>
                            <td>{{ $index + $list->firstItem() }}</td>
                            <td colspan="3">{{ $d->name }}</td>
                            <td class="text-center">@if($d->is_need_customer_sign_out == 1)
                                    <i class="far fa-circle-check" aria-hidden="true" style="color: green; "></i>
                                @endif </td>
                            <td class="text-center">@if($d->is_need_customer_sign_in == 1)
                                    <i class="far fa-circle-check" aria-hidden="true" style="color: green "></i>
                                @endif </td>
                            <td class="sticky-col text-center">
                                <div class="dropdown dropleft">
                                    <button type="button" class="btn btn-sm btn-alt-secondary dropdown-toggle"
                                            id="dropdown-dropleft-dark" data-bs-toggle="dropdown"
                                            aria-haspopup="true"
                                            aria-expanded="false">
                                        <i class="fa fa-ellipsis-vertical"></i>
                                    </button>
                                    <div class="dropdown-menu" aria-labelledby="dropdown-dropleft-dark">
                                        @can(Actions::View . '_' . Resources::ConfigInspectionFlow)
                                            <a class="dropdown-item"
                                                href="{{ route('admin.car-inspection-types.show', ['car_inspection_type' => $d->id]) }}"><i
                                                    class="fa fa-eye me-1"></i>
                                                {{ __('car_inspection_types.view') }}
                                            </a>
                                        @endcan
                                        @can(Actions::Manage . '_' . Resources::ConfigInspectionFlow)
                                            <a class="dropdown-item"
                                                href="{{ route('admin.car-inspection-types.edit', ['car_inspection_type' => $d->id]) }}"><i
                                                    class="far fa-edit me-1"></i>
                                                {{ __('car_inspection_types.edit') }}
                                            </a>
                                        @endcan
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <tr id="sub-section{{$index}}" class="hidden hd">
                            <td></td>
                            <td colspan="7">
                                <div class="table-wrap">

                                    <table class="table table-striped" :id="'sub-table-'+k">
                                        <thead class="bg-body-dark">
                                        <th style="width: 5%">#</th>
                                        <th style="width: 20%">{{ __('car_inspection_types.inspection_seq_car')}}</th>
                                        <th style="width: 20%">{{ __('car_inspection_types.inspection_form')}}</th>
                                        <th style="width: 16%">{{ __('car_inspection_types.inspection_team')}}</th>
                                        <th style="width: 16.5%"
                                            class="text-center">{{ __('car_inspection_types.take_photo')}}</th>
                                        <th style="width: 16.5%"
                                            class="text-center">{{ __('car_inspection_types.inspector_signature')}}</th>
                                        <th class="sticky-col text-center"></th>
                                        </thead>
                                        @if(count($d->subseq) > 0)
                                            <tbody>
                                            @foreach ($d->subseq as $index2 => $d2)
                                                <tr>
                                                    <td>{{ $index2 + $list->firstItem() }}</td>
                                                    <td>{{ __('car_inspection_types.status_condition_name_'. $d2->transfer_reason) }}</td>
                                                    <td>{{ $d2->in_form }}</td>
                                                    <td></td>

                                                    <td class="text-center">@if($d2->is_need_images == 1)
                                                            <i class="far fa-circle-check" aria-hidden="true"
                                                                style="color: green "></i>
                                                        @endif</td>
                                                    <td class="text-center">@if($d2->is_need_inspector_sign == 1)
                                                            <i class="far fa-circle-check" aria-hidden="true"
                                                                style="color: green "></i>
                                                        @endif</td>
                                                    <td></td>
                                                </tr>
                                            @endforeach
                                            </tbody>
                                        @else
                                            <tbody>
                                            <tr class="table-empty" id='empty-data'>
                                                <td class="text-center" colspan="7">" {{ __('lang.no_list') }}"
                                                </td>
                                            </tr>
                                            </tbody>
                                        @endif
                                    </table>
                                </div>
                        </tr>
                    @endforeach
                @else
                    <tr>
                        <td class="text-center" colspan="11">{{ __('lang.no_list') }}</td>
                    </tr>
                @endif
                </tbody>
            </table>
        </div>
        {!! $list->appends(\Request::except('page'))->render() !!}
    </div>
</div>
@endsection
@include('admin.components.select2-default')
@include('admin.components.sweetalert')
@include('admin.components.list-delete')

@push('scripts')
    <script>
        $('.copyForm').click(function (e) {

            var id = $(this).attr("data-id");
            console.log();
            $.ajax({
                type: 'GET',
                url: "{{ route('admin.car-inspections.copyForm') }}",
                data: {
                    id: id,
                },
                success: function (data) {
                    copyAlert('คัดลอกสำเร็จ');
                    setTimeout(function () {
                        location.reload();
                    }, 1200);
                }
            });

        });

        $(".hd").hide();

        function hide(index) {
            console.log(index);
            if ($("#sub-section" + index).is(":hidden")) {
                $("hd").removeClass('hidden');
                $('#bt' + index).removeClass('fa-angle-right');
                $('#bt' + index).addClass('fa-angle-down');
                $("#sub-section" + index).show();
            } else {
                $("#sub-section" + index).hide()
                $("hd").addClass('hidden')
                $('#bt' + index).removeClass('fa-angle-down');
                $('#bt' + index).addClass('fa-angle-right');
                $("#sub-section" + index).hide()
            }
        }
    </script>
@endpush
