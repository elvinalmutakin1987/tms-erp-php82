@extends('partials.main')

@section('content')
    @if (Request::get('t') == 'breakdown-summary')
        @include('report.breakdown_summary.index')
    @elseif(Request::get('t') == 'invoice-monitoring')
        @include('report.invoice_monitoring.index')
    @elseif(Request::get('t') == 'po-monitoring')
        @include('report.po_monitoring.index')
    @endif
@endsection
