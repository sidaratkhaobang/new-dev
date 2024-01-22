<table class="table-border main-table mt-2"  style="page-break-after: always">
    <tbody>
        <tr>
            <td class="text-center" style="font-size:18px;font-weight:bold;">สัญญาเช่ารถยนต์</td>
        </tr>
        <tr>
            <td>@php echo str_repeat('&nbsp;', 5) @endphp สัญญาเช่ารถยนต์("สัญญา") ฉบับนี้ทำขึ้น ณ บริษัท ทรูลิสซิ่ง
                จำกัด เมื่อวันที่ {{ '' }} โดยระหว่าง
            </td>
        </tr>
        <tr>
            <td>@php echo str_repeat('&nbsp;', 5) @endphp บริษัท ทรูลิสซิ่ง จำกัด
                @if (sizeof($attorney_host_list) > 0)
                    โดย
                @endif
                @foreach ($attorney_host_list as $attorney_host)
                    {{ $attorney_host->name }}
                    @if (!$loop->last)
                        และ
                    @endif
                @endforeach
                @if (sizeof($attorney_host_list) > 0)
                    ผู้รับมอบอำนาจ
                @endif
                สำนักงานใหญ่จดทะเบียนตั้งอยู่ เลขที่ 18 อาคารทรูทาวเวอร์ ถนนรัชดาภิเษก แขวงห้วยขวาง เขตห้วยขวาง
                กรุงเทพมหานคร
                10310 (ซึ่งต่อไปในสัญญานี้จะเรียกว่า"ผู้ให้เช่า") ฝ่ายหนึ่ง กับ
            </td>
        </tr>
        <tr>
            <td>@php echo str_repeat('&nbsp;', 5) @endphp
                {{ $lt_rental->customer_name }}
                @if (sizeof($attorney_renter_list) > 0)
                    โดย
                @endif
                @foreach ($attorney_renter_list as $attorney_renter)
                    {{ $attorney_renter->name }}
                    @if (!$loop->last)
                        และ
                    @endif
                @endforeach
                @if (sizeof($attorney_renter_list) > 0)
                    กรรรมการผู้มีอำนาจลงนาม
                @endif
                สำนักงานใหญ่จดทะเบียนตั้งอยู่
                {{ $lt_rental->customer_address }}
                (ซึ่งต่อไปในสัญญานี้จะเรียกว่า"ผู้ให้เช่า") ฝ่ายหนึ่ง กับ
            </td>
        </tr>
        <tr>
            <td>@php echo str_repeat('&nbsp;', 5) @endphp
                โดยที่ผู้เช่าประสงค์จะเช่ารถยนต์ของผู้ให้เช่าเพื่อใช้ในกิจการของผู้เช่า
                และโดยที่ผู้ให้เช่าเป็นผู้ประกอบกิจการให้เช่ารถยนต์
                และประสงค์จะให้เช่ารถยนต์แก่ผู้เช่าตามเงื่อนไขที่กำหนดไว้ในสัญญานี้
            </td>
        </tr>
        <tr>
            <td>@php echo str_repeat('&nbsp;', 5) @endphp คู่สัญญาทั้งสองฝ่ายจึงตกลงเข้าทำสัญญากัน ดังมีข้อความต่อไปนี้
            </td>
        </tr>
        @foreach ($contract_forms as $key => $contract_form)
            <tr>
                <td>{{ $key + 1 }}. @php echo str_repeat('&nbsp;', 3) @endphp
                    {{ $contract_form->name }}
                </td>
            </tr>
            @if (sizeof($contract_form->contract_form_check_lists) > 0)
                @foreach ($contract_form->contract_form_check_lists as $sub_key => $contract_form_check_list)
                    <tr>
                        <td>
                            {{-- @php echo str_repeat('&nbsp;', 5) @endphp --}}
                            <span>
                            {{ $key + 1 }}.{{ $sub_key + 1 }} @php echo str_repeat('&nbsp;', 3) @endphp
                                @php
                                    $array = mb_str_split($contract_form_check_list->name, 65, 'UTF-8'); // ใช้ UTF-8 เพื่อรองรับภาษาไทย
                                    $text = implode("\n", $array);
                                @endphp
                                {{ $text }}
                        </span>
                        </td>
                    </tr>
                @endforeach
            @endif
        @endforeach
    </tbody>
</table>
