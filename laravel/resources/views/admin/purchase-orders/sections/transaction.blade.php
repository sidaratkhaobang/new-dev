@if (isset($d->audits))
<h4>{{ __('purchase_orders.transaction') }}</h4>
<hr>
<div class="mb-5">
    <div class="table-wrap">
        <table class="table table-striped">
            <thead class="bg-body-dark">
                <th>#</th>
                <th>{{ __('purchase_orders.editor') }}</th>
                <th>{{ __('purchase_orders.edit_at') }}</th>
                <th>{{ __('lang.remark') }}</th>
            </thead>
            <tbody>
                @php $i = 1; @endphp
                @foreach ($d->audits->reverse() as $index => $audit)
                    <tr>
                        <td>{{ $i }}</td>
                        <td>{{ ($audit->user) ? $audit->user->name : '' }}</td>
                        <td>{{ get_thai_date_format($audit->created_at, 'd/m/Y H:i') }}</td>
                        <td>
                            @foreach ($audit->getModified() as $attribute => $modified)
                                @if (strcmp($attribute, 'status') === 0 && $modified['new'] == \App\Enums\POStatusEnum::CONFIRM)
                                    {{ __('purchase_orders.status_'.\App\Enums\POStatusEnum::CONFIRM) }}
                                @endif
                                @if (strcmp($attribute, 'status') === 0 && $modified['new'] == \App\Enums\POStatusEnum::REJECT)
                                    {{ __('purchase_orders.status_'.\App\Enums\POStatusEnum::REJECT) }}
                                @endif
                            @endforeach
                        </td>
                    </tr>
                    @php $i += 1; @endphp
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endif