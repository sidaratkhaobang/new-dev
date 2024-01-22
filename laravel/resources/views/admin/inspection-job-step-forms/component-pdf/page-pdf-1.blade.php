<p class="" style=" font-size:18px; text-align:center; font-weight:bold;">
    {{ $data->name ? 'แบบฟอร์มการตรวจรถ' . $data->name : null }}</p>
<table class="table-border">
    <tbody>
        @if (in_array($step_form_check_condition->form_type, [InspectionFormEnum::NEWCAR]))
            <tr>
                <td style="width:50%;">
                    <span style="width: 200px; font-size:16px;">ผู้ขาย/Dealer : {{ $creditor->creditor->name }}</span>
                </td>
            </tr>
        @endif
        <tr>
            <td>
                <span style="style=width:30%; font-size:16px;">ยี่ห้อ : {{ $car->car_brand_name }}</span>
            </td>
            <td>
                <span style=" style=width:30%; font-size:16px;">รุ่น : {{ $car->car_class_name }}</span>
            </td>
        </tr>
        <tr>
            <td>
                <span style=" style=width:30%; font-size:16px;">เลขเครื่อง : {{ $car->engine_no }}</span>
            </td>
            <td>
                <span style="style=width:30%; font-size:16px;">เลขตัวถัง : {{ $car->chassis_no }}</span>
            </td>
        </tr>
        <tr>
            <td>
                <span style="style=width:30%; font-size:16px;">CC : {{ $car->engine_size }}</span>
            </td>
            <td>
                <span style="style=width:30%; font-size:16px;">สี : {{ $car->car_colors_name }}</span>
            </td>
        </tr>
    </tbody>
</table>
<span style="font-size:18px; font-weight:bold;">ข้อมูลผู้ตรวจ</span>
<table class="table-border">
    <tbody>
        <tr>
            <td style="width:30%;">
                <span style="width: 200px; font-size:16px;">แผนกที่รับผิดชอบ :
                    {{ $step_form_check_condition->name }}</span>
            </td>
            <td style="width:30%;">
                <span style="width: 200px; font-size:16px;">ชื่อ-นามสกุล ผู้ตรวจ :
                    {{ $step_form_status->name ? $step_form_status->name : $step_form_status->inspector_fullname }}</span>
            </td>
            <td style="width:20%;">
                <span style="width: 200px; font-size:16px;">สถานที่ตรวจ : </span>
            </td>
            <td style="width:20%;">
                <span style="width: 200px; font-size:16px;">วันที่ตรวจ :
                    {{ $step_form_status->inspection_date ? get_thai_date_format($step_form_status->inspection_date, 'd/m/Y') : null }}</span>
            </td>
        </tr>
    </tbody>
</table>
<span style="font-size:18px; font-weight:bold;">ข้อมูลพื้นฐาน</span>
<table class="table-border">
    <tbody>
        <tr>
            @if ($step_form_check_condition->transfer_type == App\Enums\TransferTypeEnum::OUT)
                <td style="width:30%;">
                    <span style="width: 200px; font-size:16px;">ปริมาณน้ำมัน (ขาออก) :
                        {{ $step_form_status->oil_quantity }} %</span>
                </td>
            @else
                <td style="width:30%;">
                    <span style="width: 200px; font-size:16px;">ปริมาณน้ำมัน (ขาเข้า) :
                        {{ $step_form_status->oil_quantity }} %</span>
                </td>
            @endif
            @if (in_array($step_form_check_condition->form_type, [InspectionFormEnum::EQUIPMENT]))
                @if ($step_form_check_condition->transfer_type == App\Enums\TransferTypeEnum::OUT)
                    <td style="width:30%;">
                        <span style="width: 200px; font-size:16px;">น้ำยา DPF (ขาออก) : {{ $step_form_status->dpf_solution }} %</span>
                    </td>
                @else
                    <td style="width:30%;">
                        <span style="width: 200px; font-size:16px;">น้ำยา DPF (ขาเข้า) : {{ $step_form_status->dpf_solution }} %</span>
                    </td>
                @endif
            @endif
            @if ($step_form_check_condition->transfer_type == App\Enums\TransferTypeEnum::OUT)
                <td style="width:30%;">
                    <span style="width: 200px; font-size:16px;">เลขไมล์ (ขาออก) :
                        {{ $step_form_status->mileage }}</span>
                </td>
            @else
                <td style="width:30%;">
                    <span style="width: 200px; font-size:16px;">เลขไมล์ (ขาเข้า) :
                        {{ $step_form_status->mileage }}</span>
                </td>
            @endif
            <td style="width:20%;">
                <span style="width: 200px; font-size:16px;"></span>
            </td>
        </tr>
    </tbody>
