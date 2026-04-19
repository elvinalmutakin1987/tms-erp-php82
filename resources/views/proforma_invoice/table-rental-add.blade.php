<table class="table" id="tableRental">
    <thead class="table-dark">
        <tr>
            <th scope="col" style="width:4%">#</th>
            <th scope="col" style="width:20%">Description</th>
            <th scope="col" style="width:45%">Detail</th>
            <th scope="col" style="width:25%">Amount</th>
        </tr>
    </thead>
    <tbody id="tbody">
        <tr style="vertical-align: top">
            <td class="p-1 align-middle">1</td>
            <td class="p-1 align-middle"><b>Target PA</b></td>
            <td class="p-1 align-middle">(%)</td>
            <td class="p-1 align-middle">
                <input type="hidden" class="form-control" id="target" name="target">
                <input type="text" class="form-control" id="_target" name="_target" readonly>
            </td>
        </tr>
        <tr>
            <td class="p-1 align-middle">2</td>
            <td class="p-1 align-middle"><b>Aktual Hari Kerja</b>
            </td>
            <td class="p-1 align-middle">
                (*30* hari @ Rp. *68.400.000*)
            </td>
            <td class="p-1 align-middle">
                <input type="hidden" class="form-control" id="act_work_day" name="act_work_day">
                <input type="text" class="form-control" id="_act_work_day" name="_act_work_day" readonly>
            </td>
        </tr>
        <tr>
            <td class="p-1 align-middle">3</td>
            <td class="p-1 align-middle"><b>Aktual PA</b>
            </td>
            <td class="p-1 align-middle">
                Jam Tersedia *30* Hari x 24 Jam = *672* Jam<br>
                Breakdown *0,5* <br>
                PA = *99,9* %
            </td>
            <td class="p-1 align-middle">
                <input type="hidden" class="form-control" id="act_work_hour" name="act_work_hour">
                <input type="hidden" class="form-control" id="breakdown" name="breakdown">
                <input type="hidden" class="form-control" id="pa" name="pa">
                <input type="text" class="form-control" id="_pa" name="_pa" readonly>
            </td>
        </tr>
        <tr style="vertical-align: top">
            <td class="p-1 align-middle">4</td>
            <td class="p-1 align-middle"><b>Penalty</td>
            <td class="p-1 align-middle"></td>
            <td class="p-1 align-middle">
                <input type="hidden" class="form-control" id="penalty" name="penalty">
                <input type="text" class="form-control" id="_penalty" name="_penalty" readonly>
            </td>
        </tr>
        <tr style="vertical-align: top">
            <td class="p-1 align-middle">5</td>
            <td class="p-1 align-middle"><b>KM Awal</td>
            <td class="p-1 align-middle"></td>
            <td class="p-1 align-middle">
                <input type="hidden" class="form-control" id="km_awal" name="km_awal">
                <input type="text" class="form-control" id="_km_awal" name="_km_awal" readonly>
            </td>
        </tr>
        <tr style="vertical-align: top">
            <td class="p-1 align-middle">6</td>
            <td class="p-1 align-middle"><b>KM Akhir</td>
            <td class="p-1 align-middle"></td>
            <td class="p-1 align-middle">
                <input type="hidden" class="form-control" id="km_akhir" name="km_akhir">
                <input type="text" class="form-control" id="_km_akhir" name="_km_akhir" readonly>
            </td>
        </tr>
    </tbody>
    <tfoot>
        <tr>
            <td class="p-1 align-middle">7</td>
            <td class="p-1 align-middle"><b>Total Payment</td>
            <td class="p-1 align-middle"></td>
            <td class="p-1 align-middle">
                <input type="text" class="form-control text-end" id="total" name="total">
                <input type="hidden" class="form-control" name="_total" id="_total" readonly>
            </td>
        </tr>
    </tfoot>
</table>
