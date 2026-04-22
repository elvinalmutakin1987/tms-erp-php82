@php
    function getBgColor($date)
    {
        if (!$date) {
            return '#ffffff';
        }

        $targetDate = \Carbon\Carbon::parse($date);
        $now = \Carbon\Carbon::now();
        $diffInDays = $now->diffInDays($targetDate, false);

        if ($diffInDays <= 30) {
            return '#ff0000'; // Merah (<= 1 bulan)
        } elseif ($diffInDays <= 45) {
            return '#ffff00'; // Kuning (<= 1.5 bulan)
        } else {
            return '#00ff00'; // Hijau (<= 2 bulan)
        }

        return '#ffffff';
    }
@endphp

<table>
    <tr>
        <td style="font-size: 18px; text-align:center" colspan="14"><b>PT. TUNAS MITRA SEJATI</b></td>
    </tr>
    <tr>
        <td style="font-size: 18px; text-align:center" colspan="14"><b>COMMISSIONING UNIT</b></td>
    </tr>
    <tr>
        <td style="font-size: 18px; text-align:center" colspan="14"></td>
    </tr>

    <!-- Header dengan Width asli Anda -->
    <tr>
        <td style="width: 30px; text-align:center; border: 1px solid #000000;"><b>No.</b></td>
        <td style="width: 150px; text-align:center; border: 1px solid #000000;"><b>Type</b></td>
        <td style="width: 150px; text-align:center; border: 1px solid #000000;"><b>Nomor Lambung</b></td>
        <td style="width: 150px; text-align:center; border: 1px solid #000000;"><b>Nomor Chassis</b></td>
        <td style="width: 150px; text-align:center; border: 1px solid #000000;"><b>Nomor Polisi</b></td>
        <td style="width: 150px; text-align:center; border: 1px solid #000000;"><b>Kode Access</b></td>
        <td style="width: 150px; text-align:center; border: 1px solid #000000;"><b>Nomor PLR</b></td>
        <td style="width: 150px; text-align:center; border: 1px solid #000000;"><b>Nomor Banlaw</b></td>
        <td style="width: 150px; text-align:center; border: 1px solid #000000;"><b>Expired Crane</b></td>
        <td style="width: 150px; text-align:center; border: 1px solid #000000;"><b>Expired Fuel Issue</b></td>
        <td style="width: 150px; text-align:center; border: 1px solid #000000;"><b>Expired TBST</b></td>
        <td style="width: 150px; text-align:center; border: 1px solid #000000;"><b>Expired STNK</b></td>
        <td style="width: 150px; text-align:center; border: 1px solid #000000;"><b>Expired Tax</b></td>
        <td style="width: 150px; text-align:center; border: 1px solid #000000;"><b>Expired Commissioning</b></td>
    </tr>

    @foreach ($unit as $d)
        <tr>
            <td style="width: 30px; text-align:center; border: 1px solid #000000;">{{ $loop->iteration }}</td>
            <td style="width: 150px; text-align:center; border: 1px solid #000000;">{{ $d->type }}</td>
            <td style="width: 150px; text-align:center; border: 1px solid #000000;">{{ $d->vehicle_no }}</td>
            <td style="width: 150px; text-align:center; border: 1px solid #000000;">{{ $d->chassis_no }}</td>
            <td style="width: 150px; text-align:center; border: 1px solid #000000;">{{ $d->registeration_no }}</td>
            <td style="width: 150px; text-align:center; border: 1px solid #000000;">{{ $d->code_access }}</td>
            <td style="width: 150px; text-align:center; border: 1px solid #000000;">{{ $d->plr_no }}</td>
            <td style="width: 150px; text-align:center; border: 1px solid #000000;">{{ $d->banlaw_no }}</td>

            {{-- Bagian Date dengan Logika Warna --}}
            <td
                style="width: 150px; text-align:center; border: 1px solid #000000; background-color: {{ getBgColor($d->exp_crane) }};">
                {{ $d->exp_crane ? \Carbon\Carbon::parse($d->exp_crane)->translatedFormat('d F Y') : '' }}</td>
            <td
                style="width: 150px; text-align:center; border: 1px solid #000000; background-color: {{ getBgColor($d->exp_fuel_issue) }};">
                {{ $d->exp_fuel_issue ? \Carbon\Carbon::parse($d->exp_fuel_issue)->translatedFormat('d F Y') : '' }}
            </td>
            <td
                style="width: 150px; text-align:center; border: 1px solid #000000; background-color: {{ getBgColor($d->exp_tbst) }};">
                {{ $d->exp_tbst ? \Carbon\Carbon::parse($d->exp_tbst)->translatedFormat('d F Y') : '' }}</td>
            <td
                style="width: 150px; text-align:center; border: 1px solid #000000; background-color: {{ getBgColor($d->exp_stnk) }};">
                {{ $d->exp_stnk ? \Carbon\Carbon::parse($d->exp_stnk)->translatedFormat('d F Y') : '' }}</td>
            <td
                style="width: 150px; text-align:center; border: 1px solid #000000; background-color: {{ getBgColor($d->exp_tax) }};">
                {{ $d->exp_tax ? \Carbon\Carbon::parse($d->exp_tax)->translatedFormat('d F Y') : '' }}</td>
            <td
                style="width: 150px; text-align:center; border: 1px solid #000000; background-color: {{ getBgColor($d->exp_comm) }};">
                {{ $d->exp_comm ? \Carbon\Carbon::parse($d->exp_comm)->translatedFormat('d F Y') : '' }}</td>
        </tr>
    @endforeach

    {{-- Keterangan Legenda di bawah --}}
    <tr>
        <td colspan="14"></td>
    </tr>
    <tr>
        <td style="border: 1px solid #000000; background-color: #ff0000;"></td>
        <td colspan="13">Masa berlaku kurang dari atau sama dengan 1 bulan</td>
    </tr>
    <tr>
        <td style="border: 1px solid #000000; background-color: #ffff00;"></td>
        <td colspan="13">Masa berlaku antara 1 hingga 1.5 bulan</td>
    </tr>
    <tr>
        <td style="border: 1px solid #000000; background-color: #00ff00;"></td>
        <td colspan="13">Masa berlaku antara diatas 1.5 bulan</td>
    </tr>
</table>
