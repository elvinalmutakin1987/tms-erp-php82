@extends('partials.main')

@section('css')
    <link href="{{ asset('assets/plugins/datatable/css/dataTables.bootstrap5.min.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" />
    <link rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />
    <link href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css" rel="stylesheet" />
    <link href="assets/plugins/fullcalendar/css/main.min.css" rel="stylesheet" />
@endsection

@section('content')
    <!--start page wrapper -->
    <div class="page-wrapper">
        <div class="page-content container-xxl">

            @include('partials.breadcrum')

            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col">
                                    <select class="form-select select-top" id="month" name="month">
                                        <option value="All">All Month</option>
                                        <option value="01">January</option>
                                        <option value="02">February</option>
                                        <option value="03">March</option>
                                        <option value="04">April</option>
                                        <option value="05">May</option>
                                        <option value="06">June</option>
                                        <option value="07">July</option>
                                        <option value="08">August</option>
                                        <option value="09">September</option>
                                        <option value="10">October</option>
                                        <option value="11">November</option>
                                        <option value="12">December</option>
                                    </select>
                                </div>
                                <div class="col">
                                    <select class="form-select select-top" id="year" name="year">
                                        <option value="All">All Year</option>
                                        @for ($year = date('Y'); $year >= 2010; $year--)
                                            <option value="{{ $year }}"
                                                {{ request('_year') == $year ? 'selected' : '' }}>
                                                {{ $year }}
                                            </option>
                                        @endfor
                                    </select>
                                </div>

                                <div class="col">
                                    <button type="button" class="btn btn-primary getResultButton" id="getResultButton"
                                        name="status">Get Result</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body" id="div-result" style="display: none">
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
    <!--end page wrapper -->

    @include('report.po_monitoring.modal-detail')
@endsection

@section('js')
    <script src="{{ asset('assets/plugins/datatable/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/datatable/js/dataTables.bootstrap5.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="{{ asset('assets/plugins/select2/js/select2-custom.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="assets/plugins/fullcalendar/js/main.min.js"></script>

    <script>
        const saveButton = document.getElementById('saveButton');
        const saveUpdateButton = document.getElementById('saveUpdateButton');

        var unitId = '';

        $(document).ready(function() {
            gen_select2();
        });

        function get_result() {
            var month = $("#month").val();
            var year = $("#year").val();
            const params = new URLSearchParams(window.location.search);
            const t = params.get('t');

            $('#div-result')
                .css('display', 'block')
                .html(`
                    <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                    <span>Loading...</span>
                `);

            const url = '{{ route('report.get_result') }}';

            $.ajax({
                url: url,
                type: 'GET',
                data: {
                    year: year,
                    month: month,
                    t: t
                },
                success: function(response) {
                    $('#div-result').html(response.html);
                },
                error: function(xhr, status, error) {
                    console.error('Error:', error);

                    $('#div-result').css('display', 'block').html(`
                        <div class="alert alert-danger mb-0">
                            Failed to load data.
                        </div>
                    `);
                }
            });
        }

        function gen_select2() {
            $('.select-top').each(function() {
                const $el = $(this);

                if ($el.hasClass('select2-hidden-accessible')) {
                    $el.select2('destroy');
                }

                $el.select2({
                    theme: "bootstrap-5",
                    width: $el.data('width') ? $el.data('width') : ($el.hasClass('w-100') ? '100%' :
                        'style')
                });

                $el.off('change.topFilter').on('change.topFilter', function() {
                    $('#table-data').DataTable().draw();
                });
            });
        }

        $(document).off('click.getResultButton').on('click.getResultButton', '.getResultButton', function() {
            get_result();
        });
    </script>
    <!--app JS-->
@endsection