</table>
@if ($step_form_check_condition->is_need_images == STATUS_ACTIVE)
    <span style="font-size:18px; font-weight:bold;">ข้อมูลรูปถ่ายรถรอบคัน</span>
    <table class="table-border">
        <thead>
            <tr class="border-tr">
                <th style="width:20%" class="text-left">รูปถ่ายที่กำหนด</th>
                <th style="width:80%" class="text-left">รูปถ่าย</th>
                {{-- <th style="width:40% ">ขาเข้าคลัง</th> --}}
            </tr>
        </thead>
        <tbody>
            <tr style="height:200px">
                <td style="width:30%; height:60px;">
                    <span style="width: 200px; font-size:16px;">รูปถ่ายด้านหน้า : </span>
                </td>
                <td>
                    @foreach ($front_image_files_out as $index => $d)
                        <img src="./storage/{{ $front_image_files_out[$index]['id'] }}/{{ $front_image_files_out[$index]['file_name'] }}"
                            style="width:50px; height:50px; margin-top:20px; margin-bottom:-15px; border-style: solid; border-width:1.5px;"
                            alt="">
                    @endforeach
                </td>
                {{-- <td>
                    @foreach ($front_image_files_in as $index => $d)
                        <img src="./storage/{{ $front_image_files_in[$index]['id'] }}/{{ $front_image_files_in[$index]['file_name'] }}"
                            style="width:50px; height:50px; margin-top:20px; margin-bottom:-15px; border-style: solid; border-width:1.5px;"
                            alt="">
                    @endforeach
                </td> --}}

            </tr>
            <tr>
                <td>&nbsp;</td>
            </tr>
            <tr>
                <td style="width:30%; height:60px;">
                    <span style="width: 200px; font-size:16px;">รูปถ่ายด้านหลัง : </span>
                </td>
                <td>
                    @foreach ($back_image_files_out as $index => $d)
                        <img src="./storage/{{ $back_image_files_out[$index]['id'] }}/{{ $back_image_files_out[$index]['file_name'] }}"
                            style="width:50px; height:50px; margin-top:20px; margin-bottom:-15px; border-style: solid; border-width:1.5px;"
                            alt="">
                    @endforeach
                </td>
                {{-- <td>
                    @foreach ($back_image_files_in as $index => $d)
                        <img src="./storage/{{ $back_image_files_in[$index]['id'] }}/{{ $back_image_files_in[$index]['file_name'] }}"
                            style="width:50px; height:50px; margin-top:20px; margin-bottom:-15px; border-style: solid; border-width:1.5px;"
                            alt="">
                    @endforeach
                </td> --}}

            </tr>
            <tr>
                <td>&nbsp;</td>
            </tr>
            <tr>
                <td style="width:30%; height:60px;">
                    <span style="width: 200px; font-size:16px;">รูปถ่ายด้านขวา : </span>
                </td>
                <td>
                    @foreach ($right_image_files_out as $index => $d)
                        <img src="./storage/{{ $right_image_files_out[$index]['id'] }}/{{ $right_image_files_out[$index]['file_name'] }}"
                            style="width:50px; height:50px; margin-top:20px; margin-bottom:-15px; border-style: solid; border-width:1.5px;"
                            alt="">
                    @endforeach
                </td>
                {{-- <td>
                    @foreach ($right_image_files_in as $index => $d)
                        <img src="./storage/{{ $right_image_files_in[$index]['id'] }}/{{ $right_image_files_in[$index]['file_name'] }}"
                            style="width:50px; height:50px; margin-top:20px; margin-bottom:-15px; border-style: solid; border-width:1.5px;"
                            alt="">
                    @endforeach
                </td> --}}
            </tr>
            <tr>
                <td>&nbsp;</td>
            </tr>
            <tr style="height:150px;">
                <td style="width:30%; height:60px;">
                    <span style="width: 200px; font-size:16px;">รูปถ่ายด้านซ้าย : </span>
                </td>
                {{-- <td>
                    @foreach ($left_image_files_out as $index => $d)
                        <img src='./storage/{{ $left_image_files_out[$index]['id'] }}/{{ $left_image_files_out[$index]['file_name'] }}'
                            style="width:50px; height:50px; margin-top:20px; margin-bottom:-15px; border-style: solid; border-width:1.5px;"
                            alt="">
                    @endforeach
                </td> --}}
                <td>

                    @foreach ($left_image_files_in as $index => $d)
                        <img src="./storage/{{ $left_image_files_in[$index]['id'] }}/{{ $left_image_files_in[$index]['file_name'] }}"
                            style="width:50px; height:50px; margin-top:20px; margin-bottom:-15px; border-style: solid; border-width:1.5px;"
                            alt="">
                    @endforeach
                </td>
            </tr>
            <tr>
                <td>&nbsp;</td>
            </tr>
            <tr style="height:150px;">
                <td style="width:30%; height:60px;">
                    <span style="width: 200px; font-size:16px;">รูปถ่ายด้านบน : </span>
                </td>
                <td>
                    @foreach ($top_image_files_out as $index => $d)
                        <img src='./storage/{{ $top_image_files_out[$index]['id'] }}/{{ $top_image_files_out[$index]['file_name'] }}'
                            style="width:50px; height:50px; margin-top:20px; margin-bottom:-15px; border-style: solid; border-width:1.5px;"
                            alt="">
                    @endforeach
                </td>
                {{-- <td>

                    @foreach ($top_image_files_in as $index => $d)
                        <img src="./storage/{{ $top_image_files_in[$index]['id'] }}/{{ $top_image_files_in[$index]['file_name'] }}"
                            style="width:50px; height:50px; margin-top:20px; margin-bottom:-15px; border-style: solid; border-width:1.5px;"
                            alt="">
                    @endforeach
                </td> --}}
            </tr>

            <tr class="border-tr-bottom">
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
            </tr>
        </tbody>
    </table>
