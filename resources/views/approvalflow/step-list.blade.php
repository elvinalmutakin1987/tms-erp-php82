@foreach ($approval_step as $d)
    <tr class="row-number">
        <td class="p-1 align-middle row-number">
            {{ $loop->iteration }}
        </td>

        <td class="p-1 align-middle">
            <input type="text" class="form-control" name="username[]" readonly value="{{ $d->user->name }}">
            <input type="hidden" class="form-control" name="user_id[]" value="{{ $d->user_id }}">
            <input type="hidden" class="form-control" name="approval_step_id[]" value="{{ $d->id }}">
        </td>

        <td class="p-1 align-middle">
            <select class="form-select select-select step-action" name="action[]">
                <option value="Requested" {{ $d->action == 'Requested' ? 'selected' : '' }}>
                    Requested
                </option>
                <option value="Known" {{ $d->action == 'Known' ? 'selected' : '' }}>
                    Known
                </option>
                <option value="Checked" {{ $d->action == 'Checked' ? 'selected' : '' }}>
                    Checked
                </option>
                <option value="Approved" {{ $d->action == 'Approved' ? 'selected' : '' }}>
                    Approved
                </option>
            </select>
        </td>

        <td class="p-1 align-middle">
            <input type="number" class="form-control" name="order[]" value="{{ $d->order }}">
        </td>

        <td class="text-center p-1 align-middle">
            <div class="row row-cols-auto g-3">
                <div class="col">
                    <button type="button" class="btn btn-lg btn-danger bx bx-trash mr-1 delete-row"></button>
                </div>
            </div>
        </td>
    </tr>
@endforeach
