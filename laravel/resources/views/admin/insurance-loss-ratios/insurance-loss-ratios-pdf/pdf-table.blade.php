<table class="table-collapse">
    <thead style="display: table-header-group">


    <tr>
        <td rowspan="2" class="text-center">ประกัน</td>
        <td rowspan="2" class="text-center">จำนวนรถ</td>
        <td colspan="4" class="text-center">ฝ่ายผิด</td>
        <td colspan="3" class="text-center">ฝ่ายถูก</td>
    </tr>
    <tr>
        {{--        <td rowspan="2">Row 2, Cell 1</td>--}}
        {{--        <td rowspan="2">Row 2, Cell 2</td>--}}
        <td colspan="1" class="text-center">จำนวนรถที่เกิดเหตุ</td>
        <td colspan="1" class="text-center">จำนวนเคลม</td>
        <td colspan="1" class="text-center">ค่าเสียหาย</td>
        <td colspan="1" class="text-center">L/R</td>
        <td colspan="1" class="text-center">จำนวนรถที่เกิดเหตุ</td>
        <td colspan="1" class="text-center">จำนวนเคลม</td>
        <td colspan="1" class="text-center">ค่าเสียหาย</td>
    </tr>
    </thead>
    <tbody>
    @if(!empty($dataTable))
        @foreach($dataTable as $keyData => $valueData)
            <tr>
                <td class="text-center">{{$valueData['name_insurance'] ?? '-'}}</td>
                <td class="text-center">{{$valueData['total_insurance_car'] ?? '-'}}</td>
                <td colspan="1" class="text-center">{{$valueData['total_false_car'] ?? '-'}}</td>
                <td colspan="1" class="text-center">{{$valueData['total_claim_false'] ?? '-'}}</td>
                <td colspan="1" class="text-center">{{$valueData['total_false_loss'] ?? '-'}}</td>
                <td colspan="1" class="text-center">{{$valueData['total_insurance_car'] ?? '-'}}</td>
                <td colspan="1" class="text-center">{{$valueData['total_true_car'] ?? '-'}}</td>
                <td colspan="1" class="text-center">{{$valueData['total_claim_true'] ?? '-'}}</td>
                <td colspan="1" class="text-center">{{$valueData['total_true_loss'] ?? '-'}}</td>
            </tr>
        @endforeach
    @endif
    {{--    <tr>--}}
    {{--        <td>data</td>--}}
    {{--        <td>data</td>--}}
    {{--    </tr>--}}
    </tbody>
</table>
