<!-- Modal -->
<div class="modal" id="formModal" tabindex="-1" aria-labelledby="formModalLabel">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-fullscreen-md-down">
        <div class="modal-content">
            <div class="modal-header" id="modal-header"></div>

            <div class="modal-body">
                <form enctype="multipart/form-data" onsubmit="disableButton()">
                    @csrf
                    <input type="hidden" name="request_token" id="request_token">

                    <div class="col-md-12 mb-2">
                        <label class="form-label">Type</label>
                        <select class="form-select" id="type" name="type">
                            <option value="AN">AN</option>
                            <option value="ANSOL">ANSOL</option>
                            <option value="Pupuk">Pupuk</option>
                            <option value="Acc">Acc</option>
                            <option value="LCT">LCT</option>
                            <option value="Pallet">Pallet</option>
                            <option value="Hazardous Waste">Hazardous Waste</option>
                            <option value="Tanker">Tanker</option>
                        </select>
                    </div>

                    <div class="col-md-12 mb-2">
                        <label class="form-label">Periode</label>
                        <input type="text" class="form-control" id="year" name="year"
                            value="{{ date('Y') }}">
                    </div>

                    <div class="col-md-12 mb-2">
                        <table class="table table-striped table-bordered mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th width="10%">No</th>
                                    <th width="35%">Month</th>
                                    <th width="55%">Value</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $months = [
                                        1 => 'January',
                                        2 => 'February',
                                        3 => 'March',
                                        4 => 'April',
                                        5 => 'May',
                                        6 => 'June',
                                        7 => 'July',
                                        8 => 'August',
                                        9 => 'September',
                                        10 => 'October',
                                        11 => 'November',
                                        12 => 'December',
                                    ];
                                @endphp

                                @foreach ($months as $key => $month)
                                    <tr>
                                        <td class="text-center">{{ $key }}</td>
                                        <td>{{ $month }}</td>
                                        <td>
                                            <input type="text" class="form-control value-month"
                                                data-target="value{{ $key }}">

                                            <input type="hidden" id="value{{ $key }}"
                                                name="value-{{ $key }}">
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </form>
            </div>

            <div class="modal-footer">
                <div class="d-md-flex d-grid gap-1">
                    <button type="button" class="btn btn-success" id="saveButton">Save</button>
                    <button type="button" class="btn btn-light" id="cancelButton">Cancel</button>
                </div>
            </div>
        </div>
    </div>
</div>

@push('script-format')
    <script>
        (() => {
            const inputs = document.querySelectorAll('.value-month');

            function formatCurrency(el) {
                let cursor = el.selectionStart;
                let value = el.value.replace(/[^0-9.]/g, '');

                let parts = value.split('.');
                let integer = parts[0];
                let decimal = parts[1] ?? '';

                integer = integer.replace(/^0+(?=\d)/, '') || '0';

                integer = integer.replace(/\B(?=(\d{3})+(?!\d))/g, ',');

                let result = integer;

                if (decimal !== '') {
                    result += '.' + decimal.substring(0, 2);
                }

                el.value = result;

                if (el.setSelectionRange) {
                    let diff = result.length - value.length;
                    el.setSelectionRange(cursor + diff, cursor + diff);
                }

                let target = el.dataset.target;

                if (target) {
                    document.getElementById(target).value = result.replace(/,/g, '');
                }
            }

            inputs.forEach(input => {
                input.addEventListener('keydown', function(e) {
                    const allowed = [
                        'Backspace',
                        'Delete',
                        'ArrowLeft',
                        'ArrowRight',
                        'Tab',
                        'Home',
                        'End'
                    ];

                    if (allowed.includes(e.key)) return;

                    if (!/[0-9.]/.test(e.key)) {
                        e.preventDefault();
                    }
                });

                input.addEventListener('input', function() {
                    formatCurrency(this);
                });
            });
        })();
    </script>
@endpush
