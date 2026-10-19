<style>
    #formUpdate .modal-body {
        overflow-y: auto !important;
        max-height: calc(100vh - 160px);
        scroll-behavior: auto;
    }
</style>


<!-- search modal -->
<div class="modal" id="formUpdate" aria-labelledby="formUpdateLabel" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable modal-fullscreen-md-down">
        <div class="modal-content">
            <div class="modal-header" id="modal-update-header">
            </div>
            <div class="modal-body" id="modal-update-body">
                <form enctype="multipart/form-data" onsubmit="disableButton()">
                    @csrf
                    <div class="row mb-2">
                        <div class="col">
                            <label for="update_contract_no" class="form-label">Contract No.</label>
                            <input type="text" class="form-control" id="update_contract_no" name="update_contract_no"
                                readonly>
                        </div>
                        <div class="col">
                            <label for="invoice_no" class="form-label">Invoice No.</label>
                            <input type="text" class="form-control" id="invoice_no" name="invoice_no" readonly>
                        </div>
                    </div>
                    <div class="row mb-2">
                        <div class="col">
                            <label for="cut_off_date" class="form-label">Cut Of Date</label>
                            <input type="text" class="form-control datepicker" id="cut_off_date" name="cut_off_date">
                        </div>
                        <div class="col">
                            <label for="consolidation_date" class="form-label">Konsolidasi Data TMS & CMD</label>
                            <input type="text" class="form-control datepicker" id="consolidation_date"
                                name="consolidation_date">
                        </div>
                    </div>
                    <div class="row mb-2">
                        <div class="col">
                            <label for="progress_claim_date" class="form-label">Kirim Progress Claim Approval</label>
                            <input type="text" class="form-control datepicker" id="progress_claim_date"
                                name="progress_claim_date">
                        </div>
                        <div class="col">
                            <label for="ops_received_date" class="form-label">Data Diterima Dari OPS</label>
                            <input type="text" class="form-control datepicker" id="ops_received_date"
                                name="ops_received_date">
                        </div>
                    </div>
                    <div class="row mb-2">
                        <div class="col">
                            <label for="prof_inv_app_date" class="form-label">Proforma Invoice Approved</label>
                            <input type="text" class="form-control datepicker" id="prof_inv_app_date"
                                name="prof_inv_app_date">
                        </div>
                        <div class="col">
                            <label for="cic_request_date" class="form-label">CIC Request Date</label>
                            <input type="text" class="form-control datepicker" id="cic_request_date"
                                name="cic_request_date">
                        </div>
                    </div>
                    <div class="row mb-2">
                        <div class="col">
                            <label for="cic_created_date" class="form-label">Pembuatan CIC</label>
                            <input type="text" class="form-control datepicker" id="cic_created_date"
                                name="cic_created_date">
                        </div>
                        <div class="col">
                            <label for="cic_received_date" class="form-label">Terima CIC</label>
                            <input type="text" class="form-control datepicker" id="cic_received_date"
                                name="cic_received_date">
                        </div>
                    </div>
                    <div class="row mb-2">
                        <div class="col">
                            <label for="inv_date" class="form-label">Tanggal Invoice</label>
                            <input type="text" class="form-control datepicker" id="inv_date" name="inv_date">
                        </div>
                        <div class="col">
                            <label for="inv_create_date" class="form-label">Pembuatan Invoice</label>
                            <input type="text" class="form-control datepicker" id="inv_create_date"
                                name="inv_create_date">
                        </div>
                    </div>
                    <div class="row mb-2">
                        <div class="col">
                            <label for="cic_send_date" class="form-label">Kirim CIC Ke KPC</label>
                            <input type="text" class="form-control datepicker" id="cic_send_date"
                                name="cic_send_date">
                        </div>
                        <div class="col">
                            <label for="cic_ready_to_pick_date" class="form-label">Informasi CIC Bisa Diambil</label>
                            <input type="text" class="form-control datepicker" id="cic_ready_to_pick_date"
                                name="cic_ready_to_pick_date">
                        </div>
                    </div>
                    <div class="row mb-2">
                        <div class="col">
                            <label for="cic_pick_up_date" class="form-label">CIC Diambil TMS</label>
                            <input type="text" class="form-control datepicker" id="cic_pick_up_date"
                                name="cic_pick_up_date">
                        </div>
                        <div class="col">
                            <label for="inv_send_date" class="form-label">Invoice Terima KPC</label>
                            <input type="text" class="form-control datepicker" id="inv_send_date"
                                name="inv_send_date">
                        </div>
                    </div>
                    <div class="row mb-2">
                        <div class="col">
                            <label for="cic_number" class="form-label">CIC Number</label>
                            {{-- <input type="text" class="form-control" id="cic_number" name="cic_number"> --}}
                            <table class="table" id="cicTable">
                                <thead class="table-dark">
                                    <tr>
                                        <th class="text-center">CIC Number</th>
                                        <th class="text-end">Total</th>
                                        <th class="text-center" style="width:50px;">Action</th>
                                    </tr>
                                </thead>
                                <tbody>

                                </tbody>
                            </table>
                        </div>
                        <div class="col">
                            <label for="cic_path" class="form-label">Attachment</label>
                            <input class="form-control" type="file" id="cic_path" name="cic_path">
                        </div>
                    </div>
                    <div class="row mb-2">
                        <div class="col"></div>
                        <div class="col" id="div-file">

                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <div class="d-md-flex d-grid align-items-center gap-1">
                    <button type="button" class="btn btn-primary saveUpdateButton" id="saveUpdateButton1"
                        name="status" value="Save">Save</button>
                    <button type="button" class="btn btn-success saveUpdateButton" id="saveUpdateButton2"
                        name="status" value="Done">Done</button>
                    <button type="button" class="btn btn-light" id="cancelUpdateButton">Cancel</button>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- end search modal -->

