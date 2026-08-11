<!--start header -->
<header>
    <div class="topbar">
        <nav class="navbar navbar-expand gap-2 align-items-center">
            <div class="mobile-toggle-menu d-flex"><i class='bx bx-menu'></i>
            </div>

            <div class="top-menu ms-auto">
                <ul class="navbar-nav align-items-center gap-1">
                    <li class="nav-item mobile-search-icon d-flex d-lg-none" data-bs-toggle="modal"
                        data-bs-target="#SearchModal">
                        <a class="nav-link" href="avascript:;"><i class='bx bx-search'></i>
                        </a>
                    </li>

                    <li class="nav-item dark-mode d-none d-sm-flex">
                        <a class="nav-link dark-mode-icon" href="javascript:;"><i class='bx bx-moon'></i>
                        </a>
                    </li>
                </ul>
            </div>


            <div class="user-box dropdown px-3">
                <a class="d-flex align-items-center nav-link dropdown-toggle gap-3 dropdown-toggle-nocaret"
                    href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                    {{-- <img src="assets/images/avatars/avatar-2.png" class="user-img" alt="user avatar"> --}}
                    <div class="user-info">
                        <p class="user-name mb-0">{{ strtoupper(Auth::user()->username) }}</p>
                        <p class="designattion mb-0">{{ Auth::user()->roles->pluck('name')->first() }}</p>
                    </div>
                </a>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li><a class="dropdown-item d-flex align-items-center" href="javascript:;"><i
                                class="bx bx-user fs-5"></i><span>Profile</span></a>
                    </li>
                    <li><a class="dropdown-item d-flex align-items-center" href="javascript:;"
                            data-bs-toggle="offcanvas" data-bs-target="#staticBackdrop"><i
                                class="bx bx-cog fs-5"></i><span>Change Password</span></a>
                    </li>
                    <li><a class="dropdown-item d-flex align-items-center" href="{{ route('logout') }}"><i
                                class="bx bx-log-out-circle"></i><span>Logout</span></a>
                    </li>
                </ul>
            </div>
        </nav>
    </div>
</header>
<!--end header -->
