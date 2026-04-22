<table class="table" id="tablePallet">
    <thead class="table-dark">
        <tr>
            <th scope="col" style="width:4">#</th>
            <th scope="col" width="15%">Item No.</th>
            <th scope="col">Description</th>
            <th scope="col" width="15%">Unit</th>
            <th scope="col" width="10%">Qty</th>
            <th scope="col" width="20%">Amount</th>
            <th scope="col" width="5">Action</th>
        </tr>
    </thead>
    <tbody id="tbody">
        <tr class="fixed-row">
            <td class="p-1 align-middle">

            </td>
            <td class="p-1 align-middle">
                <select class="form-select select-select" id="_item_no" name="_item_no">

                </select>
            </td>
            <td class="p-1 align-middle">
                <input type="text" class="form-control" id="_description" name="_description">
            </td>
            <td class="p-1 align-middle">
                <select class="form-select select-select" id="_unit" name="_unit">
                    @foreach ($unit as $d)
                        <option value="{{ $d->id }}">{{ $d->vehicle_no }}</option>
                    @endforeach
                </select>
            </td>
            <td class="p-1 align-middle">
                <input type="hidden" class="form-control" id="_qty" name="_qty">
                <input type="text" class="form-control" id="_qty_" name="_qty_">
            </td>
            <td class="p-1 align-middle">
                <input type="hidden" class="form-control" id="_qty" name="_qty">
                <input type="text" class="form-control" id="_qty_" name="_qty_">
            </td>
            <td class="p-1 align-middle">
                <div class="row row-cols-auto g-3">
                    <div class="col">
                        <button type="button" class="btn btn-lg btn-primary bx bx-plus mr-1"
                            id="addPalletButton"></button>
                    </div>
                </div>
            </td>
        </tr>
    </tbody>
    <tfoot>
        <tr>
            <td class="p-1 align-middle text-end" colspan="5"><b>Total Payment</td>
            <td class="p-1 align-middle">
                <input type="text" class="form-control text-end" id="total" name="total">
                <input type="hidden" class="form-control" name="_total" id="_total" readonly>
            </td>
            <td class="p-1 align-middle"></td>
        </tr>
    </tfoot>
</table>

<script>
    $(document).ready(function() {
        function initSelect2() {
            $('.select-select').each(function() {
                const $el = $(this);
                $el.select2({
                    theme: "bootstrap-5",
                    dropdownParent: $('#formModal'),
                    width: '100%',
                    selectOnClose: false,
                    minimumResultsForSearch: 0,
                });
            });
        }
        initSelect2();

        $('#formModal').on('shown.bs.modal', function() {
            initSelect2();
        });
    });
</script>
