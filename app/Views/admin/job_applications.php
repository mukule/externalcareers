<?= $this->extend('layouts/dashboard') ?>
<?= $this->section('content') ?>

<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mt-4">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                    <a href="<?= base_url('admin') ?>">Dashboard</a>
                </li>
                <li class="breadcrumb-item">
                    <a href="<?= base_url('admin/jobs-applications') ?>">Applications</a>
                </li>
                <li class="breadcrumb-item active" aria-current="page">
                    <?= esc($title ?? '') ?>
                </li>
            </ol>
        </nav>
    </div>
    <hr>

    <!-- Single Card with All Filters -->
    <div class="card mb-4 shadow-sm border-0">
        <div class="card-body">
            <form method="get" class="row g-2 align-items-end">
                <div class="col-md-2">
                    <label for="user_name" class="form-label small">Name</label>
                    <input type="text" name="user_name" id="user_name" value="<?= esc($filters['user_name'] ?? '') ?>" class="form-control form-control-sm" placeholder="Search by name">
                </div>

                <div class="col-md-2">
                    <label for="email" class="form-label small">Email</label>
                    <input type="text" name="email" id="email" value="<?= esc($filters['email'] ?? '') ?>" class="form-control form-control-sm" placeholder="Search by email">
                </div>

                <div class="col-md-2">
                    <label for="national_id" class="form-label small">ID Number</label>
                    <input type="text" name="national_id" id="national_id" value="<?= esc($filters['national_id'] ?? '') ?>" class="form-control form-control-sm" placeholder="ID Number">
                </div>

                <div class="col-md-2">
                    <label for="gender" class="form-label small">Gender</label>
                    <select name="gender" id="gender" class="form-select form-select-sm">
                        <option value="">All</option>
                        <option value="Male" <?= isset($filters['gender']) && $filters['gender'] === 'Male' ? 'selected' : '' ?>>Male</option>
                        <option value="Female" <?= isset($filters['gender']) && $filters['gender'] === 'Female' ? 'selected' : '' ?>>Female</option>
                    </select>
                </div>

                <div class="col-md-2">
                    <label for="qualification" class="form-label small">Qualification</label>
                    <select name="qualification" id="qualification" class="form-select form-select-sm">
                        <option value="">All</option>
                        <option value="qualify" <?= isset($filters['qualification']) && $filters['qualification'] === 'qualify' ? 'selected' : '' ?>>Qualified</option>
                        <option value="not qualify" <?= isset($filters['qualification']) && $filters['qualification'] === 'not qualify' ? 'selected' : '' ?>>Not Qualified</option>
                    </select>
                </div>

                <div class="col-md-2">
                    <label for="job_ref" class="form-label small">Application Ref</label>
                    <input type="text" name="job_ref" id="job_ref" value="<?= esc($filters['job_ref'] ?? '') ?>" class="form-control form-control-sm" placeholder="Reference No">
                </div>

                <div class="col-md-2">
                    <label for="status" class="form-label small">Status</label>
                    <select name="status" id="status" class="form-select form-select-sm">
                        <option value="">All</option>
                        <option value="pending" <?= isset($filters['status']) && strtolower($filters['status']) === 'pending' ? 'selected' : '' ?>>Pending</option>
                        <option value="reviewed" <?= isset($filters['status']) && strtolower($filters['status']) === 'reviewed' ? 'selected' : '' ?>>Reviewed</option>
                        <option value="shortlisted" <?= isset($filters['status']) && strtolower($filters['status']) === 'shortlisted' ? 'selected' : '' ?>>Shortlisted</option>
                    </select>
                </div>

                <div class="col-md-12 col-lg-3 d-flex justify-content-end mt-2 gap-2">
                    <button type="submit" class="btn btn-primary btn-sm">Search</button>
                    <a href="<?= current_url() ?>" class="btn btn-secondary btn-sm">Clear</a>
                    <a href="<?= current_url() . '?' . http_build_query($filters) . '&export=csv' ?>" class="btn btn-outline-primary btn-sm">Export CSV</a>
                </div>
            </form>
        </div>
    </div>

    <!-- Applications Table -->
    <div class="card mb-4 shadow-sm border-0">
        <div class="card-body">
            <?php if (!empty($applications) && count($applications) > 0): ?>
                <div class="table-responsive">
                    <table class="table table-bordered table-striped align-middle table-sm">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Name</th>
                                <th>Date Applied</th>
                                <th>Reference No</th>
                                <th>Qualification</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($applications as $index => $app): ?>
                                <tr>
                                    <td><?= $index + 1 ?></td>
                                    <td>
                                        <a href="<?= base_url('applicant/profile-review/' . $app['user_uuid']) ?>">
                                            <?= esc($app['first_name'] ?? '-') ?> <?= esc($app['last_name'] ?? '-') ?>
                                        </a>
                                    </td>
                                    <td><?= date('d M, Y', strtotime($app['created_at'] ?? '')) ?></td>
                                    <td><?= esc($app['ref_no'] ?? '-') ?></td>
                                    <td>
                                        <span class="badge <?= $app['qualification'] === 'qualify' ? 'bg-success' : 'bg-secondary' ?>">
                                            <?= ucfirst($app['qualification'] ?? '-') ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php
                                        // Always show actual status from DB
                                        $status = strtolower($app['status']);
                                        $badge = match($status) {
                                            'pending'     => 'bg-warning',
                                            'reviewed'    => 'bg-info',
                                            'shortlisted' => 'bg-success',
                                            default       => 'bg-secondary'
                                        };
                                        ?>
                                        <span class="badge <?= $badge ?>"><?= esc(ucfirst($status)) ?></span>
                                    </td>
                                    <td>
                                        <form action="<?= route_to('admin.job-application.update-status', $app['id']) ?>" method="POST">
                                            <?= csrf_field() ?>
                                            <input type="hidden" name="status" value="<?= in_array($status, ['pending', 'reviewed']) ? 'shortlisted' : 'reviewed' ?>">
                                            <button type="submit" class="btn btn-sm <?= $status === 'shortlisted' ? 'btn-primary' : 'btn-success' ?> w-100">
                                                <?= $status === 'shortlisted' ? 'Unshortlist' : 'Shortlist' ?>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <p class="text-muted text-center mb-0">No applicants found.</p>
            <?php endif; ?>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
