<div class="table-wrap">
    <table class="table table-striped">
        <thead class="bg-body-dark">
            <th style="width: 1px;">#</th>
            <th>{{ __('short_term_rentals.quotation') }}</th>
            <th>{{ __('quotations.bill_payment') }}</th>
            <th colspan="2">ลิงก์ 2c2p</th>
        </thead>
        <tbody>
            <tr>
                <td>1</td>
                <td>
                    <a class="btn btn-primary text-start"
                        href="{{ route('admin.quotations.pdf', ['id' => $quotation->id]) }}"
                        target="_blank" style="width: 100%;">
                        <i class="si si-link"></i>
                        {{ $quotation->qt_no }}
                    </a>
                </td>
                <td>
                    <a class="btn btn-primary text-start"
                        href="{{ route('admin.quotations.pdf', ['id' => $quotation->id, 'type' => 'payment']) }}"
                        target="_blank" style="width: 100%;">
                        <i class="si si-link"></i> ใบนำฝากชำระเงิน
                    </a>
                </td>
                <td>
                    <input type="hidden" id="link" value="{{ $quotation->payment_url }}">
                    <a class="btn btn-primary copy_link text-start" href="" target="_blank"
                        style="width: 100%;" id="2c2p-payment-link">
                        <i class="far fa-clone" aria-hidden="true"></i> คัดลอกลิงก์
                    </a>
                </td>
                <td style="width: 50px;">
                    @if($show_regenerate_quickpay_link)
                    <a class="btn btn-primary btn-mini btn-regenerate-link" onclick="gen2c2pPaymentLink('{{ $d->id }}')"
                        href="javascript:;"><i class="fa fa-arrow-rotate-left"></i>
                    </a>
                    @endif
                </td>
            </tr>
        </tbody>
    </table>
    <span class="fs-sm" id="toasts">
    </span>
</div>