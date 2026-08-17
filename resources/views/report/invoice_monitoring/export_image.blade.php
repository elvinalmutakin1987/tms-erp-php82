@php
    use App\Models\Maintenance;
    use App\Models\Invoice;
    use App\Models\Invoice_detail;
    use App\Models\Proforma_invoice;
    use App\Models\Invoice_proforma_invoice;
    use App\Models\Proforma_invoice_detail;
    use Illuminate\Support\Number;
    use Carbon\Carbon;

    /*
    |--------------------------------------------------------------------------
    | Periode
    |--------------------------------------------------------------------------
    */

    $periode = Carbon::create($year, $month, 1)->format('Y-m');

    /*
    |--------------------------------------------------------------------------
    | Ambil Proforma Invoice
    |--------------------------------------------------------------------------
    */

    $proformaInvoices = Proforma_invoice::where('periode', $periode)
        ->whereIn('status', ['Approved', 'CIC Approval', 'Invoicing', 'Done'])
        ->get();

    /*
    |--------------------------------------------------------------------------
    | Group berdasarkan Invoice
    |--------------------------------------------------------------------------
    */

    $invoiceGroups = [];

    foreach ($proformaInvoices as $pi) {
        /*
        |--------------------------------------------------------------------------
        | Cari relasi Proforma Invoice -> Invoice
        |--------------------------------------------------------------------------
        */

        $invoiceProforma = Invoice_proforma_invoice::where('proforma_invoice_id', $pi->id)->first();

        /*
        |--------------------------------------------------------------------------
        | Skip jika relasi tidak ada
        |--------------------------------------------------------------------------
        */

        if (!$invoiceProforma) {
            continue;
        }

        /*
        |--------------------------------------------------------------------------
        | Cari Invoice
        |--------------------------------------------------------------------------
        */

        $groupInvoice = Invoice::find($invoiceProforma->invoice_id);

        /*
        |--------------------------------------------------------------------------
        | Skip jika Invoice tidak ditemukan
        |--------------------------------------------------------------------------
        */

        if (!$groupInvoice) {
            continue;
        }

        /*
        |--------------------------------------------------------------------------
        | Buat Group Invoice
        |--------------------------------------------------------------------------
        */

        if (!isset($invoiceGroups[$groupInvoice->id])) {
            $invoiceGroups[$groupInvoice->id] = [
                'invoice' => $groupInvoice,
                'proformas' => [],
            ];
        }

        /*
        |--------------------------------------------------------------------------
        | Masukkan Proforma ke Invoice terkait
        |--------------------------------------------------------------------------
        */

        $invoiceGroups[$groupInvoice->id]['proformas'][] = $pi;
    }

    /*
    |--------------------------------------------------------------------------
    | Counter
    |--------------------------------------------------------------------------
    */

    $no = 1;
@endphp


