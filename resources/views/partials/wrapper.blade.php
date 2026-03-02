<!--wrapper-->
<div class="wrapper">

    @include('partials.sidebar-wrapper')

    @include('partials.header-wrapper')

    @yield('content')

    <!--start overlay-->
    <div class="overlay mobile-toggle-icon"></div>
    <!--end overlay-->

    <!--Start Back To Top Button-->
    <a href="javaScript:;" class="back-to-top"><i class='bx bxs-up-arrow-alt'></i></a>
    <!--End Back To Top Button-->

    <footer class="page-footer">
        <p class="mb-0">Copyright © {{ date('Y') }}. PT Tunas Mitra Sejati.</p>
    </footer>
</div>
<!--end wrapper-->
