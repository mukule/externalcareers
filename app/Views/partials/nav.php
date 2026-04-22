<nav class="sb-topnav navbar navbar-expand navbar-light bg-light">
    <!-- Navbar Brand-->
   <a class="navbar-brand ps-3" href="<?= site_url('index') ?>">
    <img src="<?= base_url('logo.png') ?>" alt="<?= esc($app_name) ?>" height="40">
</a>


    <!-- Sidebar Toggle-->
    <button class="btn btn-link btn-sm order-1 order-lg-0 me-4 me-lg-0"
            id="sidebarToggle" href="#!">
        <i class="fas fa-bars"></i>
    </button>

    <!-- Right side: user dropdown -->
    <ul class="navbar-nav ms-auto me-3 me-lg-4">
        <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" id="navbarDropdown" href="#"
               role="button" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="fas fa-user fa-fw"></i>
            </a>
            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                <li><a class="dropdown-item" href="#">Profile</a></li>
                <li><hr class="dropdown-divider" /></li>
                <li><a class="dropdown-item" href="<?= site_url('auth/change-password') ?>">Change Password</a></li>
                 <li><hr class="dropdown-divider" /></li>
                <li><a class="dropdown-item" href="<?= site_url('logout') ?>">Logout</a></li>
            </ul>
        </li>
    </ul>
</nav>
