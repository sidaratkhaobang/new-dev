@if (isset($d->audits))
    <br>
    <h4>{{ __('purchase_requisitions.transaction') }}</h4>
    <hr>
    <div class="mb-5">
        <div class="table-wrap">
            <table class="table table-striped">
                <thead class="bg-body-dark">
                    <th>#</th>
                    <th>{{ __('purchase_requisitions.editor') }}</th>
                    <th>{{ __('purchase_requisitions.edit_at') }}</th>
                    <th>{{ __('lang.remark') }}</th>
                </thead>
                <tbody>
                    @php $i = 1; @endphp
                    @foreach ($d->audits->reverse() as $index => $audit)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $audit->user ? $audit->user->name : '' }}</td>
                            <td>{{ get_thai_date_format($audit->created_at, 'd/m/Y H:i') }}</td>
                            <td>
                                @foreach ($audit->getModified() as $attribute => $modified)
                                    @if (strcmp($attribute, 'status') === 0 && $modified['new'] == \App\Enums\PRStatusEnum::CONFIRM)
                                        {{ __('purchase_requisitions.status_' . \App\Enums\PRStatusEnum::CONFIRM . '_text') }}
                                    @endif
                                    @if (strcmp($attribute, 'status') === 0 && $modified['new'] == \App\Enums\PRStatusEnum::REJECT)
                                        {{ __('purchase_requisitions.status_' . \App\Enums\PRStatusEnum::REJECT . '_text') }}
                                    @endif
                                    @if (strcmp($attribute, 'status') === 0 && $modified['new'] == \App\Enums\PRStatusEnum::CANCEL)
                                        {{ __('purchase_requisitions.status_' . \App\Enums\PRStatusEnum::CANCEL . '_text') }}
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
