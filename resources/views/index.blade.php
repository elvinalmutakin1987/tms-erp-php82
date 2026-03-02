@extends('partials.main')

@section('css')
@endsection

@section('content')
    @if (Request::get('t') == 'equipment')
        @include('dashboard.equipment')
    @elseif(Request::get('t') == 'procurement')
        @include('dashboard.procurement')
    @elseif(Request::get('t') == 'survey')
        @include('dashboard.survey')
    @elseif(Request::get('t') == 'finance')
        @include('dashboard.finance')
    @endif
@endsection

@section('js-plugin')
    <script src="assets/plugins/apexcharts-bundle/js/apexcharts.min.js"></script>
@endsection

@section('js')
    <script src="assets/js/index.js"></script>

    <script src="assets/plugins/peity/jquery.peity.min.js"></script>
    <script>
        $(".data-attributes span").peity("donut")
    </script>
@endsection
