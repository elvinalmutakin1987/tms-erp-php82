<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!--favicon-->
    <link rel="icon" href="{{ asset('assets/images/tms_logo.png') }}" type="image/png">

    @include('partials.css')

    <title>{{ env('APP_NAME') }}</title>
</head>
