<nav class="sidebar">
    <div class="sidebar-header">
        <a href="#" class="sidebar-brand">
            Simover<span>App</span>
        </a>
        <div class="sidebar-toggler not-active">
            <span></span>
            <span></span>
            <span></span>
        </div>
    </div>
    <div class="sidebar-body">
        <ul class="nav">
            {{-- <li class="nav-item nav-category">Main</li> --}}
            <li class="nav-item">
                <a href="{{ route('dashboard') }}" class="nav-link {{ Request::is('dashboard') ? 'active' : '' }}">
                    <i class="link-icon" data-feather="monitor"></i>
                    <span class="link-title">Dashboard</span>
                </a>
            </li>
            <li class="nav-item nav-category">Menu</li>
            <li class="nav-item">
                <a class="nav-link" data-bs-toggle="collapse" href="#emails" role="button" aria-expanded="false" aria-controls="emails">
                    <i class="link-icon" data-feather="smartphone"></i>
                    <span class="link-title">Perangkat</span>
                    <i class="link-arrow" data-feather="chevron-down"></i>
                </a>
                <div class="collapse" id="emails">
                    <ul class="nav sub-menu">
                        <li class="nav-item">
                            <a href="pages/email/inbox.html" class="nav-link">Semua Perangkat</a>
                        </li>
                        <li class="nav-item">
                            <a href="pages/email/read.html" class="nav-link">Tambah Perangkat</a>
                        </li>
                    </ul>
                </div>
            </li>
            <li class="nav-item">
                <a href="{{ route('riwayat') }}" class="nav-link">
                    <i class="link-icon" data-feather="database"></i>
                    <span class="link-title">Riwayat</span>
                </a>
            </li>
            </li>
            <br>
            <li class="nav-item">
                <a href="" class="nav-link">
                    <i class="link-icon" data-feather="log-out"></i>
                    <span class="link-title">Keluar</span>
                </a>
            </li>

        </ul>
    </div>
</nav>
<!-- <nav class="settings-sidebar">
    <div class="sidebar-body">
        <a href="#" class="settings-sidebar-toggler">
            <i data-feather="settings"></i>
        </a>
        <h6 class="text-muted mb-2">Sidebar:</h6>
        <div class="mb-3 pb-3 border-bottom">
            <div class="form-check form-check-inline">
                <input type="radio" class="form-check-input" name="sidebarThemeSettings" id="sidebarLight" value="sidebar-light" checked>
                <label class="form-check-label" for="sidebarLight">
                    Light
                </label>
            </div>
            <div class="form-check form-check-inline">
                <input type="radio" class="form-check-input" name="sidebarThemeSettings" id="sidebarDark" value="sidebar-dark">
                <label class="form-check-label" for="sidebarDark">
                    Dark
                </label>
            </div>
        </div>
        <div class="theme-wrapper">
            <h6 class="text-muted mb-2">Light Theme:</h6>
            <a class="theme-item active" href="../demo1/dashboard.html">
                <img src="../assets/images/screenshots/light.jpg" alt="light theme">
            </a>
            <h6 class="text-muted mb-2">Dark Theme:</h6>
            <a class="theme-item" href="../demo2/dashboard.html">
                <img src="../assets/images/screenshots/dark.jpg" alt="light theme">
            </a>
        </div>
    </div>
</nav> -->
