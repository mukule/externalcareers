<?= $this->extend('layouts/dashboard') ?>
<?= $this->section('content') ?>

<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mt-4 mb-3">
        <h1 class="mb-0"><?= esc($title ?? 'Job Vacancies') ?></h1>
        <a href="<?= base_url('admin/jobs/create') ?>" class="btn btn-primary">
            <i class="fas fa-plus me-1"></i> Add Job Vacancy
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
                                <th>Job Title</th>
                                <th>Job Type</th>
                                <th>Open Date</th>
                                <th>Close Date</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>

                        <tbody>
                            <?php foreach ($jobs as $index => $job): ?>
                                <tr>
                                    <td><?= $index + 1 ?></td>

                                   <td>
                                    <a href="<?= base_url('admin/jobs/' . $job['uuid']) ?>" 
                                    class="text-decoration-none">
                                        <?= esc($job['name']) ?> - <?= esc($job['reference_no']) ?>
                                    </a>
                                </td>


                                    <td><?= esc($job['job_type_name'] ?? 'N/A') ?></td>

                                    <td><?= esc($job['date_open']) ?></td>
                                    <td><?= esc($job['date_close']) ?></td>

                                    <!-- Job Status -->
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
                                        <span class="badge p-2 <?= $badgeClass ?>"><?= esc($status) ?></span>
                                    </td>

                                   <td class="text-center">

                                        
                                        <?php if ($job['active'] == 1): ?>
                                            <a href="<?= base_url('admin/jobs/toggle/' . $job['uuid']) ?>"
                                            class="btn btn-sm btn-outline-primary"
                                            data-bs-toggle="tooltip" 
                                            data-bs-placement="top" 
                                            title="Unpublish Job"
                                            onclick="return confirm('Unpublish this job?');">
                                                <i class="fas fa-eye-slash"></i>
                                            </a>
                                        <?php else: ?>
                                            <a href="<?= base_url('admin/jobs/toggle/' . $job['id']) ?>"
                                            class="btn btn-sm btn-outline-secondary"
                                            data-bs-toggle="tooltip" 
                                            data-bs-placement="top" 
                                            title="Publish Job"
                                            onclick="return confirm('Publish this job?');">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        <?php endif; ?>

                                        <!-- EDIT -->
                                        <a href="<?= base_url('admin/jobs/edit/' . $job['uuid']) ?>" 
                                        class="btn btn-sm btn-outline-primary"
                                        data-bs-toggle="tooltip" 
                                        data-bs-placement="top" 
                                        title="Edit Job">
                                            <i class="fas fa-edit"></i>
                                        </a>

                                    </td>

                                </tr>
                            <?php endforeach; ?>
                        </tbody>

                    </table>
                </div>

            <?php else: ?>
                <p class="text-muted text-center mb-0">No job vacancies found.</p>
            <?php endif; ?>

        </div>
    </div>
</div>

<?= $this->endSection() ?>
