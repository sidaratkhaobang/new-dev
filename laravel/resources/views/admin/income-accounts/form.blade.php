@extends('admin.layouts.layout')

@section('page_title', $page_title)

@section('content')
    <div class="block {{ __('block.styles') }}">
        <div class="block-content">
            <form id="save-form">
                <div class="table-wrap db-scroll mb-4">
                    <table class="table table-striped table-vcenter">
                        <thead class="bg-body-dark">
                            <tr>
                                <th>FLAG</th>
                                <th>Posting Date</th>
                                <th>Document Date</th>
                                <th>Document Type</th>
                                <th>Company Code</th>
                                <th>Branch Number</th>
                                <th>Currency</th>
                                <th>Currency Rate</th>
                                <th>Translation Date</th>
                                <th>Reference Document</th>
                                <th>Header Text</th>
                                <th>Posting Key</th>
                                <th>Account No.</th>
                                <th>Amount in document</th>
                                <th>Amount in local currency</th>
                                <th>Cost Center</th>
                                <th>Fund Code</th>
                                <th>Base Amount</th>
                                <th>Tax Code</th>
                                <th>Assignment</th>
                                <th>Line item text</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($sap_interface_lines as $index => $d)
                                <tr>
                                    <td>{{ $d->flag ? '*' : null }}</td>
                                    <td>{{ date('dmY', strtotime($d->posting_date)) }}</td>
                                    <td>{{ date('dmY', strtotime($d->document_date)) }}</td>
                                    <td>{{ $d->document_type }}</td>
                                    <td>{{ $d->company_code }}</td>
                                    <td>{{ $d->branch_number }}</td>
                                    <td>{{ $d->currency }}</td>
                                    <td></td>
                                    <td></td>
                                    <td>{{ $d->reference_document }}</td>
                                    <td>{{ $d->header_text }}</td>
                                    <td>{{ $d->posting_key }}</td>
                                    <td>{{ $d->account_no }}</td>
                                    <td class="text-end">{{ number_format($d->amount_in_document, 2) }}</td>
                                    <td class="text-end">{{ number_format($d->amount_in_document, 2) }}</td>
                                    <td>{{ $d->cost_center }}</td>
                                    <td></td>
                                    <td class="text-end">{{ !empty($d->base_amount) ? number_format($d->base_amount,2) : null }}</td>
                                    <td>{{ $d->tax_code }}</td>
                                    <td>{{ $d->assignment }}</td>
                                    <td>{{ $d->text }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <x-forms.hidden id="id" :value="$d->id" />
                <x-forms.submit-group :optionals="['url' => 'admin.income-accounts.index', 'view' => empty($view) ? null : $view]" />
            </form>
        </div>
    </div>
@endsection
