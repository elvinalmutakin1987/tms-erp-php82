@foreach ($maintenance_detail as $d)
    <tr>
        <td class="p-1 align-middle row-number">
            #
        </td>
        <td class="p-1 align-middle">
            <input type="text" class="form-control" id="action" name="action[]" readonly value="{{ $d->action }}">
        </td>
        <td class="p-1 align-middle">
            <input type="hidden" class="form-control" id="maintenance_item_id" name="maintenance_item_id[]" readonly
                value="{{ $d->maintenance_item_id }}">
            <input type="text" class="form-control" id="main_item" name="main_item[]" readonly
                value="{{ $d->maintenance_item->name }}">
        </td>
        <td class="text-center p-1 align-middle">
            <div class="row row-cols-auto g-3">
                <div class="col">
                    <button type="button" class="btn btn-lg btn-danger bx bx-trash mr-1 delete-row  "
                        id="removeItemButton"></button>
                </div>
            </div>
        </td>
    </tr>
@endforeach


<script>
    gen_select2();
</script>
