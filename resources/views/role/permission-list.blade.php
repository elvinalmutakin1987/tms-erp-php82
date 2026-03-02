@foreach ($permission as $key => $value)
    <div class="form-check">
        <input class="form-check-input" type="checkbox" value="{{ $value->id }}" id="permission_{{ $value->id }}"
            name="permission_id[]">
        <label class="form-check-label" for="permission_{{ $value->id }}">
            {{ $value->name }}
        </label>
    </div>
@endforeach
