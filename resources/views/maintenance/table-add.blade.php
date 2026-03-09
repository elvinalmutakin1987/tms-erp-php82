<tr>
    <td class="p-1 align-middle" style="width: 3%">
    </td>
    <td class="p-1 align-middle" style="width: 15%">
        <select class="form-select select-select" id="act" name="act">
            <option value="Repair" selected>Repair</option>
            <option value="Replace">Replace</option>
            <option value="Washing">Washing</option>
            <option value="Add">Add</option>
            <option value="Flushing">Flushing</option>
            <option value="Commisioning">Commisioning</option>
            <option value="Welding">Welding</option>
            <option value="Adjust">Adjust</option>
        </select>
    </td>
    <td class="p-1 align-middle">
        <select class="form-select select-select" id="main_item_id" name="main_item_id">
            @foreach ($maintenance_item as $d)
                <option value="{{ $d->id }}">{{ $d->name }}</option>
            @endforeach
        </select>
    </td>
    <td class="p-1 align-middle" style="width:20%">
        <input type="hidden" class="form-control timepicker" id="cost_i" name="cost_i">
        <input type="text" class="form-control timepicker" id="_cost_i" name="_cost_i">
    </td>
    <td class="p-1 align-middle" style="width:7%">
        <div class="row row-cols-auto g-3">
            <div class="col">
                <button type="button" class="btn btn-lg btn-primary bx bx-plus mr-1" id="addItemButton"></button>
            </div>
        </div>
    </td>
</tr>

<script>
    gen_select2();
</script>
