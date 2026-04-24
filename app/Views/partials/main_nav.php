<nav id="mainNavbar" class="navbar navbar-expand-lg navbar-light bg-white shadow-sm py-3 border-bottom border-2 border-secondary">
    <div class="container">

        <!-- Logo -->
        <a class="navbar-brand d-flex align-items-center" href="<?= base_url() ?>">
            <img src="<?= base_url('logo.png') ?>" 
                 alt="<?= esc($app_name ?? 'APP LOGO') ?>" 
                 height="45" 
                 class="me-2">
        </a>

        <!-- Mobile Toggle -->
        <button class="navbar-toggler" type="button"
                data-bs-toggle="collapse"
                data-bs-target="#mainMenu"
                aria-controls="mainMenu"
                aria-expanded="false"
                aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <!-- Menu -->
        <div class="collapse navbar-collapse" id="mainMenu">
            <ul class="navbar-nav ms-auto align-items-lg-center">

                <!-- Main Navigation -->
                <li class="nav-item">
                    <a class="nav-link px-3" href="https://www.kengen.co.ke/" target="_blank">
                        Main website
                    </a>
                </li>

                <!-- Resume (moved here) -->
                <?php if (session()->get('user_id')): ?>
                    <li class="nav-item">
                        <a class="nav-link px-3" href="<?= base_url('applicant/profile') ?>">
                            Resume
                        </a>
                    </li>
                <?php endif; ?>

                <li class="nav-item">
                    <a class="nav-link px-3" href="#">How to Apply</a>
                </li>

                <li class="nav-item">
                    <a class="nav-link px-3" href="#">FAQs</a>
                </li>

                <!-- Auth Links -->
                <?php if (session()->get('user_id')): ?>

                    <li class="nav-item">
                        <a class="nav-link px-3" href="<?= base_url('my-applications') ?>">
                            My Applications
                        </a>
                    </li>

                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle px-3" href="#" id="userMenu"
                           role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <?= esc(session()->get('first_name')) ?>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userMenu">
                            
                            <?php if (session()->get('access_level') == 1): ?>
                                <li>
                                    <a class="dropdown-item" href="<?= base_url('admin') ?>">
                                        Dashboard
                                    </a>
                                </li>
                                <li><hr class="dropdown-divider"></li>
                            <?php endif; ?>

                            <li>
                                <a class="dropdown-item" href="<?= base_url('logout') ?>">
                                    Logout
                                </a>
                            </li>
                        </ul>
                    </li>

                <?php else: ?>

                    <li class="nav-item">
                        <a class="btn btn-primary px-3 ms-lg-2" href="<?= base_url('login') ?>">
                            Login
                        </a>
                    </li>

                <?php endif; ?>

            </ul>
        </div>

    </div>
</nav>