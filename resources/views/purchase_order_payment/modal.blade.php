<!-- search modal -->
<div class="modal" id="formModal" aria-labelledby="formModalLabel" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable modal-fullscreen-md-down">
        <div class="modal-content">
            <div class="modal-header" id="modal-header">
            </div>
            <div class="modal-body">
                <form enctype="multipart/form-data" onsubmit="disableButton()">
                    @csrf
                    <input type="hidden" name="request_token" id="request_token">

                    <div class="col-md-12 mb-2">
                        <label for="purchase_order_id" class="form-label">Purchase Order</label>
                        <select class="form-select select-select" id="purchase_order_id" name="purchase_order_id">
                        </select>
                    </div>
                    <div class="col-md-12 mb-2">
                        <label for="vendor_name" class="form-label">Vendor</label>
                        <input type="hidden" id="client_vendor_id" name="client_vendor_id">
                        <input type="text" class="form-control" id="vendor_name" name="vendor_name" readonly>
                    </div>
                    <div class="col-md-12 mb-2">
                        <label for="due_date" class="form-label">Due Date</label>
                        <input type="text" class="form-control" id="due_date" name="due_date" readonly>
                    </div>
                    <div class="col-md-12 mb-2">
                        <label for="date" class="form-label">Payment Date</label>
                        <input type="text" class="form-control datepicker" id="date" name="date">
                    </div>
                    <div class="col-md-12 mb-2">
                        <label for="service_id" class="form-label">Amount</label>
                        <input type="text" class="form-control" id="amount" name="amount">
                    </div>

                    <div class="col-md-12 mb-2">
                        <label for="notes" class="form-label">Notes</label>
                        <textarea class="form-control" id="notes" name="notes"></textarea>
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
        $('#purchase_order_id').select2({
            theme: "bootstrap-5",
            width: $('#purchase_order_id').data('width') ? $('#purchase_order_id').data('width') : ($(
                '#purchase_order_id').hasClass(
                'w-100') ? '100%' : 'style'),
            selectOnClose: false,
            minimumResultsForSearch: 0,
            ajax: {
                url: '{{ route('purchaseorderpayment.get_purchase_order') }}',
                dataType: 'json',
                data: function(params) {
                    return {
                        term: params.term || '',
                        page: params.page || 1
                    };
                },
                processResults: function(data) {
                    return {
                        results: data.results || data
                    };
                },
                cache: true
            }
        }).on('select2:open', function() {
            setTimeout(function() {
                $('.select2-container--open .select2-search__field').trigger('focus');
                $('.select2-container--open').css('z-index', 1056);
            }, 0);
        });
    })();
</script>
