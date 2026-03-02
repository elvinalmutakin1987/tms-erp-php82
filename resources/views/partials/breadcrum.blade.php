<!--breadcrumb-->
<div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
    <div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0 p-0">
                <li class="breadcrumb-item"><a href="{{ route('dashboard', ['t' => request()->query('t')]) }}"><i
                            class="bx bx-home-alt"></i></a>
                </li>
                @if ($breadcrum['module'])
                    <li class="breadcrumb-item" aria-current="page">
                        @if ($breadcrum['route-module'])
                            <a href="{{ route($breadcrum['route-module']) }}">{{ $breadcrum['module'] }}</a>
                        @else
                            {{ $breadcrum['module'] }}
                        @endif
                    </li>
                @endif
                @if ($breadcrum['sub-module'])
                    <li class="breadcrumb-item" aria-current="page">
                        @if ($breadcrum['route-sub-module'])
                            <a href="{{ route($breadcrum['route-sub-module']) }}">{{ $breadcrum['sub-module'] }}</a>
                        @else
                            {{ $breadcrum['sub-module'] }}
                        @endif
                    </li>
                @endif
            </ol>
        </nav>
    </div>

</div>
<!--end breadcrumb-->
