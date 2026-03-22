<div id="layoutSidenav_nav">
    <nav class="sb-sidenav accordion sb-sidenav-light" id="sidenavAccordion">
        <div class="sb-sidenav-menu">
            <div class="nav">

                <?php $role = strtolower(session()->get('role') ?? ''); ?>

                <?php if ($role === 'admin'): ?>

                    <!-- Dashboard -->
                    <a class="nav-link <?= (uri_string() === 'index' ? 'active' : '') ?>" 
                       href="<?= base_url('index') ?>">
                        <div class="sb-nav-link-icon"><i class="fas fa-chart-line"></i></div>
                        Dashboard
                    </a>

                    <!-- Vacancies Section (Collapsible) -->
                    <a class="nav-link collapsed" href="#" data-bs-toggle="collapse" data-bs-target="#collapseVacancies" aria-expanded="false" aria-controls="collapseVacancies">
                        <div class="sb-nav-link-icon"><i class="fas fa-briefcase"></i></div>
                        Vacancies
                        <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                    </a>
                    <div class="collapse" id="collapseVacancies" data-bs-parent="#sidenavAccordion">
                        <nav class="sb-sidenav-menu-nested nav">
                            <a class="nav-link <?= (uri_string() === 'admin/job-types' ? 'active' : '') ?>" href="<?= base_url('admin/job-types') ?>">Job Types</a>
                            <a class="nav-link <?= (uri_string() === 'admin/job-disciplines' ? 'active' : '') ?>" href="<?= base_url('admin/job-disciplines') ?>">Job Disciplines</a>
                            <a class="nav-link <?= (uri_string() === 'admin/vacancies' ? 'active' : '') ?>" href="<?= base_url('admin/jobs') ?>">Vacancies</a>
                            <a class="nav-link <?= (uri_string() === 'admin/jobs-applications' ? 'active' : '') ?>" href="<?= base_url('admin/jobs-applications') ?>">Applications</a>
                        </nav>
                    </div>

                    <a class="nav-link collapsed" href="#" data-bs-toggle="collapse" data-bs-target="#collapseEducation" aria-expanded="false" aria-controls="collapseEducation">
                        <div class="sb-nav-link-icon"><i class="fas fa-graduation-cap"></i></div>
                        Education
                        <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                    </a>
                    <div class="collapse" id="collapseEducation" data-bs-parent="#sidenavAccordion">
                        <nav class="sb-sidenav-menu-nested nav">
                            <a class="nav-link <?= (uri_string() === 'admin/education-levels' ? 'active' : '') ?>" href="<?= base_url('admin/education-levels') ?>">Education Levels</a>
                            <a class="nav-link <?= (uri_string() === 'admin/fields-of-study' ? 'active' : '') ?>" href="<?= base_url('admin/fields-of-study') ?>">Fields of Study</a>
                        </nav>
                    </div>

                    <!-- Certifications Section (Collapsible) -->
                    <a class="nav-link collapsed" href="#" data-bs-toggle="collapse" data-bs-target="#collapseCertifications" aria-expanded="false" aria-controls="collapseCertifications">
                        <div class="sb-nav-link-icon"><i class="fas fa-certificate"></i></div>
                        Certifications
                        <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                    </a>
                    <div class="collapse" id="collapseCertifications" data-bs-parent="#sidenavAccordion">
                        <nav class="sb-sidenav-menu-nested nav">
                            <a class="nav-link <?= (uri_string() === 'admin/certifying-bodies' ? 'active' : '') ?>" href="<?= base_url('admin/certifying-bodies') ?>">Certifying Bodies</a>
                             <a class="nav-link <?= (uri_string() === 'admin/certifications' ? 'active' : '') ?>" href="<?= base_url('admin/certifications') ?>">Certifications</a>
                        </nav>
                    </div>

                    <!-- Other Job Portal Links -->
                   

                    <a class="nav-link collapsed" href="#" data-bs-toggle="collapse" data-bs-target="#collapseDemographics" aria-expanded="false" aria-controls="collapseDemographics">
                        <div class="sb-nav-link-icon"><i class="fas fa-users"></i></div>
                        Demographics
                        <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                    </a>
                    <div class="collapse" id="collapseDemographics" data-bs-parent="#sidenavAccordion">
                        <nav class="sb-sidenav-menu-nested nav">
                            <a class="nav-link <?= (uri_string() === 'admin/ethnicities' ? 'active' : '') ?>" href="<?= base_url('admin/ethnicities') ?>">Ethnicities</a>
                            <a class="nav-link <?= (uri_string() === 'admin/counties' ? 'active' : '') ?>" href="<?= base_url('admin/counties') ?>">Counties</a>
                        </nav>
                    </div>

                  
                    
                     <a class="nav-link <?= (uri_string() === 'admin/staffs' ? 'active' : '') ?>" href="<?= base_url('admin/staffs') ?>">
                        <div class="sb-nav-link-icon"><i class="fas fa-user-shield"></i></div>
                        Staff Members   
                    </a>

                     <a class="nav-link collapsed" href="#" data-bs-toggle="collapse" data-bs-target="#collapseLogs" aria-expanded="false" aria-controls="collapseLogs">
                        <div class="sb-nav-link-icon"><i class="fas fa-file-alt"></i></div>
                        Logs
                        <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                    </a>
                    <div class="collapse" id="collapseLogs" data-bs-parent="#sidenavAccordion">
                        <nav class="sb-sidenav-menu-nested nav">
                            <a class="nav-link <?= (uri_string() === 'admin/admin-logs' ? 'active' : '') ?>" href="<?= base_url('admin/admin-logs') ?>">Admin Logs</a>
                            <a class="nav-link <?= (uri_string() === 'admin/user-logs' ? 'active' : '') ?>" href="<?= base_url('admin/user-logs') ?>">User Logs</a>
                        </nav>
                    </div>

                 

                <?php else: ?>
                    <div class="text-center text-muted mt-4">
                        <i class="fas fa-lock"></i> No Access
                    </div>
                <?php endif; ?>

            </div>
        </div>

        <!-- Footer -->
        <div class="sb-sidenav-footer text-start">
            <?php if (isset($user)): ?>
                <strong><?= esc($user['first_name'] ?? '') ?> <?= esc($user['last_name'] ?? '') ?></strong><br>
                <small class="text-muted">
                    <i class="fas fa-user-shield"></i> <?= esc(ucfirst($user['role'] ?? '')) ?>
                </small>
            <?php else: ?>
                Guest
            <?php endif; ?>
        </div>
    </nav>
</div>