<table style="
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
    ">

    {{-- ================================================================== --}}
    {{-- JUDUL --}}
    {{-- ================================================================== --}}

    <tr>

        <td colspan="19"
            style="
                font-size: 18px;
                text-align: center;
                vertical-align: middle;
            ">
            <b>PT. TUNAS MITRA SEJATI</b>
        </td>

    </tr>


    <tr>

        <td colspan="19"
            style="
                font-size: 18px;
                text-align: center;
                vertical-align: middle;
            ">
            <b>Invoice Monitoring</b>
        </td>

    </tr>


    <tr>

        <td colspan="19"
            style="
                font-size: 14px;
                text-align: center;
                vertical-align: middle;
            ">
            <b>
                {{ Carbon::create($year, $month, 1)->format('F Y') }}
            </b>
        </td>

    </tr>


    <tr>
        <td colspan="19"></td>
    </tr>



    {{-- ================================================================== --}}
    {{-- HEADER --}}
    {{-- ================================================================== --}}

    <tr>


        {{-- 1. NO --}}
        <td
            style="
                width: 15px;
                text-align: center;
                vertical-align: middle;
                border: 1px solid #000000;
                font-weight: bold;
                background-color: #f2f2f2;
            ">
            <b>No.</b>
        </td>


        {{-- 2. INVOICE --}}
        <td
            style="
                width: 100px;
                text-align: center;
                vertical-align: middle;
                border: 1px solid #000000;
                font-weight: bold;
                background-color: #f2f2f2;
            ">
            <b>Invoice</b>
        </td>


        {{-- 3. PROFORMA INVOICE --}}
        <td
            style="
                width: 100px;
                text-align: center;
                vertical-align: middle;
                border: 1px solid #000000;
                font-weight: bold;
                background-color: #f2f2f2;
            ">
            <b>Proforma Invoice</b>
        </td>


        {{-- 4. KETERANGAN --}}
        <td
            style="
                width: 250px;
                text-align: center;
                vertical-align: middle;
                border: 1px solid #000000;
                font-weight: bold;
                background-color: #f2f2f2;
            ">
            <b>Keterangan</b>
        </td>


        {{-- 5. CUT OFF DATE --}}
        <td
            style="
                width: 100px;
                text-align: center;
                vertical-align: middle;
                border: 1px solid #000000;
                font-weight: bold;
                background-color: #f2f2f2;
            ">
            <b>Cut Off Date</b>
        </td>


        {{-- 6. KONSOLIDASI DATA --}}
        <td
            style="
                width: 100px;
                text-align: center;
                vertical-align: middle;
                border: 1px solid #000000;
                font-weight: bold;
                background-color: #f2f2f2;
            ">
            <b>
                Konsolidasi Data
                <br>
                TMS &amp; CMD
            </b>
        </td>


        {{-- 7. KIRIM PROGRESS --}}
        <td
            style="
                width: 100px;
                text-align: center;
                vertical-align: middle;
                border: 1px solid #000000;
                font-weight: bold;
                background-color: #f2f2f2;
            ">
            <b>
                Kirim Progress
                <br>
                Klaim Approval
            </b>
        </td>


        {{-- 8. DATA DITERIMA DARI OPS --}}
        <td
            style="
                width: 100px;
                text-align: center;
                vertical-align: middle;
                border: 1px solid #000000;
                font-weight: bold;
                background-color: #f2f2f2;
            ">
            <b>
                Data Diterima
                <br>
                Dari Ops
            </b>
        </td>


        {{-- 9. PROFORMA APPROVED --}}
        <td
            style="
                width: 100px;
                text-align: center;
                vertical-align: middle;
                border: 1px solid #000000;
                font-weight: bold;
                background-color: #f2f2f2;
            ">
            <b>
                Proforma Invoice
                <br>
                Approved
            </b>
        </td>


        {{-- 10. MINTA CIC --}}
        <td
            style="
                width: 100px;
                text-align: center;
                vertical-align: middle;
                border: 1px solid #000000;
                font-weight: bold;
                background-color: #f2f2f2;
            ">
            <b>Minta CIC</b>
        </td>


        {{-- 11. PEMBUATAN CIC --}}
        <td
            style="
                width: 100px;
                text-align: center;
                vertical-align: middle;
                border: 1px solid #000000;
                font-weight: bold;
                background-color: #f2f2f2;
            ">
            <b>
                Pembuatan
                <br>
                CIC
            </b>
        </td>


        {{-- 12. TERIMA CIC --}}
        <td
            style="
                width: 100px;
                text-align: center;
                vertical-align: middle;
                border: 1px solid #000000;
                font-weight: bold;
                background-color: #f2f2f2;
            ">
            <b>Terima CIC</b>
        </td>


        {{-- 13. TANGGAL INVOICE --}}
        <td
            style="
                width: 100px;
                text-align: center;
                vertical-align: middle;
                border: 1px solid #000000;
                font-weight: bold;
                background-color: #f2f2f2;
            ">
            <b>Tanggal Inv</b>
        </td>


        {{-- 14. PEMBUATAN INVOICE --}}
        <td
            style="
                width: 100px;
                text-align: center;
                vertical-align: middle;
                border: 1px solid #000000;
                font-weight: bold;
                background-color: #f2f2f2;
            ">
            <b>
                Pembuatan
                <br>
                Inv
            </b>
        </td>


        {{-- 15. KIRIM CIC --}}
        <td
            style="
                width: 100px;
                text-align: center;
                vertical-align: middle;
                border: 1px solid #000000;
                font-weight: bold;
                background-color: #f2f2f2;
            ">
            <b>
                Kirim CIC
                <br>
                Ke KPC
            </b>
        </td>


        {{-- 16. INFORMASI CIC BISA DIAMBIL --}}
        <td
            style="
                width: 100px;
                text-align: center;
                vertical-align: middle;
                border: 1px solid #000000;
                font-weight: bold;
                background-color: #f2f2f2;
            ">
            <b>
                Informasi CIC
                <br>
                Bisa Diambil
            </b>
        </td>


        {{-- 17. CIC DIAMBIL TMS --}}
        <td
            style="
                width: 100px;
                text-align: center;
                vertical-align: middle;
                border: 1px solid #000000;
                font-weight: bold;
                background-color: #f2f2f2;
            ">
            <b>
                CIC Diambil
                <br>
                TMS
            </b>
        </td>


        {{-- 18. INVOICE TERIMA KPC --}}
        <td
            style="
                width: 100px;
                text-align: center;
                vertical-align: middle;
                border: 1px solid #000000;
                font-weight: bold;
                background-color: #f2f2f2;
            ">
            <b>
                Invoice Terima
                <br>
                KPC
            </b>
        </td>


        {{-- 19. STATUS BAYAR --}}
        <td
            style="
                width: 100px;
                text-align: center;
                vertical-align: middle;
                border: 1px solid #000000;
                font-weight: bold;
                background-color: #f2f2f2;
            ">
            <b>Status Bayar</b>
        </td>

    </tr>



    {{-- ================================================================== --}}
    {{-- DATA --}}
    {{-- ================================================================== --}}

    @forelse ($invoiceGroups as $group)

        @php

            /*
            |--------------------------------------------------------------------------
            | Invoice
            |--------------------------------------------------------------------------
            */

            $groupInvoice = $group['invoice'];

            /*
            |--------------------------------------------------------------------------
            | Semua Proforma dalam Invoice
            |--------------------------------------------------------------------------
            */

            $proformas = $group['proformas'];

            /*
            |--------------------------------------------------------------------------
            | Jumlah Rowspan
            |--------------------------------------------------------------------------
            */

            $rowspan = count($proformas);

            /*
            |--------------------------------------------------------------------------
            | Status pembayaran invoice
            |--------------------------------------------------------------------------
            */

            $paymentInvoice = Invoice::where('id', $groupInvoice->id)
                ->whereIn('status', ['Approved', 'Approval', 'Received', 'Done'])
                ->first();

        @endphp



        {{-- =============================================================== --}}
        {{-- LOOP PROFORMA --}}
        {{-- =============================================================== --}}

        @foreach ($proformas as $index => $pi)
            <tr>


                {{-- ======================================================= --}}
                {{-- NO --}}
                {{-- ======================================================= --}}

                <td
                    style="
                        text-align: center;
                        vertical-align: middle;
                        border: 1px solid #000000;
                    ">
                    {{ $no++ }}
                </td>



                {{-- ======================================================= --}}
                {{-- INVOICE --}}
                {{-- Tetap digabung berdasarkan invoice yang sama --}}
                {{-- ======================================================= --}}

                @if ($index === 0)
                    <td rowspan="{{ $rowspan }}"
                        style="
                            text-align: center;
                            vertical-align: middle;
                            border: 1px solid #000000;
                            font-weight: bold;
                        ">
                        {{ $groupInvoice->invoice_no }}
                    </td>
                @endif



                {{-- ======================================================= --}}
                {{-- PROFORMA INVOICE --}}
                {{-- ======================================================= --}}

                <td
                    style="
                        text-align: center;
                        vertical-align: middle;
                        border: 1px solid #000000;
                    ">
                    {{ $pi->proforma_no }}
                </td>



                {{-- ======================================================= --}}
                {{-- KETERANGAN --}}
                {{-- ======================================================= --}}

                <td
                    style="
                        text-align: left;
                        vertical-align: middle;
                        border: 1px solid #000000;
                    ">

                    @if ($pi->contract)
                        {{ strip_tags($pi->contract->notes ?? '') }}

                        @if (
                            $pi->contract->service &&
                                ($pi->contract->service->type == 'Unit Rental' || $pi->contract->service->type == 'Fuel Truck Rental'))
                            @if ($pi->unit)
                                - {{ $pi->unit->vehicle_no }}
                            @endif
                        @endif
                    @else
                        -
                    @endif

                </td>



                {{-- ======================================================= --}}
                {{-- CUT OFF DATE --}}
                {{-- ======================================================= --}}

                <td
                    style="
                        text-align: center;
                        vertical-align: middle;
                        border: 1px solid #000000;
                    ">
                    {{ $pi->cut_off_date ? Carbon::parse($pi->cut_off_date)->format('d M Y') : '-' }}
                </td>



                {{-- ======================================================= --}}
                {{-- KONSOLIDASI DATA --}}
                {{-- ======================================================= --}}

                <td
                    style="
                        text-align: center;
                        vertical-align: middle;
                        border: 1px solid #000000;
                    ">
                    {{ $pi->consolidation_date ? Carbon::parse($pi->consolidation_date)->format('d M Y') : '-' }}
                </td>



                {{-- ======================================================= --}}
                {{-- KIRIM PROGRESS --}}
                {{-- ======================================================= --}}

                <td
                    style="
                        text-align: center;
                        vertical-align: middle;
                        border: 1px solid #000000;
                    ">
                    {{ $pi->progress_claim_date ? Carbon::parse($pi->progress_claim_date)->format('d M Y') : '-' }}
                </td>



                {{-- ======================================================= --}}
                {{-- DATA DITERIMA DARI OPS --}}
                {{-- ======================================================= --}}

                <td
                    style="
                        text-align: center;
                        vertical-align: middle;
                        border: 1px solid #000000;
                    ">
                    {{ $pi->ops_received_date ? Carbon::parse($pi->ops_received_date)->format('d M Y') : '-' }}
                </td>



                {{-- ======================================================= --}}
                {{-- PROFORMA APPROVED --}}
                {{-- ======================================================= --}}

                <td
                    style="
                        text-align: center;
                        vertical-align: middle;
                        border: 1px solid #000000;
                    ">
                    {{ $pi->prof_inv_app_date ? Carbon::parse($pi->prof_inv_app_date)->format('d M Y') : '-' }}
                </td>



                {{-- ======================================================= --}}
                {{-- MINTA CIC --}}
                {{-- ======================================================= --}}

                <td
                    style="
                        text-align: center;
                        vertical-align: middle;
                        border: 1px solid #000000;
                    ">
                    {{ $pi->cic_request_date ? Carbon::parse($pi->cic_request_date)->format('d M Y') : '-' }}
                </td>



                {{-- ======================================================= --}}
                {{-- PEMBUATAN CIC --}}
                {{-- ======================================================= --}}

                <td
                    style="
                        text-align: center;
                        vertical-align: middle;
                        border: 1px solid #000000;
                    ">
                    {{ $pi->cic_created_date ? Carbon::parse($pi->cic_created_date)->format('d M Y') : '-' }}
                </td>



                {{-- ======================================================= --}}
                {{-- TERIMA CIC --}}
                {{-- ======================================================= --}}

                <td
                    style="
                        text-align: center;
                        vertical-align: middle;
                        border: 1px solid #000000;
                    ">
                    {{ $pi->cic_received_date ? Carbon::parse($pi->cic_received_date)->format('d M Y') : '-' }}
                </td>



                {{-- ======================================================= --}}
                {{-- TANGGAL INVOICE --}}
                {{-- ======================================================= --}}

                <td
                    style="
                        text-align: center;
                        vertical-align: middle;
                        border: 1px solid #000000;
                    ">
                    {{ $pi->inv_date ? Carbon::parse($pi->inv_date)->format('d M Y') : '-' }}
                </td>



                {{-- ======================================================= --}}
                {{-- PEMBUATAN INVOICE --}}
                {{-- ======================================================= --}}

                <td
                    style="
                        text-align: center;
                        vertical-align: middle;
                        border: 1px solid #000000;
                    ">
                    {{ $pi->inv_create_date ? Carbon::parse($pi->inv_create_date)->format('d M Y') : '-' }}
                </td>



                {{-- ======================================================= --}}
                {{-- KIRIM CIC KE KPC --}}
                {{-- ======================================================= --}}

                <td
                    style="
                        text-align: center;
                        vertical-align: middle;
                        border: 1px solid #000000;
                    ">
                    {{ $pi->cic_send_date ? Carbon::parse($pi->cic_send_date)->format('d M Y') : '-' }}
                </td>



                {{-- ======================================================= --}}
                {{-- INFORMASI CIC BISA DIAMBIL --}}
                {{-- ======================================================= --}}

                <td
                    style="
                        text-align: center;
                        vertical-align: middle;
                        border: 1px solid #000000;
                    ">
                    {{ $pi->cic_ready_to_pick_date ? Carbon::parse($pi->cic_ready_to_pick_date)->format('d M Y') : '-' }}
                </td>



                {{-- ======================================================= --}}
                {{-- CIC DIAMBIL TMS --}}
                {{-- ======================================================= --}}

                <td
                    style="
                        text-align: center;
                        vertical-align: middle;
                        border: 1px solid #000000;
                    ">
                    {{ $pi->cic_pick_up_date ? Carbon::parse($pi->cic_pick_up_date)->format('d M Y') : '-' }}
                </td>



                {{-- ======================================================= --}}
                {{-- INVOICE TERIMA KPC --}}
                {{-- ======================================================= --}}

                <td
                    style="
                        text-align: center;
                        vertical-align: middle;
                        border: 1px solid #000000;
                    ">
                    {{ $pi->inv_send_date ? Carbon::parse($pi->inv_send_date)->format('d M Y') : '-' }}
                </td>



                {{-- ======================================================= --}}
                {{-- STATUS BAYAR --}}
                {{-- ======================================================= --}}

                @if ($index === 0)
                    {{-- =================================================== --}}
                    {{-- INVOICING --}}
                    {{-- =================================================== --}}

                    @if (!$paymentInvoice)
                        <td rowspan="{{ $rowspan }}"
                            style="
                                text-align: center;
                                vertical-align: middle;
                                border: 1px solid #000000;
                                background-color: #0d6efd;
                                color: #ffffff;
                                font-weight: bold;
                            ">
                            Invoicing
                        </td>



                        {{-- =================================================== --}}
                        {{-- PAID --}}
                        {{-- =================================================== --}}
                    @elseif ($paymentInvoice->payment_status == 'Paid')
                        <td rowspan="{{ $rowspan }}"
                            style="
                                text-align: center;
                                vertical-align: middle;
                                border: 1px solid #000000;
                                background-color: #198754;
                                color: #ffffff;
                                font-weight: bold;
                            ">
                            Paid
                        </td>



                        {{-- =================================================== --}}
                        {{-- PARTIALY PAID --}}
                        {{-- =================================================== --}}
                    @elseif ($paymentInvoice->payment_status == 'Partialy Paid')
                        <td rowspan="{{ $rowspan }}"
                            style="
                                text-align: center;
                                vertical-align: middle;
                                border: 1px solid #000000;
                                background-color: #ffc107;
                                color: #000000;
                                font-weight: bold;
                            ">
                            Partialy Paid
                        </td>



                        {{-- =================================================== --}}
                        {{-- UNPAID --}}
                        {{-- =================================================== --}}
                    @elseif ($paymentInvoice->payment_status == 'Unpaid')
                        <td rowspan="{{ $rowspan }}"
                            style="
                                text-align: center;
                                vertical-align: middle;
                                border: 1px solid #000000;
                                background-color: #dc3545;
                                color: #ffffff;
                                font-weight: bold;
                            ">
                            Unpaid
                        </td>



                        {{-- =================================================== --}}
                        {{-- STATUS LAIN --}}
                        {{-- =================================================== --}}
                    @else
                        <td rowspan="{{ $rowspan }}"
                            style="
                                text-align: center;
                                vertical-align: middle;
                                border: 1px solid #000000;
                                background-color: #0d6efd;
                                color: #ffffff;
                                font-weight: bold;
                            ">
                            {{ $paymentInvoice->payment_status ?? 'Invoicing' }}
                        </td>
                    @endif
                @endif


            </tr>
        @endforeach



    @empty

        {{-- ================================================================== --}}
        {{-- DATA KOSONG --}}
        {{-- ================================================================== --}}

        <tr>

            <td colspan="19"
                style="
                    text-align: center;
                    vertical-align: middle;
                    border: 1px solid #000000;
                ">
                Tidak ada data invoice pada periode ini.
            </td>

        </tr>

    @endforelse


</table>
