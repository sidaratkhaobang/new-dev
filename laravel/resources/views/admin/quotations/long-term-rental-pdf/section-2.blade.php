<table class="text-left">
    <thead class="thead-class">
        <tr>
            <th class="font-mt">เงื่อนไขการให้บริการ</th>
        </tr>
    </thead>
    <tbody>
        @if (sizeof($quotation_form) > 0)
            @foreach ($quotation_form as $index => $item)
                <tr>
                    <td class="line-mt"  style="text-align: left;">{{ $index + 1 }}. {{ $item->name }} <br>
                        @if (sizeof($item->sub_quotation_form_checklist) > 0)
                            @foreach ($item->sub_quotation_form_checklist as $key_checklist => $item_checklist)
                                &nbsp;&nbsp;- {{ $item_checklist->quotation_form_checklist_name }} <br>
                            @endforeach
                        @endif
                    </td>
                </tr>
            @endforeach
        @endif
    </tbody>
</table>
