<div class="topic">
    <div class="header-text-l">
        {{-- <img src="{{ base_path('storage/logo-pdf/true.jpg') }}" style="float: top;" alt=""> --}}
        <p style="margin-top: 80px;" class="left-p">ฉัตรเมือง</p>
        <p style="font-size: 15px;">4069 ถ.พระราม 4 แขวงพระโขนง</p>
        <p>เขตคลองเตย กทม. 10260 </p>
        <br>

        {{-- <p style="font-weight: bold;">เรียน</p>
    <p class="left-p">{{ $customer_name }}</p>
    <p style="font-size: 14px; line-height: 14px; margin-top: 5px;">{{ $customer_address }}</p>
    <p style="font-size: 14px;">โทร {{ $customer_tel }}</p> --}}
        <p style="font-weight: bold;">ข้อมูลรถ</p>
        <p>ทะเบียนรถ : {{ $car->license_plate ? $car->license_plate : '-' }}</p>
        <p>หมายเลขเครื่องยนต์ : {{ $car->engine_no ? $car->engine_no : '-' }}</p>
        <p>เลขที่สัญญา : {{ $contract && $contract->worksheet_no ? $contract->worksheet_no : '-' }}</p>

    </div>
    <div class="header-text-l">
        <p style="font-weight: bold; font-size: 25px;">{{ $page_title }}</p>
    </div>

    <div class="header-text-l">
        {{-- <hr> --}}
        <div class="display-left">
            <p style="margin-top: 80px;"></p>
            <p></p>
            <p></p>
            <p></p>
            <p></p><br>
            <p></p>
            <p></p>
            <p>รุ่นรถ : {{ $car->carClass && $car->carClass->full_name ? $car->carClass->full_name : '-' }}</p>
            <p>หมายเลขตัวถัง : {{ $car->chassis_no ? $car->chassis_no : '-' }}</p>
            <p>{{ $contract && $contract->date_send_contract && $contract->date_return_contract ? $contract->date_send_contract . ' - ' . $contract->date_return_contract : '-' }}
            </p>
        </div>

    </div>
</div>
<table class="table" style="width:60%;">
    <thead style="display: table-header-group">
        {{-- @php
            $span = $lt_rental_month->count();
            $span_line = $lt_rental_lines->count();
        @endphp --}}

        <tr style="line-height: 14px;">
            <th style="width:15%; text-align: left;">ลำดับที่</th>
            <th style="width:45%; text-align: left;">รายการ</th>
            <th style="width:15%; text-align: left;">ลักษณะแผล</th>

        </tr>

    </thead>
    <tbody>
        @foreach ($d as $index => $data)
            @if (($index + 1) % 20 == 0 && $index > 0)
                <tr style="page-break-after: always">
                    <td style="width:15%; text-align: left;">{{ $index + 1 }}</td>
                    <td style="width:45%; text-align: left;">{{ $data->accident_claim_text }}</td>
        <td style="width:15%; text-align: left;">{{ $data->wound_characteristics_text }}</td>
                </tr>
            @else
                <tr>
                    <td style="width:15%; text-align: left;">{{ $index + 1 }}</td>
                    <td style="width:45%; text-align: left;">{{ $data->accident_claim_text }}</td>
        <td style="width:15%; text-align: left;">{{ $data->wound_characteristics_text }}</td>
                </tr>
            @endif
        @endforeach
    </tbody>
</table>
<br>
