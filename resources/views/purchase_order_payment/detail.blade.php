@php
    use Illuminate\Support\Number;
    use Carbon\Carbon;
@endphp
<div class="row mb-2">
    <div class="col">
        <table style="width: 100%;border-collapse:separate; border-spacing:0 12px;">
            <tr>
                <td width="30%">Number :<br>
                    <b>{{ $purchase_order_payment->payment_no }}</b>
                </td>
                <td width="30%">Purchase Order No. :<br>
                    <b>
                        {{ $purchase_order_payment->purchase_order->order_no }}
                    </b>
                </td>
                <td>Status :<br>
                    @if ($purchase_order_payment->status == 'Draft')
                        <span class="badge bg-secondary"
                            style="font-size: 13px">{{ $purchase_order_payment->status }}</span>
                    @elseif($purchase_order_payment->status == 'Approval')
                        <span class="badge bg-info" style="font-size: 13px">{{ $purchase_order_payment->status }}</span>
                    @elseif($purchase_order_payment->status == 'Open')
                        <span class="badge bg-primary"
                            style="font-size: 13px">{{ $purchase_order_payment->status }}</span>
                    @elseif($purchase_order_payment->status == 'Approved' || $purchase_order_payment->status == 'Received')
                        <span class="badge bg-warning"
                            style="font-size: 13px">{{ $purchase_order_payment->status }}</span>
                    @elseif($purchase_order_payment->status == 'Done')
                        <span class="badge bg-success"
                            style="font-size: 13px">{{ $purchase_order_payment->status }}</span>
                    @endif
                </td>
            </tr>
            <tr>
                <td width="30%">Date :<br>
                    <b>{{ $purchase_order_payment?->date }}</b>
                </td>
                <td width="30%">Vendor :<br>
                    <b>{{ $purchase_order_payment->purchase_order->client_vendor?->name ?? '' }}</b>
                </td>
                <td width="30%">Transfer From :<br>
                    <b>{{ $purchase_order_payment->bank_sender }} -
                        {{ $purchase_order_payment->bank_account_sender }}</b>
                </td>
            </tr>
            <tr>
                <td width="30%">Bank :<br>
                    <b>{{ $purchase_order_payment->bank }}</b>
                </td>
                <td width="30%">Account :<br>
                    <b>{{ $purchase_order_payment->bank_account ?? '' }}</b>
                </td>
                <td width="30%">Total :<br>
                    <b>{{ $purchase_order_payment?->total ? Number::format($purchase_order_payment?->total, precision: 0) : '' }}</b>
                </td>
            </tr>
            <tr>
                <td width="30%">Ref No. :<br>
                    <b>{{ $purchase_order_payment->ref_no }}</b>
                </td>
                <td width="30%"><br>
                </td>
                <td width="30%"><br>
                </td>
            </tr>
        </table>
    </div>
</div>
<div class="row mb-2">
    <div class="col">
        Notes : <br>
        {!! nl2br(e($purchase_order_payment->notes)) !!}
    </div>
</div>
<div class="row mb-2">
    <div class="col">
        @php
            $html = '<table style="width: 100%">';
            if ($purchase_order_payment->payment_path) {
                $html .= '<tr>';
                $html .= '<td>';
                $html .=
                    '<a href="' .
                    route('purchaseorderpayment.export_file', $purchase_order_payment->id) .
                    '" target="_blank">' .
                    $purchase_order_payment->real_name .
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
