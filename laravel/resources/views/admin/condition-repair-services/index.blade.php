@extends('admin.layouts.layout')
@section('page_title', $page_title)

@section('block_options_list')
    <div class="block-options-item">
        @can(Actions::Manage . '_' . Resources::ConditionRepairService)
            <x-btns.add-new btn-text="{{ __('lang.add') . __('condition_quotations.condition') }}"
                route-create="{{ route('admin.condition-repair-services.create') }}" />
        @endcan
    </div>
@endsection

@section('content')
    <div class="block {{ __('block.styles') }}">
        @include('admin.components.block-header', [
            'text' => __('lang.search'),
            'block_icon_class' => 'icon-search',
            'is_toggle' => true,
            'block_option_id' => '_search',
        ])
        <div class="block-content">
            <div class="justify-content-between">
                <form action="" method="GET" id="form-search">
                    <div class="form-group row push mb-4">
                        <div class="col-sm-12">
                            <x-forms.input-new-line id="s" :value="$s" :label="__('condition_quotations.condition_name')" :optionals="['placeholder' => __('lang.search_placeholder')]" />
                        </div>
                    </div>
                    @include('admin.components.btns.search')
                </form>
            </div>
        </div>
    </div>
    <div class="block {{ __('block.styles') }}">
        @include('admin.components.block-header', [
            'text' => __('lang.total_list'),
            'block_option_id' => '_list',
        ])
        <div class="block-content">
            <div class="table-wrap db-scroll">
                <table class="table table-striped table-vcenter">
                    <thead class="bg-body-dark">
                        <tr>
                            <th style="width: 5%;"></th>
                            <th style="width: 5%;">#</th>
                            <th style="width: 90%;">{{ __('condition_quotations.condition_name') }}</th>
                            <th class="sticky-col text-center">{{ __('lang.tools') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($list as $key => $d)
                            <tr class="{{ $loop->iteration % 2 == 0 ? 'table-active' : '' }}">
                                <td class="text-center toggle-table" style="width: 30px">
                                    <i class="fa fa-angle-right text-muted"></i>
                                </td>
                                <td>{{ $d->seq }}</td>
                                <td>{{ $d->name }}</td>
                                <td>
                                    @include('admin.components.dropdown-action', [
                                        'view_route' => route('admin.condition-repair-services.show', [
                                            'condition_repair_service' => $d,
                                        ]),
                                        'edit_route' => route('admin.condition-repair-services.edit', [
                                            'condition_repair_service' => $d,
                                        ]),
                                        'delete_route' => route('admin.condition-repair-services.destroy', [
                                            'condition_repair_service' => $d,
                                        ]),
                                        'view_permission' =>
                                            Actions::View . '_' . Resources::ConditionRepairService,
                                        'manage_permission' =>
                                            Actions::Manage . '_' . Resources::ConditionRepairService,
                                    ])
                                </td>
                            </tr>
                            <tr style="display: none;">
                                <td></td>
                                <td class="td-table" colspan="2">
                                    <div class="row">
                                        <div class="col-md-9 text-left">
                                            <span>{{ __('condition_quotations.checklist_table') }}</span>
                                        </div>
                                    </div>
                                    <table class="table table-striped">
                                        <thead class="bg-body-dark">
                                            <th style="width: 50px">#</th>
                                            <th style="width: 90%"> {{ __('condition_quotations.checklist_name') }}</th>
                                        <tbody>
                                            @if (sizeof($d->child_list) > 0)
                                                @foreach ($d->child_list as $index => $item)
                                                    <tr>
                                                        <td style="width: 50px">{{ $item->seq }}
                                                        </td>
                                                        <td style="width: 90%">{{ $item->name }}</td>
                                                    </tr>
                                                @endforeach
                                            @else
                                                <tr>
                                                    <td class="text-center" colspan="3">" {{ __('lang.no_list') }} "
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
            {!! $list->appends(\Request::except('page'))->render() !!}
        </div>
    </div>
@endsection

@include('admin.components.sweetalert')
@include('admin.components.list-delete')

@push('scripts')
    <script>
        $('.toggle-table').click(function() {
            $(this).parent().next('tr').toggle();
            $(this).children().toggleClass('fa fa-angle-down text-muted').toggleClass(
                'fa fa-angle-right text-muted');
        });
    </script>
@endpush
