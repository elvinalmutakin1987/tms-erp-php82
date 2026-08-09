@php
    use Illuminate\Support\Number;
    use Carbon\Carbon;
@endphp
<div class="row mb-2">
    <div class="col">
        <table style="width: 100%;border-collapse:separate; border-spacing:0 12px;">
            <tr>
                <td width="30%" style="vertical-align: top">Number :<br>
                    <b>{{ $invoice_payment->payment_no }}</b>
                </td>
                <td width="30%" style="vertical-align: top">Invoice No. :<br>
                    <b>
                        {{ $invoice_payment->invoice->invoice_no }}
                    </b>
                </td>
                <td width="30%" style="vertical-align: top">Status :<br>
                    @if ($invoice_payment->status == 'Draft')
                        <span class="badge bg-secondary" style="font-size: 13px">{{ $invoice_payment->status }}</span>
                    @elseif($invoice_payment->status == 'Approval')
                        <span class="badge bg-info" style="font-size: 13px">{{ $invoice_payment->status }}</span>
                    @elseif($invoice_payment->status == 'Open')
                        <span class="badge bg-primary" style="font-size: 13px">{{ $invoice_payment->status }}</span>
                    @elseif($invoice_payment->status == 'Approved' || $invoice_payment->status == 'Received')
                        <span class="badge bg-warning" style="font-size: 13px">{{ $invoice_payment->status }}</span>
                    @elseif($invoice_payment->status == 'Done')
                        <span class="badge bg-success" style="font-size: 13px">{{ $invoice_payment->status }}</span>
                    @endif
                </td>
            </tr>
            <tr>
                <td width="30%" style="vertical-align: top">Date :<br>
                    <b>{{ $invoice_payment?->date }}</b>
                </td>
                <td width="30%" style="vertical-align: top">Vendor :<br>
                    <b>{{ $invoice_payment->invoice->client_vendor?->name ?? '' }}</b>
                </td>
                <td width="30%" style="vertical-align: top">Transfer From :<br>
                    <b>{{ $invoice_payment->bank_sender }} -
                        {{ $invoice_payment->bank_account_sender }}</b>
                </td>
            </tr>
            <tr>
                <td width="30%" style="vertical-align: top">Bank :<br>
                    <b>{{ $invoice_payment->bank }}</b>
                </td>
                <td width="30%" style="vertical-align: top">Account :<br>
                    <b>{{ $invoice_payment->bank_account ?? '' }}</b>
                </td>
                <td width="30%" style="vertical-align: top">Total :<br>
                    <b>{{ $invoice_payment?->total ? Number::format($invoice_payment?->total, precision: 0) : '' }}</b>
                </td>
            </tr>
            <tr>
                <td width="30%" style="vertical-align: top">Ref No. :<br>
                    <b>{{ $invoice_payment->ref_no }}</b>
                </td>
                <td width="30%" style="vertical-align: top"><br>
                </td>
                <td width="30%" style="vertical-align: top"><br>
                </td>
            </tr>
        </table>
    </div>
</div>
<div class="row mb-2">
    <div class="col">
        Notes : <br>
        {!! nl2br(e($invoice_payment->notes)) !!}
    </div>
</div>
<div class="row mb-2">
    <div class="col">
        @php
            $html = '<table style="width: 100%">';
            if ($invoice_payment->payment_path) {
                $html .= '<tr>';
                $html .= '<td>';
                $html .=
                    '<a href="' .
                    route('invoicepayment.export_file', $invoice_payment->id) .
                    '" target="_blank">' .
                    $invoice_payment->real_name .
                    '</a>';
                $html .= '</td>';
                $html .= '</tr>';
            } else {
                $html .= '<tr><td class="text-center">No quotation file</td></tr>';
            }
            $html .= '</table>';
            echo $html;
        @endphp
    </div>
</div>
