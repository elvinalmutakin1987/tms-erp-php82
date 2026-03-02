@foreach ($approval_step as $d)
    <tr>
        <td>
            #
        </td>
        <td>
            <input type="text" class="form-control" id="username" name="username" readonly value="{{ $d->user->name }}">
            <input type="hidden" class="form-control" id="user_id" name="user_id[]" value="{{ $d->user_id }}">
            <input type="hidden" class="form-control" id="approval_step_id" name="approval_step_id[]"
                value="{{ $d->id }}">
        </td>
        <td>
            <input type="text" class="form-control" id="action" name="action[]" readonly
                value="{{ $d->action }}">
        </td>
        <td>
            <input type="number" class="form-control" id="order" name="order[]" value="{{ $d->order }}">
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
