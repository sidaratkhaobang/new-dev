@extends('admin.layouts.layout')
@section('page_title', __('lang.manage') . __('config_approves.page_title') . ' - สาขา ' . $branch_name)

@section('content')
    <div class="block {{ __('block.styles') }}">
        <div class="block-content">
            <div class="table-wrap db-scroll">
                <table class="table table-striped table-vcenter">
                    <thead class="bg-body-dark">
                        <tr>
                            {{-- <th style="width: 10px;"></th> --}}
                            <th style="width: 100%;">{{ __('config_approves.page_title') }}</th>
                            <th style="width: 10px;" class="sticky-col"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($list as $index => $d)
                            <tr>
                                {{-- <td> <i class="fas fa-angle-right" aria-hidden="true" onclick="hide({{ $index }})"
                                        id="bt{{ $index }}"></i></td> --}}
                                <td>{{ __('config_approves.config_type_' . $d->type) }}</td>
                                <td class="sticky-col text-center">
                                    <div class="dropdown dropleft">
                                        <button type="button" class="btn btn-sm btn-alt-secondary dropdown-toggle"
                                            id="dropdown-dropleft-dark" data-bs-toggle="dropdown" aria-haspopup="true"
                                            aria-expanded="false">
                                            <i class="fa fa-ellipsis-vertical"></i>
                                        </button>
                                        <div class="dropdown-menu" aria-labelledby="dropdown-dropleft-dark">
                                            @can(Actions::View . '_' . Resources::ConfigApprove)
                                                <a class="dropdown-item"
                                                    href="{{ route('admin.config-approves.show', ['config_approve' => $d, 'branch_id' => $branch_id]) }}"><i
                                                        class="fa fa-eye me-1"></i>
                                                    {{ __('config_approves.view') }}
                                                </a>
                                            @endcan
                                            @can(Actions::Manage . '_' . Resources::ConfigApprove)
                                                <a class="dropdown-item"
                                                    href="{{ route('admin.config-approves.edit', ['config_approve' => $d, 'branch_id' => $branch_id]) }}"><i
                                                        class="far fa-edit me-1"></i>
                                                    {{ __('lang.edit') }}
                                                </a>
                                            @endcan
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            {{-- <tr id="sub-section{{ $index }}" class="hidden hd">
                                <td colspan="6">
                                    <div class="table-wrap">
                                        <table class="table table-striped" :id="'sub-table-' + k">
                                            <thead class="bg-body-dark">
                                                <th style="width: 5%">{{ __('config_approves.seq') }}</th>
                                                <th style="width: 10%">
                                                    {{ __('config_approves.department') }}</th>
                                                <th style="width: 5%" class="text-center">
                                                    {{ __('config_approves.all_departmen') }}</th>
                                                <th style="width: 10%">{{ __('config_approves.role') }}
                                                </th>
                                                <th style="width: 5%" class="text-center">
                                                    {{ __('config_approves.all_role') }}</th>
                                                <th style="width: 10%">
                                                    {{ __('config_approves.full_name') }}</th>
                                                <th style="width: 5%" class="text-center">
                                                    {{ __('config_approves.super_user') }}</th>
                                            </thead>
                                            @if (count($d->config_lines) > 0)
                                                <tbody>
                                                    @foreach ($d->config_lines as $index2 => $d2)
                                                        <tr>
                                                            <td>{{ $d2->seq }}</td>
                                                            <td>{{ $d2->department_name }}</td>
                                                            <td class="text-center">
                                                                @if ($d2->is_all_department == 1)
                                                                    <i class="far fa-circle-check" aria-hidden="true"
                                                                        style="color: green "></i>
                                                                @endif
                                                            </td>
                                                            <td>{{ $d2->role_name }}</td>
                                                            <td class="text-center">
                                                                @if ($d2->is_all_role == 1)
                                                                    <i class="far fa-circle-check" aria-hidden="true"
                                                                        style="color: green "></i>
                                                                @endif
                                                            </td>
                                                            <td>{{ $d2->user_name }}</td>
                                                            <td class="text-center">
                                                                @if ($d2->is_super_user == 1)
                                                                    <i class="far fa-circle-check" aria-hidden="true"
                                                                        style="color: green "></i>
                                                                @endif
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            @else
                                                <tbody>
                                                    <tr class="table-empty" id='empty-data'>
                                                        <td class="text-center" colspan="7">" {{ __('lang.no_list') }} "
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            @endif
                                        </table>
                                    </div>
                                </td>
                            </tr> --}}
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
@include('admin.components.select2-default')
@include('admin.components.sweetalert')
@include('admin.components.list-delete')

@push('scripts')
    <script>
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
