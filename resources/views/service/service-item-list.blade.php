@foreach ($service_item as $d)
    <tr>
        <td>
            #
        </td>
        <td>
            <input type="text" class="form-control" id="item_no" name="item_no[]" readonly value="{{ $d->item_no }}">
            <input type="hidden" id="service_item_id" name="service_item_id[]" value="{{ $d->id }}">
        </td>
        <td>
            <input type="text" class="form-control" id="item_des" name="item_des[]" readonly
                value="{{ $d->item_des }}">
        </td>
        <td class="text-center">
            <div class="row row-cols-auto g-3">
                <div class="col">
                    <button type="button" class="btn btn-lg btn-danger bx bx-trash mr-1 delete-row  "
                        id="removeStepButton"></button>
                </div>
            </div>
        </td>
    </tr>
@endforeach
