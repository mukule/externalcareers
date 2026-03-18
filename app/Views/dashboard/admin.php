<?= $this->extend('layouts/dashboard') ?>
<?= $this->section('content') ?>

<div class="container-fluid px-4 py-4">
    
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0">Dashboard Overview</h4>
        <small class="text-muted"><?= date('d M Y, H:i') ?></small>
    </div>
    
    <!-- Statistics Cards -->
    <div class="row g-4 mb-3">

        <!-- Total Applicants -->
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body p-4 d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-muted text-uppercase small fw-semibold mb-1">Total Registrants</p>
                        <h3 class="mb-0 fw-bold"><?= esc($totalApplicants ?? 0) ?></h3>
                    </div>
                    <div class="icon-circle bg-primary-subtle text-primary">
                        <i class="fas fa-users"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Jobs -->
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body p-4 d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-muted text-uppercase small fw-semibold mb-1">Total Jobs</p>
                        <h3 class="mb-0 fw-bold"><?= esc($totalJobs ?? 0) ?></h3>
                    </div>
                    <div class="icon-circle bg-primary-subtle text-primary">
                        <i class="fas fa-briefcase"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Open Jobs -->
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body p-4 d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-muted text-uppercase small fw-semibold mb-1">Open Jobs</p>
                        <h3 class="mb-0 fw-bold"><?= esc($openJobs ?? 0) ?></h3>
                    </div>
                    <div class="icon-circle bg-primary-subtle text-primary">
                        <i class="fas fa-folder-open"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Applications -->
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body p-4 d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-muted text-uppercase small fw-semibold mb-1">Applications</p>
                        <h3 class="mb-0 fw-bold"><?= esc($totalApplications ?? 0) ?></h3>
                    </div>
                    <div class="icon-circle bg-primary-subtle text-primary">
                        <i class="fas fa-file-alt"></i>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <!-- Applicants with Filters -->
    <div class="row g-4">

        <div class="col-xl-12">
            <div class="card shadow-sm border-0 p-2">
                <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 fw-bold">Registrants</h5>
                    <a href="#" class="btn btn-sm btn-outline-primary">
                        View All <i class="fas fa-arrow-right ms-1"></i>
                    </a>
                </div>
                
                <!-- Filter Form -->
                <div class="card-body py-2">
                    <form method="get" class="row g-2 align-items-end mb-3">
                        <div class="col-md-3">
                            <label for="filterName" class="form-label small">Name</label>
                            <input type="text" class="form-control form-control-sm" id="filterName" name="name" value="<?= esc($filters['name'] ?? '') ?>" placeholder="Search Name">
                        </div>
                        <div class="col-md-3">
                            <label for="filterEmail" class="form-label small">Email</label>
                            <input type="text" class="form-control form-control-sm" id="filterEmail" name="email" value="<?= esc($filters['email'] ?? '') ?>" placeholder="Search Email">
                        </div>
                        <div class="col-md-3">
                            <label for="filterNationalId" class="form-label small">National ID</label>
                            <input type="text" class="form-control form-control-sm" id="filterNationalId" name="national_id" value="<?= esc($filters['national_id'] ?? '') ?>" placeholder="Search ID">
                        </div>
                        <div class="col-md-3 d-flex gap-2">
                            <button type="submit" class="btn btn-primary btn-sm flex-fill">Filter</button>
                            <?php if (!empty($filters['name']) || !empty($filters['email']) || !empty($filters['national_id'])): ?>
                                <a href="<?= current_url() ?>" class="btn btn-outline-secondary btn-sm flex-fill">Clear</a>
                            <?php endif; ?>
                        </div>
                    </form>

                    <?php if (!empty($applicants)): ?>
                        <div class="table-responsive">
                            <table class="table align-middle dashboard-table">
                                <thead class="table-light">
                                    <tr>
                                        <th>#</th>
                                        <th>Full Name</th>
                                        <th>Email</th>
                                        <th>National ID</th>
                                        <th>Registered On</th>
                                        <th>Last Login</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($applicants as $index => $applicant): ?>
                                        <tr>
                                            <td><?= esc(($applicantsPage - 1) * $applicantsPerPage + $index + 1) ?></td>
                                            <td>
                                                <a href="<?= base_url('admin/users/' . esc($applicant['uuid'])) ?>" class="text-decoration-none">
                                                    <?= esc(trim($applicant['first_name'] . ' ' . $applicant['last_name'])) ?>
                                                </a>
                                            </td>
                                            <td><?= esc($applicant['email']) ?></td>
                                            <td><?= esc($applicant['national_id'] ?? '-') ?></td>
                                            <td><?= esc(isset($applicant['created_at']) ? date('d M Y, H:i', strtotime($applicant['created_at'])) : '-') ?></td>
                                            <td><?= esc(!empty($applicant['last_login']) ? date('d M Y, H:i', strtotime($applicant['last_login'])) : '-') ?></td>
                                            <td>
                                                <?php if (!empty($applicant['active']) && $applicant['active'] == 1): ?>
                                                    <span class="badge bg-primary">Active</span>
                                                <?php else: ?>
                                                    <span class="badge bg-secondary">Inactive</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php if ($applicant['id'] != session()->get('user_id')): // prevent self-toggle ?>
                                                    <?php if (!empty($applicant['active']) && $applicant['active'] == 1): ?>
                                                        <a href="<?= base_url('admin/user-status/' . $applicant['id'] . '/deactivate') ?>" 
                                                           class="btn btn-sm btn-outline-primary"
                                                           onclick="return confirm('Are you sure you want to deactivate this user?');">
                                                            Deactivate
                                                        </a>
                                                    <?php else: ?>
                                                        <a href="<?= base_url('admin/user-status/' . $applicant['id'] . '/activate') ?>" 
                                                           class="btn btn-sm btn-outline-secondary"
                                                           onclick="return confirm('Are you sure you want to activate this user?');">
                                                            Activate
                                                        </a>
                                                    <?php endif; ?>
                                                    <a href="<?= base_url('admin/user-del/' . $applicant['id'] . '/delete') ?>" 
                                                       class="btn btn-sm btn-outline-danger"
                                                       onclick="return confirm('Are you sure you want to permanently delete this user Account');">
                                                        <i class="fas fa-trash-alt"></i>
                                                    </a>
                                                <?php else: ?>
                                                    <span class="text-muted small">N/A</span>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>

                            <!-- Pagination -->
                            <?php
                            $totalPages = ceil($applicantsTotal / $applicantsPerPage);
                            $current    = $applicantsPage;
                            $window     = 2;
                            ?>
                            <nav class="text-center">
                                <ul class="pagination pagination-sm justify-content-center p-2">

                                    <!-- Previous -->
                                    <li class="page-item <?= $current <= 1 ? 'disabled' : '' ?>">
                                        <a class="page-link" href="<?= current_url() ?>?<?= http_build_query(array_merge($filters, ['page' => $current - 1])) ?>">
                                            &laquo; Prev
                                        </a>
                                    </li>

                                    <!-- First page + ellipsis -->
                                    <?php if ($current > $window + 2): ?>
                                        <li class="page-item">
                                            <a class="page-link" href="<?= current_url() ?>?<?= http_build_query(array_merge($filters, ['page' => 1])) ?>">1</a>
                                        </li>
                                        <li class="page-item disabled"><span class="page-link">...</span></li>
                                    <?php endif; ?>

                                    <!-- Page window around current -->
                                    <?php
                                    $start = max(1, $current - $window);
                                    $end   = min($totalPages, $current + $window);
                                    for ($i = $start; $i <= $end; $i++): ?>
                                        <li class="page-item <?= $i == $current ? 'active' : '' ?>">
                                            <a class="page-link" href="<?= current_url() ?>?<?= http_build_query(array_merge($filters, ['page' => $i])) ?>">
                                                <?= $i ?>
                                            </a>
                                        </li>
                                    <?php endfor; ?>

                                    <!-- Last page + ellipsis -->
                                    <?php if ($current < $totalPages - $window - 1): ?>
                                        <li class="page-item disabled"><span class="page-link">...</span></li>
                                        <li class="page-item">
                                            <a class="page-link" href="<?= current_url() ?>?<?= http_build_query(array_merge($filters, ['page' => $totalPages])) ?>"><?= $totalPages ?></a>
                                        </li>
                                    <?php endif; ?>

                                    <!-- Next -->
                                    <li class="page-item <?= $current >= $totalPages ? 'disabled' : '' ?>">
                                        <a class="page-link" href="<?= current_url() ?>?<?= http_build_query(array_merge($filters, ['page' => $current + 1])) ?>">
                                            Next &raquo;
                                        </a>
                                    </li>

                                </ul>
                            </nav>

                        </div>
                    <?php else: ?>
                        <div class="text-center py-4">
                            <p class="text-muted mb-0">No Registrants found.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

    </div>

</div>

<!-- Custom CSS for smaller table fonts -->
<style>
    .dashboard-table {
        font-size: 0.85rem;
    }
    .dashboard-table th, 
    .dashboard-table td {
        padding: 0.35rem 0.5rem;
        vertical-align: middle;
    }
</style>

<?= $this->endSection() ?>