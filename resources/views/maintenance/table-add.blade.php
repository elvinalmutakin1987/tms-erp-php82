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

    $("#act").select2({
        theme: "bootstrap-5",
        width: $(this).data('width') ? $(this).data('width') : $(this).hasClass(
            'w-100') ? '100%' : 'style',
    }).on('change', function() {
        $('#main_item_id').val('').trigger('change');
        let action = $("#act").val();
        $('#main_item_id').select2({
            theme: "bootstrap-5",
            width: $(this).data('width') ? $(this).data('width') : $(this).hasClass(
                'w-100') ? '100%' : 'style',
            allowClear: true,
            ajax: {
                url: '{{ route('maintenance.get_maintenance_item_by_action') }}',
                dataType: 'json',
                data: function(params) {
                    return {
                        term: params.term || '',
                        page: params.page || 1,
                        action: action
                    };
                },
                cache: true,
            }
        });
    });

    const $cost_i = $('#_cost_i');
    $cost_i.on('keydown', function(e) {
        textKeyDown(e);
    });

    $cost_i.on('input', function(e) {
        textInput("cost_i", e);
    });
</script>
