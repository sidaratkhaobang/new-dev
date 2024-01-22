<div class="block {{ __('block.styles') }}">
    <div class="block-content">
        <div class="justify-content-between mb-4">
            <h4 class="grey-text">{{ __('short_term_rentals.work_detail') }}</h4>
            <hr>
            <div class="row push mb-4 ">
                <div class="col-sm-2 mt-2">
                    {{ __('operations.contract_no') }} @if ($operation->status == RentalStatusEnum::PAID)
                        <span class="text-danger"></span>
                    @endif
                </div>
                <div class="col-sm-2 mt-2">
                    @if (!empty($contract))
                    <div class="d-flex justify-content-center align-items-center">
                        <div class="px-2">
                            @php
                                $link = route('admin.contracts.show', ['contract' => $contract]);
                            @endphp 
                            @can(Actions::Manage . '_' . Resources::ContractAllList)
                                @php
                                    $link = route('admin.contracts.edit', ['contract' => $contract]);
                                @endphp                                                               
                            @endcan
                            <a href="{{ route('admin.contracts.print-pdf', ['contract' => $contract]) }}" target="_blank"
                                class="text-nowrap">{{ $contract->worksheet_no }}</a>
                        </div>
                        <div class="px-2 border-start">
                            <a class="fw-bolder" href="{{ $link }}" target="_blank">
                                <i class="si si-link text-dark"></i>
                            </a>
                        </div>
                    </div>
                    @endif
                </div>
                @if (isset($view))
                    <div class="col-sm-2 mt-2">
                        @if (!empty($contract_file))
                            @foreach ($contract_file as $item)
                                @if (!empty($item['url']))
                                    <a href="{{ $item['url'] }}" target="_blank">
                                        <i class="fa fa-download text-primary"></i>
                                        {{ $item['name'] }}
                                    </a>
                                    <br>
                                @endif
                            @endforeach
                        @endif
                    </div>
                @else
                    <div class="col-sm-2">
                            <div id="contract_file" class="dropzone file-upload" class="custom-file-image">
                                <div class="file-select dropzone-area" id="contract_file-area">
                                    <div class="file-select-button" id="contract_file">ไฟล์แนบ @if ($operation->status == RentalStatusEnum::PAID)
                                            <span class="text-danger"> *</span>
                                        @endif
                                    </div>
                                    <div id="contract_file-area" class="file-select-name">
                                        <span>กรุณาเลือกไฟล์ (ขนาดไฟล์ไม่เกิน 10 MB)</span>
                                    </div>
                                </div>
                                <div class="contract_file-previews">
                                </div>
                            </div>
                    </div>
                @endif
                <div class="col-sm-2 mt-2">
                    {{ __('operations.receipt_no') }}</div>
                <div class="col-sm-2 mt-2">
                    @if (!empty($receipt))
                    <div class="d-flex justify-content-center align-items-center">
                        <div class="px-2">
                            @php
                                $link = route('admin.receipts.show', ['receipt' => $receipt]);
                            @endphp 
                            @can(Actions::Manage . '_' . Resources::Receipt)
                                @php
                                    $link = route('admin.receipts.edit', ['receipt' => $receipt]);
                                @endphp                                                               
                            @endcan
                            <a href="{{ route('admin.receipts.pdf', ['receipt' => $receipt]) }}" target="_blank"
                                class="text-nowrap">{{ $receipt->worksheet_no }}</a>
                        </div>
                        <div class="px-2 border-start">
                            <a class="fw-bolder" href="{{ $link }}" target="_blank">
                                <i class="si si-link text-dark"></i>
                            </a>
                        </div>
                    </div>
                    @endif
                </div>
                @if (isset($view))
                    <div class="col-sm-2 mt-2">
                        @if (!empty($receipt_file))
                            @foreach ($receipt_file as $item)
                                @if (!empty($item['url']))
                                    <a href="{{ $item['url'] }}" target="_blank">
                                        <i class="fa fa-download text-primary"></i>
                                        {{ $item['name'] }}
                                    </a>
                                    <br>
                                @endif
                            @endforeach
                        @endif
                    </div>
                @else
                    <div class="col-sm-2 mb-2">
                        <div id="receipt_file" class="dropzone file-upload" class="custom-file-image">
                            <div class="file-select dropzone-area" id="receipt_file-area">
                                <div class="file-select-button" id="receipt_file">ไฟล์แนบ
                                </div>
                                <div id="receipt_file-area" class="file-select-name">
                                    <span>กรุณาเลือกไฟล์ (ขนาดไฟล์ไม่เกิน 10 MB)</span>
                                </div>
                            </div>
                            <div class="receipt_file-previews"></div>
                        </div>
                    </div>
                @endif
            </div>
            <x-forms.hidden id="rental_id" :value="$operation->id" />

            <div class="block block-rounded">
                <div class="row">
                    <div class="col-sm-12">
                        <ul class="nav nav-tabs nav-tabs-alt" role="tablist">
                            @php$count_car = count($operation_new); @endphp
                            @foreach ($operation_new as $index => $car)
                                <li class="nav-item">
                                    <button class="nav-link @if ($loop->first) active @endif"
                                        id="btabs-alt-static-info{{ $index }}-tab" data-bs-toggle="tab"
                                        data-bs-target="#btabs-alt-static-info{{ $index }}" role="tab"
                                        aria-controls="btabs-alt-static-info{{ $index }}"
                                        aria-selected="true">{{ $car->license_plate }}
                                        {{ $car->self_drive_type != SelfDriveTypeEnum::OTHER ? __('operations.sd_' . $car->self_drive_type) : '' }}
                                        {{ $car->driving_job_type == DrivingJobTypeStatusEnum::SIDE_JOB ? '(' . __('operations.drivig_type_' . $car->driving_job_type) . ')' : '' }}</button>
                                </li>
                            @endforeach
                        </ul>
                        <form id="save-form">
                            <div class="block-content tab-content">
                                @foreach ($operation_new as $index => $car)
                                    <div class="tab-pane @if ($loop->first) active @endif"
                                        id="btabs-alt-static-info{{ $index }}" role="tabpanel"
                                        aria-labelledby="btabs-alt-static-info{{ $index }}-tab">
                                        @include('admin.operations.sections.tab-content')
                                        <x-forms.hidden id="data[{{ $index }}][driving_job_id]"
                                            :value="$operation_new[$index]->driving_job_id" />
                                        <x-forms.hidden id="data[{{ $index }}][car_id]" :value="$operation_new[$index]->car_id" />
                                        <x-forms.hidden id="data[{{ $index }}][self_drive_type]"
                                            :value="$operation_new[$index]->self_drive_type" />
                                        <x-forms.hidden id="data[{{ $index }}][driving_job_type]"
                                            :value="$operation_new[$index]->driving_job_type" />
                                        <input type="hidden" id="index" value="{{ $index }}" />
                                    </div>
                                @endforeach
                                <h4 class="grey-text">{{ __('operations.status') }}</h4>
                                <hr>
                                <div class="col-sm-2">
                                    <x-forms.select-option id="status" :value="$operation->status" :list="$status_lists"
                                        :label="__('operations.status')" />
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
</div>
@push('styles')
    <style>
        .block-car-card {
            height: 200px !important;
        }

        .item-block {
            margin-top: 5% !important;

        }

        .text-block {
            margin-top: auto !important;
            width: 70% !important;
        }

        .block-content {
            height: 80%;
            width: 100%
        }
    </style>
