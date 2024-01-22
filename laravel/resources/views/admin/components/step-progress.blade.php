<div class="block {{ __('block.styles') }}">
    @include('admin.components.block-header', [
        'text' => __('install_equipment_pos.step_approve'),
    ])
    {{-- <div class="block-content"> --}}
        {{-- <h4>{{ __('install_equipment_pos.step_approve') }}</h4>
        <hr> --}}
        <div class="row">
            <div class="form-progress-bar">
                <div class="form-progress-bar-header">
                    <ul class="list-unstyled form-progress-bar-steps d-flex justify-content-center align-items-center">
                        @if (is_array($approve_line_list))
                        @foreach ($approve_line_list as $index => $item)
                        @if ($item['is_pass'] == 1)
                            <div class="check-status" style="width:250px;">
                                <span class="check">
                                    <i class="fa fa-check"></i>
                                </span>
                                <span class="text-dark text-center mb-2">
                                    @if ($item['user_name'] && $item['department_name'] && $item['role_name'])
                                        {{ $item['user_name'] }} <br> ({{ $item['department_name'] }} ,
                                        {{ $item['role_name'] }})
                                    @elseif($item['department_name'] && $item['role_name'])
                                        {{ $item['department_name'] }} ({{ $item['role_name'] }})
                                    @elseif($item['department_name'])
                                        {{ $item['department_name'] }}
                                    @endif
                                </span>
                                <p class="text-white">{!! badge_render('success', __('install_equipment_pos.status_approve_CONFIRM')) !!}</p>
                            </div>
                            @if (!$loop->last)
                                <li class="form-progress-bar-line"></li>
                            @endif
                        @elseif($item['seq'] == $approve->status_state && is_null($item['is_pass']))
                            <div class="check-status" style="width:250px;">
                                <span class="pending">
                                    <i class="fas fa-clock"></i>

                                </span>
                                <span class="text-dark text-center mb-2">
                                    @if ($item['user_name'] && $item['department_name'] && $item['role_name'])
                                    {{ $item['user_name'] }} <br> ({{ $item['department_name'] }} ,
                                    {{ $item['role_name'] }})
                                @elseif($item['department_name'] && $item['role_name'])
                                    {{ $item['department_name'] }} ({{ $item['role_name'] }})
                                @elseif($item['department_name'])
                                    {{ $item['department_name'] }}
                                @endif
                                </span>
                                <p class="text-white">{!! badge_render('warning', __('install_equipment_pos.status_approve_PENDING')) !!}</p>
                            </div>
                            @if (!$loop->last)
                                <li class="form-progress-bar-line"></li>
                            @endif
                        @elseif($item['seq'] > $approve->status_state)
                            <div class="check-status" style="width:250px;">
                                <span class="pending-secondary">
                                    <i class="fas fa-clock"></i>
                                </span>
                                <span class="text-dark text-center mb-2">
                                    @if ($item['user_name'] && $item['department_name'] && $item['role_name'])
                                        {{ $item['user_name'] }} <br> ({{ $item['department_name'] }} ,
                                        {{ $item['role_name'] }})
                                    @elseif($item['department_name'] && $item['role_name'])
                                        {{ $item['department_name'] }} ({{ $item['role_name'] }})
                                    @elseif($item['department_name'])
                                        {{ $item['department_name'] }}
                                    @endif
                                </span>
                                <p class="text-white">{!! badge_render('secondary', __('install_equipment_pos.status_approve_PENDING_PREVIOUS')) !!}</p>
                            </div>
                            @if (!$loop->last)
                                <li class="form-progress-bar-line"></li>
                            @endif
                            @elseif($item['is_pass'] == 0)
                            <div class="check-status" style="width:250px;">
                                <span class="reject">
                                    <i class="fa fa-times"></i>

                                </span>
                                <span class="text-dark text-center mb-2">
                                    @if ($item['user_name'] && $item['department_name'] && $item['role_name'])
                                        {{ $item['user_name'] }} <br> ({{ $item['department_name'] }} ,
                                        {{ $item['role_name'] }})
                                    @elseif($item['department_name'] && $item['role_name'])
                                        {{ $item['department_name'] }} ({{ $item['role_name'] }})
                                    @elseif($item['department_name'])
                                        {{ $item['department_name'] }} 
                                    @endif
                                </span>
                                <p class="text-white text-center">{!! badge_render(__('install_equipment_pos.class_approve_REJECT'), __('install_equipment_pos.status_approve_REJECT')) !!} 
                                   <span class="text-dark">@if($item['reason']) <br> ({{ $item['reason'] }}) @endif</span> </p>
                                
                            </div>
                            @if (!$loop->last)
                                <li class="form-progress-bar-line"></li>
                            @endif
                        @endif
                    @endforeach
                        @endif
                    </ul>
                </div>
            </div>
        </div>

    {{-- </div> --}}
</div>
