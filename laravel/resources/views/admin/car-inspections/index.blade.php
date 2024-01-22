@extends('admin.layouts.layout')
@section('page_title', __('car_inspections.page_title'))

@section('content')
    <div class="block {{ __('block.styles') }}">
        @include('admin.components.block-header', [
            'text' => __('lang.search'),
            'block_icon_class' => 'icon-search',
            'is_toggle' => true
        ])

        <div class="block-content pt-0">
            @include('admin.components.forms.simple-search')
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
                        <th style="width: 10px;">#</th>
                        <th style="width: 100%;">@sortablelink('name', __('car_inspections.form_name'))</th>
                        <th style="width: 10px;" class="sticky-col"></th>
                    </tr>
                    </thead>
                    <tbody>
                    @if(!empty($list->count()))
                        @foreach ($list as $index => $d)
                            <tr>
                                <td>{{ $index + $list->firstItem() }}</td>
                                <td>{{ $d->name }}</td>
                                <td class="sticky-col text-center">
                                    <div class="dropdown dropleft">
                                        <button type="button" class="btn btn-sm btn-alt-secondary dropdown-toggle"
                                                id="dropdown-dropleft-dark" data-bs-toggle="dropdown"
                                                aria-haspopup="true"
                                                aria-expanded="false">
                                            <i class="fa fa-ellipsis-vertical"></i>
                                        </button>

                                        <div class="dropdown-menu" aria-labelledby="dropdown-dropleft-dark">
                                            @can(Actions::View . '_' . Resources::ConfigCarInspection)
                                                <a class="dropdown-item"
                                                   href="{{ route('admin.car-inspections.show', ['car_inspection' => $d->id]) }}"><i
                                                        class="fa fa-eye me-1"></i>
                                                    {{ __('car_inspections.view') }}
                                                </a>
                                            @endcan
                                            @can(Actions::Manage . '_' . Resources::ConfigCarInspection)
                                                <a class="dropdown-item"
                                                   href="{{ route('admin.car-inspections.edit', ['car_inspection' => $d->id]) }}"><i
                                                        class="far fa-edit me-1"></i>
                                                    {{ __('car_inspections.edit') }}
                                                </a>

                                                <a class="dropdown-item copyForm" href="javascript:void(0)"
                                                   data-id="{{ $d->id }}"><i class="far fa-clone me-1"></i>
                                                    {{ __('car_inspections.copy_form') }}
                                                </a>

                                                @if ($d->is_standard != 1)
                                                    <a class="dropdown-item btn-delete-row" href="javascript:void(0)"
                                                       data-route-delete="{{ route('admin.car-inspections.destroy', ['car_inspection' => $d->id]) }}"><i
                                                            class="fa fa-trash-alt me-1"></i>{{ __('car_inspections.delete') }}
                                                    </a>
                                                @endif
                                            @endcan
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td class="text-center" colspan="11">" {{ __('lang.no_list') }} "</td>
                        </tr>
                    @endif
                    </tbody>
                </table>
            </div>
            {!! $list->appends(\Request::except('page'))->render() !!}
        </div>
    </div>
@endsection

@include('admin.components.sweetalert')
@include('admin.components.list-delete')

@push('scripts')
    <script>
        $('.copyForm').click(function (e) {

            var id = $(this).attr("data-id");
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
    </script>
@endpush
