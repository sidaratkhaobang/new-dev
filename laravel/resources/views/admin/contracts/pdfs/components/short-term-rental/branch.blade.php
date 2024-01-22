<table class="table-border">
    <tbody>
        <tr>
            <td style="font-size:18px;font-weight:bold;">Branch Offices</td>
        </tr>
        {{-- <tr class="border-tr-bottom">
           <td></td>
        </tr> --}}
    </tbody>
</table>

<table class="table-border mb-1">
    <tbody>
        @foreach ($branches as $branch)
            @if ($loop->iteration % 3 == 1)
                <tr>
            @endif
            <td style="width:10%;" class="text-left">
                <img src="{{ $rental->branch_id == $branch->id ? base_path('storage/logo-pdf/checkbox.png') : base_path('storage/logo-pdf/no-check.png') }}" 
                    style="width:10px; height:10px;"
                    alt=""> {{ $branch->name }}</td>
            @if ($loop->iteration % 3 == 0 || $loop->last)
                </tr>
            @endif
        @endforeach
    </tbody>
</table>