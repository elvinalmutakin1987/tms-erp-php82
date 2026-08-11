@extends('partials.main')

@section('content')
    @if (Request::get('t') == 'breakdown-summary')
        @include('report.breakdown_summary.index')
    @elseif(Request::get('t') == 'monitoring-invoice')
        @include('report.monitoring_invoice.index')
    @elseif(Request::get('t') == 'monitoring-po')
        @include('report.monitoring_po.index')
    @endif
@endsection
