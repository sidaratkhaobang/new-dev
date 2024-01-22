<div class="modal fade" id="modal-history-edit-contract" tabindex="-1" aria-labelledby="modal-edit-contract" aria-hidden="false">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-popout" style="width:1250px;">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="wage-job-modal-label">{{ __('ประวัติการแก้ไขข้อมูล') }} <span id="modal-title"></span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="save-form">
                <x-forms.hidden id="contract_id" :value="null"/>
                <div class="modal-body">
                    <div class="row mt-2">
                        <div id="table-show-history-edit-contract" data-detail-uri="" data-title="">
                            <div class="table-wrap table-responsive">
                                <table class="table table-striped">
                                    <thead class="bg-body-dark">
                                    <th>{{ __('ผู้แก้ไข') }}</th>
                                    <th>{{ __('วันที่แก้ไข') }}</th>
                                    <th>{{ __('ประเภทแก้ไข') }}</th>
                                    <th>{{ __('ทะเบียนรถ') }}</th>
                                    <th>{{ __('ข้อมูลก่อนแก้ไข') }}</th>
                                    <th>{{ __('ข้อมูลที่แก้ไข') }}</th>
                                    <th>{{ __('ผู้ยืนยันแก้ไขข้อมูล') }}</th>
                                    <th>{{ __('สถานะ') }}</th>
                                    <th>{{ __('หมายเหตุที่ไม่ยืนยัน') }}</th>
                                    </thead>
                                    <tbody>
                                    @if(isset($data->contract_log))
                                        @foreach($data->contract_log as $item)
                                            <tr>
                                                <td>{{$item->createdBy->name}}</td>
                                                <td>{{get_date_time_by_format($item->created_at)}}</td>
                                                <td>{{__('contract.status_text_' . $item->type_log)}}</td>
                                                <td>{{isset($item->car_id) ? $item->car->license_plate : ''}}</td>
                                                <td>
                                                    @if($item->type_log == \App\Enums\ContractEnum::REQUEST_CHANGE_ADDRESS)
                                                        {{$item->old_value}}
                                                    @elseif($item->type_log == \App\Enums\ContractEnum::REQUEST_CHANGE_USER_CAR)
                                                        @php
                                                            $old_value = json_decode($item->old_value,true);
                                                        @endphp
                                                        <span>ชื่อผู้ใช้ : {{$old_value['car_user']}}</span>
                                                        <br>
                                                        <span>เบอร์โทร : {{$old_value['car_phone']}}</span>
                                                    @elseif($item->type_log == \App\Enums\ContractEnum::REQUEST_TRANSFER_CONTRACT)
                                                        @php
                                                            $old_value = \App\Models\Customer::find($item->old_value);
                                                        @endphp
                                                        {{$old_value['customer_code'] . ' ' . $old_value['name']}}
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($item->type_log == \App\Enums\ContractEnum::REQUEST_CHANGE_ADDRESS)
                                                        {{$item->new_value}}
                                                    @elseif($item->type_log == \App\Enums\ContractEnum::REQUEST_CHANGE_USER_CAR)
                                                        @php
                                                            $new_value = json_decode($item->new_value,true);
                                                        @endphp
                                                        <span>ชื่อผู้ใช้ : {{$new_value['car_user']}}</span>
                                                        <br>
                                                        <span>เบอร์โทร : {{$new_value['car_phone']}}</span>
                                                    @elseif($item->type_log == \App\Enums\ContractEnum::REQUEST_TRANSFER_CONTRACT)
                                                        @php
                                                            $new_value = \App\Models\Customer::find($item->new_value);
                                                        @endphp
                                                        {{$new_value['customer_code'] . ' ' . $new_value['name']}}
                                                    @endif
                                                </td>
                                                <td>{{isset($item->approvedBy) ? $item->approvedBy->name : ''}}</td>
                                                <td>{!! badge_render(__('contract.status_class_'.$item->status),__('contract.status_text_'.$item->status)) !!}</td>
                                                <td>{{$item->reason}}</td>
                                            </tr>
                                        @endforeach
                                    @else
                                        <tr class="table-empty">
                                            <td class="text-center" colspan="7">"
                                                {{ __('lang.no_list') . __('ประวัติการแก้ไขข้อมูล') }} "
                                            </td>
                                        </tr>
                                    @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
            <div class="modal-footer"></div>
        </div>
    </div>
</div>
