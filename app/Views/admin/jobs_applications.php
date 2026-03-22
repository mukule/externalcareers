<?= $this->extend('layouts/dashboard') ?>
<?= $this->section('content') ?>

<div class="container-fluid px-4 mt-2">

    <!-- Breadcrumb Card -->
    <div class="card mb-3 shadow-sm border-0">
        <div class="card-body d-flex justify-content-between align-items-center">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="<?= base_url('admin') ?>">Dashboard</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Jobs</li>
                </ol>
            </nav>
            <a href="<?= base_url('admin/jobs/create') ?>" class="btn btn-primary btn-sm">
                <i class="fas fa-plus me-1"></i> Add Job
            </a>
        </div>
    </div>

    <!-- Combined Filters + Jobs Table Card -->
    <div class="card mb-4 shadow-sm border-0">
        <div class="card-body py-2">

            <!-- Filter Form -->
            <form method="get" class="row g-2 align-items-end mb-3">
                <div class="col-md-3">
                    <label for="filterName" class="form-label small mb-0">Job Name</label>
                    <input type="text" class="form-control form-control-sm" id="filterName" name="name" 
                           value="<?= esc($filters['name'] ?? '') ?>" placeholder="Search Job Name">
                </div>
                <div class="col-md-3">
                    <label for="filterRef" class="form-label small mb-0">Reference No</label>
                    <input type="text" class="form-control form-control-sm" id="filterRef" name="ref_no" 
                           value="<?= esc($filters['ref_no'] ?? '') ?>" placeholder="Search Ref No">
                </div>
                <div class="col-md-3">
                    <label for="filterType" class="form-label small mb-0">Job Type</label>
                    <select name="job_type_id" id="filterType" class="form-select form-select-sm">
                        <option value="">All</option>
                        <?php foreach ($jobTypes as $type): ?>
                            <option value="<?= esc($type['id']) ?>" <?= isset($filters['job_type_id']) && $filters['job_type_id'] == $type['id'] ? 'selected' : '' ?>>
                                <?= esc($type['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="filterDiscipline" class="form-label small mb-0">Discipline</label>
                    <select name="discipline_id" id="filterDiscipline" class="form-select form-select-sm">
                        <option value="">All</option>
                        <?php foreach ($disciplines as $discipline): ?>
                            <option value="<?= esc($discipline['id']) ?>" <?= isset($filters['discipline_id']) && $filters['discipline_id'] == $discipline['id'] ? 'selected' : '' ?>>
                                <?= esc($discipline['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-md-12 d-flex gap-2 mt-2">
                    <button type="submit" class="btn btn-primary btn-sm">Filter</button>
                    <?php if (!empty($filters)): ?>
                        <a href="<?= current_url() ?>" class="btn btn-outline-secondary btn-sm">Clear</a>
                    <?php endif; ?>
                </div>
            </form>

            <!-- Jobs Table -->
            <?php if (!empty($jobs)): ?>
                <div class="table-responsive">
                    <table class="table table-bordered table-striped align-middle dashboard-table mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Name</th>
                                <th>Reference No</th>
                                <th>Created On</th>
                                <th>Date Open</th>
                                <th>Date Close</th>
                                <th>Applications</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($jobs as $index => $job): ?>
                                <tr>
                                    <td><?= ($currentPage - 1) * $perPage + $index + 1 ?></td>
                                    <td>
                                        <a href="<?= base_url('admin/job-applications/' . $job['uuid']) ?>" 
                                           style="all: unset; cursor: pointer; color: var(--bs-primary);">
                                            <?= esc($job['name']) ?>
                                        </a>
                                    </td>
                                    <td><?= esc($job['reference_no']) ?></td>
                                    <td><?= date('d M, Y', strtotime($job['created_at'])) ?></td>
                                    <td><?= date('d M, Y', strtotime($job['date_open'])) ?></td>
                                    <td><?= date('d M, Y', strtotime($job['date_close'])) ?></td>
                                    <td class="text-center">
                                        <span class="badge bg-primary"><?= esc($job['applications_count'] ?? 0) ?></span>
                                    </td>
                                    <td>
                                        <?php
                                            $today = date('Y-m-d');
                                            if ($today < $job['date_open']) {
                                                $status = 'Upcoming'; $badge = 'primary';
                                            } elseif ($today > $job['date_close']) {
                                                $status = 'Closed'; $badge = 'primary';
                                            } else {
                                                $status = 'Open'; $badge = 'secondary';
                                            }
                                        ?>
                                        <span class="badge bg-<?= $badge ?>"><?= esc($status) ?></span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <?php if ($totalPages > 1): ?>
                    <nav class="mt-2 text-center">
                        <ul class="pagination pagination-sm justify-content-center mb-0">
                            <li class="page-item <?= $currentPage <= 1 ? 'disabled' : '' ?>">
                                <a class="page-link" href="<?= current_url() ?>?<?= http_build_query(array_merge($filters, ['page' => $currentPage - 1])) ?>">&laquo; Prev</a>
                            </li>
                            <?php
                                $window = 2;
                                $start = max(1, $currentPage - $window);
                                $end   = min($totalPages, $currentPage + $window);
                                for ($i = $start; $i <= $end; $i++): ?>
                                <li class="page-item <?= $i == $currentPage ? 'active' : '' ?>">
                                    <a class="page-link" href="<?= current_url() ?>?<?= http_build_query(array_merge($filters, ['page' => $i])) ?>">
                                        <?= $i ?>
                                    </a>
                                </li>
                            <?php endfor; ?>
                            <li class="page-item <?= $currentPage >= $totalPages ? 'disabled' : '' ?>">
                                <a class="page-link" href="<?= current_url() ?>?<?= http_build_query(array_merge($filters, ['page' => $currentPage + 1])) ?>">Next &raquo;</a>
                            </li>
                        </ul>
                    </nav>
                <?php endif; ?>

            <?php else: ?>
                <p class="text-center text-muted mb-0">No jobs found.</p>
            <?php endif; ?>

        </div>
    </div>
</div>

<!-- Custom CSS -->
<style>
    .dashboard-table {
        font-size: 0.85rem;
    }
    .dashboard-table th, .dashboard-table td {
        padding: 0.35rem 0.5rem;
        vertical-align: middle;
    }
    .card .form-label.small {
        font-size: 0.8rem;
    }
</style>

<?= $this->endSection() ?>