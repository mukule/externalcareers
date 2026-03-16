<?= $this->extend('layouts/dashboard') ?>
<?= $this->section('content') ?>

<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mt-4 mb-3">
        <nav aria-label="breadcrumb" class="mt-4">
        <ol class="breadcrumb mb-3">
            <li class="breadcrumb-item">
                <a href="<?= base_url('admin') ?>">Dashboard</a>
            </li>
            <li class="breadcrumb-item active" aria-current="page">
                Applicants
            </li>
        </ol>
    </nav>
        <a href="<?= base_url('admin/jobs/create') ?>" class="btn btn-primary">
            <i class="fas fa-plus me-1"></i> Add Job
        </a>
    </div>
    <hr>

    <div class="card mb-4">
        <div class="card-body">
            <?php if (!empty($jobs) && count($jobs) > 0): ?>
                <div class="table-responsive">
                    <table id="datatablesSimple" class="table table-bordered table-striped align-middle">
                        <thead class="table-dark">
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
                                    <td><?= $index + 1 ?></td>
                                    <td>
                                        <a href="<?= base_url('admin/job-applications/' . $job['uuid']) ?>">
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
                                            $status = 'Upcoming';
                                            $badge = 'primary';
                                        } elseif ($today > $job['date_close']) {
                                            $status = 'Closed';
                                            $badge = 'info';
                                        } else {
                                            $status = 'Open';
                                            $badge = 'secondary';
                                        }
                                        ?>
                                        <span class="badge bg-<?= $badge ?>"><?= esc($status) ?></span>
                                    </td>
                                   
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <p class="text-muted text-center mb-0">No jobs found.</p>
            <?php endif; ?>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
