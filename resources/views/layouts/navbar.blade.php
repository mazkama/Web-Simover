<!-- partial:partials/_navbar.html -->
<nav class="navbar">
    <a href="#" class="sidebar-toggler">
        <i data-feather="menu"></i>
    </a>
    <div class="navbar-content">
        <ul class="navbar-nav">
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" id="profileDropdown" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <!-- <img class="wd-30 ht-30 rounded-circle" src="https://via.placeholder.com/30x30" alt="profile"> -->
                    <i data-feather="user"></i>
                </a>
                <div class="dropdown-menu p-0" aria-labelledby="profileDropdown">
                    <div class="d-flex flex-column align-items-center border-bottom px-5 py-3">
                        <div class="mb-3">
                            <img class="wd-80 ht-80 rounded-circle" src="https://via.placeholder.com/80x80" alt="">
                        </div>
                        <div class="text-center">
                            @if (session('user_name'))
                            <p class="tx-16 fw-bolder">{{ session('user_name') }}</p>
                            <p class="tx-12 text-muted">{{ session('user_email') }}</p>
                            @endif
                        </div>
                    </div>
                    <ul class="list-unstyled p-1">
                        <li class="dropdown-item py-2">
                            <a href="pages/general/profile.html" class="text-body ms-0">
                                <i class="me-2 icon-md" data-feather="user"></i>
                                <span>Profile</span>
                            </a>
                        </li>
                        <!-- <li class="dropdown-item py-2">
                            <a href="javascript:;" class="text-body ms-0">
                                <i class="me-2 icon-md" data-feather="edit"></i>
                                <span>Edit Profile</span>
                            </a>
                        </li>
                        <li class="dropdown-item py-2">
                            <a href="javascript:;" class="text-body ms-0">
                                <i class="me-2 icon-md" data-feather="repeat"></i>
                                <span>Switch User</span>
                            </a>
                        </li> -->
                        <li class="dropdown-item py-2">
                            <a href="{{ route('firebase.logout') }}"
                               onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
                               class="d-flex align-items-center text-body text-decoration-none">
                                <i class="me-2 icon-md" data-feather="log-out"></i>
                                <span>Log Out</span>
                            </a>
                        </li>

                        <!-- Form logout diletakkan di luar menu -->
                        <form id="logout-form" action="{{ route('firebase.logout') }}" method="POST" style="display: none;">
                            @csrf
                        </form>
                        <!-- <li class="dropdown-item py-2">
                            <a href="javascript:;" class="text-body ms-0">
                                <i class="me-2 icon-md" data-feather="log-out"></i>
                                <span>Log Out</span>
                            </a>
                        </li> -->
                    </ul>
                </div>
            </li>
        </ul>
    </div>
</nav>
<!-- partial -->