@endif

<span style="font-size:18px; font-weight:bold;">รายการตรวจเช็ค</span><br>

<table>
    <tr>
        <th colspan="5" class="text-left">
            ลำดับการตรวจ
        </th>
    </tr>

    <tbody>
        @foreach ($list as $index => $d)
            <table>
                <thead>
                    <tr>
                        <td style="width:10px;" class="text-left">{{ $index + 1 }}</td>
                        <td style="text-align: left;">{{ $d->name }}</td>
                    </tr>
                </thead>
            </table>
            <tr>
                <td>
            <tr>
                <th style="width:40%;"></th>
                <th style="width:20%;"> มี/ปกติ </th>
                <th style="width:20%;"> ไม่มี/ไม่ปกติ </th>
                <th style="width:20%;"> หมายเหตุ </th>
            </tr>
            @foreach ($d->subseq as $index2 => $d2)
                <tr>
                    <td style="width:40%;">{{ $d2->name2 }}</td>
                    @if ($d2->is_pass == 1)
                        <td style="width:20%;" class="text-center"><img
                                src="{{ base_path('storage/logo-pdf/checkbox.png') }}"
                                style=" width:10px; height:10px;" alt=""></td>
                    @else
                        <td style="width:20%;" class="text-center"><img
                                src="{{ base_path('storage/logo-pdf/no-check.png') }}"
                                style="width:10px; height:10px;" alt=""></td>
                    @endif
                    @if ($d2->is_pass == 0 && $d2->is_pass != '')
                        <td style="width:20%;" class="text-center"><img
                                src="{{ base_path('storage/logo-pdf/checkbox.png') }}"
                                style=" width:10px; height:10px;" alt=""></td>
                    @else
                        <td style="width:20%;" class="text-center"><img
                                src="{{ base_path('storage/logo-pdf/no-check.png') }}"
                                style="width:10px; height:10px;" alt=""></td>
                    @endif

                    <td style="width:20%;" class="text-center">{{ $d2->remark }}</td>

                </tr>
            @endforeach
            </td>
            </tr>
        @endforeach
    </tbody>
</table>
<span style="font-size:18px; font-weight:bold;">ผลการตรวจรถ</span>
<table class="table-border">
    <tbody>
        <tr>
            @if ($step_form_status->inspection_status == \App\Enums\InspectionStatusEnum::PASS)
                <td style="width:7%;">
                    <span style="width: 10px; font-size:16px;"><img
                            src="{{ base_path('storage/logo-pdf/checkbox.png') }}" style=" width:10px; height:10px;"
                            alt=""> &nbsp;ผ่าน&nbsp;</span>
                </td>
                <td style="">
                    <span style="width: 10px; font-size:16px;"><img
                            src="{{ base_path('storage/logo-pdf/no-check.png') }}" style=" width:10px; height:10px;"
                            alt=""> &nbsp;ไม่ผ่าน</span>
                </td>
            @elseif($step_form_status->inspection_status == \App\Enums\InspectionStatusEnum::NOT_PASS)
                <td style="width:7%;">
                    <span style="width: 10px; font-size:16px;"><img
                            src="{{ base_path('storage/logo-pdf/no-check.png') }}" style=" width:10px; height:10px;"
                            alt=""> &nbsp;ผ่าน&nbsp;</span>
                </td>
                <td>
                    <span style="width: 10px; font-size:16px;"><img
                            src="{{ base_path('storage/logo-pdf/checkbox.png') }}" style="width:10px; height:10px;"
                            alt=""> &nbsp;ไม่ผ่าน</span>
                </td>
            @else
                <td style="width:7%;">
                    <span style="width: 10px; font-size:16px;"><img
                            src="{{ base_path('storage/logo-pdf/no-check.png') }}" style=" width:10px; height:10px;"
                            alt=""> &nbsp;ผ่าน</span>
                </td>

                <td>
                    <span style="width: 10px; font-size:16px;"><img
                            src="{{ base_path('storage/logo-pdf/no-check.png') }}" style="width:10px; height:10px;"
                            alt=""> &nbsp;ไม่ผ่าน</span>
                </td>
            @endif
        </tr>
    </tbody>
</table>
@if ($step_form_check_condition->is_need_inspector_sign == STATUS_ACTIVE)
    @if ($signature != null)
        <span style="font-size:18px; font-weight:bold;">ส่งมอบรถ</span><br>
        <img src="./storage/{{ $signature['id'] }}/{{ $signature['file_name'] }}"
            style="width:100px; height:60px; margin-top:30px;" alt="">
        <p style="margin-top:-20px;">(..................................................................)</p>
    @else
        <span style="font-size:18px; font-weight:bold;">ส่งมอบรถ</span><br>
        <p style="margin-left: 18px;">......................................................</p>
        <p>(..................................................................)</p>
    @endif
@endif