<script>
    (function() {
        const tbody = document.querySelector('#cicTable tbody');
        const FORMAT = '0,0.[00]';

        function parseRaw(str) {
            if (!str) return '';
            let cleaned = String(str).replace(/[^\d.-]/g, '');
            if (cleaned === '' || cleaned === '-' || cleaned === '.') return '';
            return cleaned;
        }

        function formatDisplay(raw) {
            if (raw === '') return '';
            const num = Number(raw);
            if (isNaN(num)) return '';
            return numbro(num).format(FORMAT);
        }

        // ⬇️ createRow sekarang MENERIMA nilai awal
        function createRow(initialNumber = '', initialTotal = '') {
            const tr = document.createElement('tr');

            // Kolom 1: CIC Number
            const tdNumber = document.createElement('td');
            const inputNumber = document.createElement('input');
            inputNumber.type = 'text';
            inputNumber.className = 'form-control';
            inputNumber.name = 'cic_number[]';
            inputNumber.value = initialNumber; // ⬅️ isi nilai awal
            tdNumber.appendChild(inputNumber);

            // Kolom 2: CIC Total
            const tdTotal = document.createElement('td');
            const inputTotal = document.createElement('input');
            inputTotal.type = 'text';
            inputTotal.className = 'form-control';
            inputTotal.name = 'cic_total[]';
            inputTotal.inputMode = 'decimal';
            inputTotal.autocomplete = 'off';
            inputTotal.style.textAlign = 'right';
            inputTotal.dataset.raw = '';

            // ⬇️ isi total awal (kalau ada)
            if (initialTotal !== '' && initialTotal !== null && initialTotal !== undefined) {
                const raw = parseRaw(String(initialTotal));
                inputTotal.dataset.raw = raw;
                inputTotal.value = formatDisplay(raw);
            }
            tdTotal.appendChild(inputTotal);

            // Formatter live
            inputTotal.addEventListener('input', () => {
                const raw = parseRaw(inputTotal.value);
                inputTotal.dataset.raw = raw;
                inputTotal.value = formatDisplay(raw);
            });

            inputTotal.addEventListener('blur', () => {
                inputTotal.value = formatDisplay(inputTotal.dataset.raw || '');
            });

            // Kolom 3: tombol hapus
            const tdAction = document.createElement('td');
            const btn = document.createElement('button');
            btn.type = 'button';
            btn.className = 'btn btn-sm btn-danger';
            btn.textContent = 'Delete';
            btn.addEventListener('click', () => {
                if (tbody.rows.length > 1) {
                    tr.remove();
                } else {
                    inputNumber.value = '';
                    inputTotal.value = '';
                    inputTotal.dataset.raw = '';
                    inputNumber.focus();
                }
            });
            tdAction.appendChild(btn);

            tr.appendChild(tdNumber);
            tr.appendChild(tdTotal);
            tr.appendChild(tdAction);

            function addNewRowAndFocus() {
                const newRow = createRow();
                tbody.appendChild(newRow);
                newRow.querySelector('input').focus();
            }

            inputNumber.addEventListener('keydown', (e) => {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    addNewRowAndFocus();
                }
            });

            inputTotal.addEventListener('keydown', (e) => {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    inputTotal.value = formatDisplay(inputTotal.dataset.raw || '');
                    addNewRowAndFocus();
                }
            });

            inputTotal.addEventListener('keydown', (e) => {
                if (e.key === 'Tab' && !e.shiftKey) {
                    const isLastRow = tr === tbody.rows[tbody.rows.length - 1];
                    const isNumberFilled = inputNumber.value.trim() !== '';
                    const isTotalFilled = (inputTotal.dataset.raw || '') !== '';

                    if (isLastRow && isNumberFilled && isTotalFilled) {
                        e.preventDefault();
                        inputTotal.value = formatDisplay(inputTotal.dataset.raw || '');
                        addNewRowAndFocus();
                    }
                }
            });

            return tr;
        }

        // ⬇️ EXPOSE ke global supaya bisa dipanggil dari view utama
        window.CicTable = {
            // reset: kosongkan & buat 1 baris kosong
            reset: function() {
                tbody.innerHTML = '';
                tbody.appendChild(createRow());
            },

            // fill: isi tabel dari data (array / string JSON / double-encoded)
            fill: function(data) {
                tbody.innerHTML = '';

                // Handle string JSON / double-encoded
                if (typeof data === 'string') {
                    try {
                        data = JSON.parse(data);
                        if (typeof data === 'string') data = JSON.parse(data);
                    } catch (e) {
                        data = [];
                    }
                }

                // Kalau kosong / bukan array → 1 baris kosong
                if (!Array.isArray(data) || data.length === 0) {
                    tbody.appendChild(createRow());
                    return;
                }

                data.forEach(item => {
                    tbody.appendChild(createRow(
                        item.cic_number ?? '',
                        item.cic_total ?? ''
                    ));
                });
            }
        };

        // baris default pertama
        tbody.appendChild(createRow());
        tbody.querySelector('input').focus();
    })();
</script>
