<?= $this->extend('layouts/dashboard') ?>
<?= $this->section('content') ?>

<div class="container-fluid px-4 mt-2">

    <!-- Breadcrumb Card -->
    <div class="card mb-3 shadow-sm">
        <div class="card-body d-flex justify-content-between align-items-center">
            <nav aria-label="breadcrumb" class="mb-0">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="<?= base_url('admin') ?>">Dashboard</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Job Vacancies</li>
                </ol>
            </nav>
            <a href="<?= base_url('admin/jobs/create') ?>" class="btn btn-primary btn-sm">
                <i class="fas fa-plus me-1"></i> Add Job Vacancy
            </a>
        </div>
    </div>

    <!-- Combined Card: Filters + Jobs Table -->
    <div class="card mb-4 shadow-sm">
        <div class="card-body py-2">

            <!-- Filter Form -->
            <form method="get" class="row g-2 align-items-end mb-3">
                <div class="col-md-3">
                    <label for="filterName" class="form-label small">Job Name</label>
                    <input type="text" name="name" id="filterName" class="form-control form-control-sm" 
                           value="<?= esc($filters['name'] ?? '') ?>" placeholder="Search Job Name">
                </div>
                <div class="col-md-3">
                    <label for="filterRef" class="form-label small">Reference No</label>
                    <input type="text" name="ref_no" id="filterRef" class="form-control form-control-sm" 
                           value="<?= esc($filters['ref_no'] ?? '') ?>" placeholder="Search Ref No">
                </div>
                <div class="col-md-3">
                    <label for="filterType" class="form-label small">Job Type</label>
                    <select name="job_type_id" id="filterType" class="form-select form-select-sm">
                        <option value="">All</option>
                        <?php foreach ($jobTypes as $type): ?>
                            <option value="<?= esc($type['id']) ?>" <?= isset($filters['job_type_id']) && $filters['job_type_id'] == $type['id'] ? 'selected' : '' ?>>
                                <?= esc($type['display_name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="filterDiscipline" class="form-label small">Discipline</label>
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
                    <table class="table table-bordered table-striped align-middle dashboard-table">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Job Title</th>
                                <th>Type</th>
                                <th>Dates</th>
                                <th>Status</th>
                                <th class="text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($jobs as $index => $job): ?>
                                <tr>
                                    <td><?= ($currentPage - 1) * $perPage + $index + 1 ?></td>

                                    <td>
                                        <a href="<?= base_url('admin/jobs/' . $job['uuid']) ?>" class="text-primary text-decoration-none">
                                            <?= esc($job['name']) ?> - <?= esc($job['reference_no']) ?>
                                        </a>
                                    </td>

                                    <td><?= esc($job['job_type_name'] ?? 'N/A') ?></td>
                                    <td><?= esc($job['date_open']) ?> - <?= esc($job['date_close']) ?></td>

                                    <td class="text-center">
                                        <?php 
                                            $status = $job['status'] ?? 'Unknown';
                                            $badgeClass = match($status) {
                                                'Upcoming' => 'bg-info',
                                                'Open'     => 'bg-primary',
                                                'Closed'   => 'bg-secondary',
                                                default    => 'bg-secondary'
                                            };
                                        ?>
                                        <span class="badge p-1 <?= $badgeClass ?>"><?= esc($status) ?></span>
                                    </td>

                                    <td class="text-center">
                                        <?php if (!empty($job['active']) && $job['active'] == 1): ?>
                                            <a href="<?= base_url('admin/jobs/toggle/' . $job['uuid']) ?>"
                                               class="btn btn-sm btn-outline-primary"
                                               title="Unpublish Job"
                                               onclick="return confirm('Unpublish this job?');">
                                                <i class="fas fa-eye-slash"></i>
                                            </a>
                                        <?php else: ?>
                                            <a href="<?= base_url('admin/jobs/toggle/' . $job['uuid']) ?>"
                                               class="btn btn-sm btn-outline-secondary"
                                               title="Publish Job"
                                               onclick="return confirm('Publish this job?');">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        <?php endif; ?>

                                        <a href="<?= base_url('admin/jobs/edit/' . $job['uuid']) ?>" 
                                           class="btn btn-sm btn-outline-primary"
                                           title="Edit Job">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                    </td>

                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <?php if (!empty($pager) && $pager->getPageCount() > 1): ?>
                    <div class="mt-2 d-flex justify-content-center">
                        <?= $pager->links('default', 'bootstrap5') ?>
                    </div>
                <?php endif; ?>

            <?php else: ?>
                <p class="text-center text-muted mb-0">No job vacancies found.</p>
            <?php endif; ?>

        </div>
    </div>
</div>

<!-- Custom CSS -->
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