@endpush
@push('scripts')
    <script>
        $status = '{{ isset($view) }}';
        if ($status) {
            $('.form-control').prop('disabled', true);
            $("input[type='radio']").attr('disabled', true);
            $("input[type='checkbox']").attr('disabled', true);
        }

        $(function() {
            $("button").click(function(e) {
                e.preventDefault();
            });
        });
    </script>

    <script>
        $('.disable').prop('disabled', true);
        var list = @json($operation_new);
        list.forEach((item, index) => {
            var check_show_alcohol = item.alcohol_check;
            if (check_show_alcohol != {{ STATUS_ACTIVE }}) {
                $("#alcohol_check" + index).show();
                $("#alcohol_check_radio" + index).show();
            } else {
                $("#alcohol_check" + index).hide();
                $("#alcohol_check_radio" + index).hide();
            }

            $("#alcohol_not_pass" + index).click(function() {
                $("#alcohol_check" + index).show();
                $("#alcohol_check_radio" + index).show();
            });

            $("#alcohol_pass" + index).click(function() {
                $("#alcohol_check" + index).hide();
                $("#alcohol_check_radio" + index).hide();
            });

            if (item.keys_address == null && (item.self_drive_type != '{{ SelfDriveTypeEnum::SEND }}' && item
                    .self_drive_type != '{{ SelfDriveTypeEnum::OTHER }}')) {
                console.log('fddsds');
                $("select[name='data[" + index + "][key_address]']").prop('disabled', true);
            }

            item.product.forEach((item2, index2) => {

                if (item2.inbound_approve == {{ STATUS_ACTIVE }}) {
                    $("input[name='data[" + index + "][product][" + item2.id + "][inbound_remark]']")
                        .hide();
                }

                $("input[id='data[" + index + "][product][" + item2.id + "][check_in_pass]']").click(
                    function() {
                        $("input[name='data[" + index + "][product][" + item2.id +
                            "][inbound_remark]']").hide();
                    });

                $("input[id='data[" + index + "][product][" + item2.id + "][check_in_not_pass]']").click(
                    function() {
                        $("input[name='data[" + index + "][product][" + item2.id +
                            "][inbound_remark]']").show();
                    });
            });

        });
    </script>
@endpush
