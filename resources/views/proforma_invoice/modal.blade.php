<style>
    #formModal .modal-body {
        overflow-y: auto !important;
        max-height: calc(100vh - 160px);
        scroll-behavior: auto;
    }
</style>

<!-- search modal -->
<div class="modal" id="formModal" aria-labelledby="formModalLabel" tabindex="-1">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable modal-fullscreen-md-down">
        <div class="modal-content">
            <div class="modal-header" id="modal-header">
            </div>
            <div class="modal-body">
                <form enctype="multipart/form-data">
                    @csrf
                    <div class="row mb-2">
                        <div class="col">
                            <label for="contract_id" class="form-label">Contract</label>
                            <select class="form-select select-select" id="contract_id" name="contract_id">
                                <option value="" selected disabled></option>
                                @foreach ($contract as $d)
                                    <option value="{{ $d->id }}">{{ $d->contract_no }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col">
                            <label for="month" class="form-label">Periode</label>
                            <select class="form-select select-select" id="month" name="month">
                                @foreach (range(1, 12) as $m)
                                    <option value="{{ $m }}" {{ $m == date('n') ? 'selected' : '' }}>
                                        {{ \Carbon\Carbon::create()->month($m)->translatedFormat('F') }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col">
                            <label for="year" class="form-label">&nbsp;</label>
                            <input type="number" class="form-control" id="year" name="year"
                                value="{{ now()->year }}">
                        </div>
                    </div>
                    <div class="row mb-2">
                        <div class="col" id="div-table">
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <div class="d-md-flex d-grid align-items-center gap-1">
                    <button type="button" class="btn btn-secondary saveButton" id="saveButton1" name="status"
                        value="Draft">Draft</button>
                    <button type="button" class="btn btn-success saveButton" id="saveButton2" name="status"
                        value="Open">Save</button>
                    <button type="button" class="btn btn-light" id="cancelButton">Cancel</button>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- end search modal -->

<script>
    (() => {

    });
</script>
